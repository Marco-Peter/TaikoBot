#pragma once

#include <QObject>
#include <QVariantList>
#include <QVariantMap>
#include <QQmlEngine>

#include "../api/ApiClient.h"
#include "../storage/LocalDatabase.h"
#include "../storage/CacheManager.h"

class UserStore : public QObject
{
    Q_OBJECT
    QML_ELEMENT

    Q_PROPERTY(QVariantList users READ users NOTIFY usersChanged)
    Q_PROPERTY(QVariantMap currentUser READ currentUser NOTIFY currentUserChanged)
    Q_PROPERTY(bool loading READ isLoading NOTIFY loadingChanged)
    Q_PROPERTY(QString error READ error NOTIFY errorChanged)

public:
    explicit UserStore(ApiClient *api, LocalDatabase *db, CacheManager *cache,
                       QObject *parent = nullptr);

    QVariantList users()       const { return m_users; }
    QVariantMap  currentUser() const { return m_currentUser; }
    bool         isLoading()   const { return m_loading; }
    QString      error()       const { return m_error; }

    Q_INVOKABLE void loadUsers();
    Q_INVOKABLE void loadUser(int userId);
    Q_INVOKABLE void updateUser(int userId, const QVariantMap &data);
    Q_INVOKABLE void addPayment(int userId, int amount, const QString &notes);
    Q_INVOKABLE void updateSettings(const QVariantMap &settings);

signals:
    void usersChanged();
    void currentUserChanged();
    void loadingChanged();
    void errorChanged();
    void settingsSaved();

private:
    ApiClient    *m_api;
    LocalDatabase *m_db;
    CacheManager  *m_cache;

    QVariantList m_users;
    QVariantMap  m_currentUser;
    bool         m_loading = false;
    QString      m_error;

    void setLoading(bool v);
    void setError(const QString &e);
};
