#pragma once

#include <QObject>
#include <QQmlEngine>
#include "../models/UserModel.h"
#include "../api/ApiClient.h"

class AuthStore : public QObject
{
    Q_OBJECT
    QML_ELEMENT
    QML_SINGLETON

    Q_PROPERTY(bool authenticated READ isAuthenticated NOTIFY authChanged)
    Q_PROPERTY(bool loading READ isLoading NOTIFY loadingChanged)
    Q_PROPERTY(QString firstName READ firstName NOTIFY authChanged)
    Q_PROPERTY(QString role READ role NOTIFY authChanged)
    Q_PROPERTY(bool canEditCourses READ canEditCourses NOTIFY authChanged)
    Q_PROPERTY(bool canEditUsers READ canEditUsers NOTIFY authChanged)
    Q_PROPERTY(bool canAssistLessons READ canAssistLessons NOTIFY authChanged)
    Q_PROPERTY(QVariant karma READ karma NOTIFY authChanged)
    Q_PROPERTY(int openPayment READ openPayment NOTIFY authChanged)
    Q_PROPERTY(int userId READ userId NOTIFY authChanged)

public:
    explicit AuthStore(QObject *parent = nullptr);

    bool isAuthenticated() const { return m_authenticated; }
    bool isLoading()       const { return m_loading; }
    QString firstName()    const { return m_user.firstName; }
    QString role()         const { return m_user.role; }
    bool canEditCourses()  const { return m_user.canEditCourses(); }
    bool canEditUsers()    const { return m_user.canEditUsers(); }
    bool canAssistLessons() const { return m_user.canAssistLessons(); }
    QVariant karma()       const { return m_user.karma; }
    int openPayment()      const { return m_user.openPayment; }
    int userId()           const { return m_user.id; }
    UserModel currentUser() const { return m_user; }

    Q_INVOKABLE void login(const QString &email, const QString &password);
    Q_INVOKABLE void logout();
    void restoreSession();

signals:
    void authChanged();
    void loadingChanged();
    void loginFailed(QString error);
    void sessionRestored();
    void sessionExpired();
    void tokenChanged(QString accessToken);  // emitted whenever token is set/cleared

private:
    ApiClient   *m_api;
    UserModel    m_user;
    QString      m_accessToken;
    QString      m_refreshToken;
    qint64       m_tokenExpiresAt = 0;  // Unix timestamp
    bool         m_authenticated = false;
    bool         m_loading = false;

    void saveTokens(const QString &access, const QString &refresh, int expiresIn);
    void clearTokens();
    void loadStoredTokens();
    void fetchCurrentUser();
    bool isTokenExpiringSoon() const;

    static QString localStorageGet(const QString &key);
    static void localStorageSet(const QString &key, const QString &value);
    static void localStorageRemove(const QString &key);
};
