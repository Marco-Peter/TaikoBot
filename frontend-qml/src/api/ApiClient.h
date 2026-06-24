#pragma once

#include <QObject>
#include <QNetworkAccessManager>
#include <QNetworkRequest>
#include <QNetworkReply>
#include <QJsonObject>
#include <QJsonDocument>
#include <QQmlEngine>
#include <functional>

class ApiClient : public QObject
{
    Q_OBJECT
    QML_ELEMENT
    QML_SINGLETON

public:
    explicit ApiClient(QObject *parent = nullptr);

    using SuccessCallback = std::function<void(const QJsonObject &)>;
    using ErrorCallback   = std::function<void(int httpStatus, const QString &message)>;

    void get(const QString &path, SuccessCallback onSuccess, ErrorCallback onError = nullptr);
    void post(const QString &path, const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError = nullptr);
    void put(const QString &path, const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError = nullptr);
    void del(const QString &path, SuccessCallback onSuccess, ErrorCallback onError = nullptr);

    void setToken(const QString &token);
    void clearToken();
    bool hasToken() const;

    // OAuth token endpoint (not /api/v1, so handled separately)
    void fetchToken(const QString &email, const QString &password,
                    std::function<void(const QString &accessToken, const QString &refreshToken, int expiresIn)> onSuccess,
                    ErrorCallback onError);
    void refreshToken(const QString &refreshToken,
                      std::function<void(const QString &accessToken, const QString &newRefreshToken, int expiresIn)> onSuccess,
                      ErrorCallback onError);

signals:
    void unauthorised();
    void networkError(int status, QString message);

private:
    QNetworkAccessManager m_nam;
    QString m_token;
    QString m_baseUrl;

    QNetworkRequest makeRequest(const QString &path) const;
    void handleReply(QNetworkReply *reply, SuccessCallback onSuccess, ErrorCallback onError);
    void sendJsonRequest(const QByteArray &method, const QString &path,
                         const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError);
};
