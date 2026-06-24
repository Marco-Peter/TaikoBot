#include "UserStore.h"

#include <QJsonArray>
#include <QDebug>

UserStore::UserStore(ApiClient *api, LocalDatabase *db, CacheManager *cache, QObject *parent)
    : QObject(parent)
    , m_api(api)
    , m_db(db)
    , m_cache(cache)
{}

void UserStore::setLoading(bool v)
{
    if (m_loading == v) return;
    m_loading = v;
    emit loadingChanged();
}

void UserStore::setError(const QString &e)
{
    m_error = e;
    emit errorChanged();
}

void UserStore::loadUsers()
{
    setLoading(true);
    m_api->get("/users",
        [this](const QJsonObject &root) {
            const QJsonArray arr = root["data"].toArray();
            m_users.clear();
            for (const auto &item : arr)
                m_users.append(item.toObject().toVariantMap());
            emit usersChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void UserStore::loadUser(int userId)
{
    setLoading(true);
    m_api->get("/users/" + QString::number(userId),
        [this](const QJsonObject &root) {
            m_currentUser = root["data"].toObject().toVariantMap();
            emit currentUserChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void UserStore::updateUser(int userId, const QVariantMap &data)
{
    setLoading(true);
    m_api->put("/users/" + QString::number(userId), QJsonObject::fromVariantMap(data),
        [this](const QJsonObject &root) {
            m_currentUser = root["data"].toObject().toVariantMap();
            emit currentUserChanged();
            setLoading(false);
        },
        [this](int, const QString &msg) { setError(msg); setLoading(false); }
    );
}

void UserStore::addPayment(int userId, int amount, const QString &notes)
{
    m_api->post("/users/" + QString::number(userId) + "/payment",
               {{"amount", amount}, {"notes", notes}},
        [this, userId](const QJsonObject &) { loadUser(userId); },
        [this](int, const QString &msg) { setError(msg); }
    );
}

void UserStore::updateSettings(const QVariantMap &settings)
{
    m_api->put("/user/settings", QJsonObject::fromVariantMap(settings),
        [this](const QJsonObject &) { emit settingsSaved(); },
        [this](int, const QString &msg) { setError(msg); }
    );
}
