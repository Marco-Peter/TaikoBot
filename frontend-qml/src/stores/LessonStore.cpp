#include "LessonStore.h"

#include <QJsonArray>
#include <QJsonDocument>
#include <QDebug>

LessonStore::LessonStore(ApiClient *api, LocalDatabase *db, CacheManager *cache,
                         PendingOpQueue *queue, SyncEngine *sync, QObject *parent)
    : QObject(parent)
    , m_api(api)
    , m_db(db)
    , m_cache(cache)
    , m_queue(queue)
    , m_sync(sync)
{}

// ── Helpers ───────────────────────────────────────────────────────────────────

void LessonStore::setLoading(bool v)
{
    if (m_loading == v) return;
    m_loading = v;
    emit loadingChanged();
}

void LessonStore::setError(const QString &e)
{
    m_error = e;
    emit errorChanged();
}

void LessonStore::reloadLesson(int lessonId)
{
    m_cache->invalidate("lesson/" + QString::number(lessonId));
    loadLesson(lessonId);
}

// ── Dashboard ─────────────────────────────────────────────────────────────────

void LessonStore::loadDashboard()
{
    // Serve stale cache immediately
    const QJsonObject cached = m_db->loadDashboard();
    if (!cached.isEmpty()) {
        const QJsonArray arr = cached["upcoming_lessons"].toArray();
        m_dashboardLessons.clear();
        for (const auto &v : arr)
            m_dashboardLessons.append(v.toObject().toVariantMap());
        emit dashboardLessonsChanged();
    }

    if (m_cache->isFresh("dashboard") && !cached.isEmpty()) return;

    m_dashboardLoading = true;
    emit dashboardLoadingChanged();

    m_api->get("/dashboard",
        [this](const QJsonObject &root) {
            const QJsonObject data = root["data"].toObject();
            m_db->saveDashboard(data);
            m_cache->markFresh("dashboard");

            const QJsonArray arr = data["upcoming_lessons"].toArray();
            m_dashboardLessons.clear();
            for (const auto &v : arr)
                m_dashboardLessons.append(v.toObject().toVariantMap());
            emit dashboardLessonsChanged();

            m_dashboardLoading = false;
            emit dashboardLoadingChanged();
        },
        [this](int, const QString &msg) {
            setError(msg);
            m_dashboardLoading = false;
            emit dashboardLoadingChanged();
        }
    );
}

// ── Load lesson ───────────────────────────────────────────────────────────────

void LessonStore::loadLesson(int lessonId)
{
    // Serve stale cache immediately
    const QJsonObject cached = m_db->loadLesson(lessonId);
    if (!cached.isEmpty()) {
        m_currentLesson = cached.toVariantMap();
        emit currentLessonChanged();

        const QJsonArray cachedParts = m_db->loadParticipants(lessonId);
        QVariantList list;
        for (const auto &v : cachedParts)
            list.append(v.toObject().toVariantMap());
        m_participants = list;
        emit participantsChanged();
    }

    const QString key = "lesson/" + QString::number(lessonId);
    if (m_cache->isFresh(key) && !cached.isEmpty()) return;

    setLoading(true);
    m_api->get("/lessons/" + QString::number(lessonId),
        [this, lessonId, key](const QJsonObject &root) {
            const QJsonObject obj = root["data"].toObject();
            m_db->saveLesson(obj);
            m_cache->markFresh(key);
            m_currentLesson = obj.toVariantMap();
            emit currentLessonChanged();

            // Extract + cache participants
            const QJsonArray partsArr = obj["participants"].toObject()["data"].toArray();
            m_db->saveParticipants(lessonId, partsArr);
            QVariantList list;
            for (const auto &v : partsArr)
                list.append(v.toObject().toVariantMap());
            m_participants = list;
            emit participantsChanged();

            setLoading(false);
        },
        [this](int, const QString &msg) {
            setError(msg);
            setLoading(false);
        }
    );
}

// ── CRUD ──────────────────────────────────────────────────────────────────────

