#include "CourseStore.h"

#include <QJsonArray>
#include <QJsonDocument>
#include <QDebug>

CourseStore::CourseStore(ApiClient *api, LocalDatabase *db, CacheManager *cache, QObject *parent)
    : QObject(parent)
    , m_api(api)
    , m_db(db)
    , m_cache(cache)
{}

// ── Helpers ───────────────────────────────────────────────────────────────────

void CourseStore::setLoading(bool v)
{
    if (m_loading == v) return;
    m_loading = v;
    emit loadingChanged();
}

void CourseStore::setError(const QString &e)
{
    m_error = e;
    emit errorChanged();
}

QVariantMap CourseStore::jsonToVariant(const QJsonObject &obj)
{
    return obj.toVariantMap();
}

// ── Load courses list ─────────────────────────────────────────────────────────

void CourseStore::loadCourses()
{
    // Serve cache immediately (stale-while-revalidate)
    const auto cached = m_db->loadCourses();
    if (!cached.isEmpty()) {
        QVariantList list;
        for (const auto &c : cached)
            list.append(c.toVariantMap());
        m_courses = list;
        emit coursesChanged();
    }

    if (m_cache->isFresh("courses") && !cached.isEmpty()) return;

    setLoading(true);
    m_api->get("/courses",
        [this](const QJsonObject &root) {
            const QJsonArray arr = root["data"].toArray();
            m_courses.clear();
            for (const auto &item : arr) {
                const QJsonObject obj = item.toObject();
                m_db->saveCourse(obj);
                m_courses.append(jsonToVariant(obj));
            }
            m_cache->markFresh("courses");
            emit coursesChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

// ── Load single course ────────────────────────────────────────────────────────

void CourseStore::loadCourse(int courseId)
{
    const QJsonObject cached = m_db->loadCourse(courseId);
    if (!cached.isEmpty()) {
        m_currentCourse = cached.toVariantMap();
        emit currentCourseChanged();
    }

    const QString key = "course/" + QString::number(courseId);
    if (m_cache->isFresh(key) && !cached.isEmpty()) return;

    setLoading(true);
    m_api->get("/courses/" + QString::number(courseId),
        [this, courseId, key](const QJsonObject &root) {
            const QJsonObject obj = root["data"].toObject();
            m_db->saveCourse(obj);
            m_cache->markFresh(key);
            m_currentCourse = obj.toVariantMap();
            emit currentCourseChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

// ── Create / Update / Delete ──────────────────────────────────────────────────

void CourseStore::createCourse(const QVariantMap &data)
{
    setLoading(true);
    const QJsonObject body = QJsonObject::fromVariantMap(data);
    m_api->post("/courses", body,
        [this](const QJsonObject &root) {
            m_cache->invalidate("courses");
            m_currentCourse = root["data"].toObject().toVariantMap();
            emit currentCourseChanged();
            loadCourses();
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

void CourseStore::updateCourse(int courseId, const QVariantMap &data)
{
    setLoading(true);
    const QJsonObject body = QJsonObject::fromVariantMap(data);
    m_api->put("/courses/" + QString::number(courseId), body,
        [this, courseId](const QJsonObject &root) {
            const QJsonObject obj = root["data"].toObject();
            m_db->saveCourse(obj);
            m_cache->invalidate("course/" + QString::number(courseId));
            m_cache->invalidate("courses");
            m_currentCourse = obj.toVariantMap();
            emit currentCourseChanged();
            emit courseUpdated(courseId);
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

void CourseStore::deleteCourse(int courseId)
{
    setLoading(true);
    m_api->del("/courses/" + QString::number(courseId),
        [this, courseId](const QJsonObject &) {
            m_cache->invalidate("courses");
            m_cache->invalidate("course/" + QString::number(courseId));
            emit courseDeleted(courseId);
            loadCourses();
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

// ── Signup ────────────────────────────────────────────────────────────────────

void CourseStore::signup(int courseId)
{
    setLoading(true);
    m_api->post("/courses/" + QString::number(courseId) + "/signup", {},
        [this, courseId](const QJsonObject &) {
            m_cache->invalidate("course/" + QString::number(courseId));
            loadCourse(courseId);
            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

void CourseStore::markPaid(int courseId, int userId)
{
    const QString path = "/courses/" + QString::number(courseId)
                       + "/participants/" + QString::number(userId) + "/paid";
    m_api->put(path, {},
        [this, courseId](const QJsonObject &) {
            m_cache->invalidate("course/" + QString::number(courseId));
            loadCourse(courseId);
        },
        [this](int, const QString &msg) { setError(msg); }
    );
}