void LessonStore::createLesson(const QVariantMap &data)
{
    setLoading(true);
    m_api->post("/lessons", QJsonObject::fromVariantMap(data),
        [this](const QJsonObject &root) {
            m_currentLesson = root["data"].toObject().toVariantMap();
            emit currentLessonChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::updateLesson(int lessonId, const QVariantMap &data)
{
    setLoading(true);
    m_api->put("/lessons/" + QString::number(lessonId), QJsonObject::fromVariantMap(data),
        [this, lessonId](const QJsonObject &root) {
            const QJsonObject obj = root["data"].toObject();
            m_db->saveLesson(obj);
            m_cache->invalidate("lesson/" + QString::number(lessonId));
            m_currentLesson = obj.toVariantMap();
            emit currentLessonChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::deleteLesson(int lessonId)
{
    setLoading(true);
    m_api->del("/lessons/" + QString::number(lessonId),
        [this, lessonId](const QJsonObject &) {
            m_cache->invalidate("lesson/" + QString::number(lessonId));
            emit lessonDeleted(lessonId);
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

// ── Participation actions ─────────────────────────────────────────────────────

void LessonStore::signIn(int lessonId, const QString &message)
{
    setLoading(true);
    QJsonObject body;
    if (!message.isEmpty()) body["message"] = message;

    m_api->post("/lessons/" + QString::number(lessonId) + "/signin", body,
        [this, lessonId](const QJsonObject &) {
            emit participationChanged(lessonId);
            reloadLesson(lessonId);
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::signOut(int lessonId)
{
    setLoading(true);
    m_api->post("/lessons/" + QString::number(lessonId) + "/signout", {},
        [this, lessonId](const QJsonObject &) {
            emit participationChanged(lessonId);
            reloadLesson(lessonId);
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::compensate(int lessonId, const QString &message)
{
    setLoading(true);
    QJsonObject body;
    if (!message.isEmpty()) body["message"] = message;

    m_api->post("/lessons/" + QString::number(lessonId) + "/compensate", body,
        [this, lessonId](const QJsonObject &) {
            emit participationChanged(lessonId);
            reloadLesson(lessonId);
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::assist(int lessonId)
{
    setLoading(true);
    m_api->post("/lessons/" + QString::number(lessonId) + "/assist", {},
        [this, lessonId](const QJsonObject &) {
            emit participationChanged(lessonId);
            reloadLesson(lessonId);
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void LessonStore::sendMessage(int lessonId, const QString &message)
{
    m_api->post("/lessons/" + QString::number(lessonId) + "/message",
               {{"message", message}}, nullptr);
}

// ── Attendance (offline-safe for teachers) ────────────────────────────────────

void LessonStore::setAttendance(int lessonId, int userId, const QString &participation)
{
    // Optimistic cache update
    m_db->updateParticipant(lessonId, userId, participation);

    const QString endpoint = "/lessons/" + QString::number(lessonId)
                           + "/participants/" + QString::number(userId) + "/attendance";
    const QJsonObject payload{{"participation", participation}};

    if (!m_sync->isOnline()) {
        m_queue->enqueue("PUT", endpoint, payload);
        // Refresh participant list from cache
        const QJsonArray parts = m_db->loadParticipants(lessonId);
        QVariantList list;
        for (const auto &v : parts)
            list.append(v.toObject().toVariantMap());
        m_participants = list;
        emit participantsChanged();
        return;
    }

    m_api->put(endpoint, payload,
        [this, lessonId](const QJsonObject &) {
            reloadLesson(lessonId);
        },
        [this](int, const QString &msg) { setError(msg); }
    );
}

// ── Teacher management ────────────────────────────────────────────────────────

void LessonStore::addTeacher(int lessonId, int userId)
{
    m_api->post("/lessons/" + QString::number(lessonId) + "/teachers",
               {{"user_id", userId}},
        [this, lessonId](const QJsonObject &) { reloadLesson(lessonId); },
        [this](int, const QString &msg) { setError(msg); }
    );
}

void LessonStore::removeTeacher(int lessonId, int userId)
{
    m_api->del("/lessons/" + QString::number(lessonId) + "/teachers/" + QString::number(userId),
        [this, lessonId](const QJsonObject &) { reloadLesson(lessonId); },
        [this](int, const QString &msg) { setError(msg); }
    );
}
