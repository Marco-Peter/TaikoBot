#include "ApiClient.h"
#include "AppConfig.h"

#include <QJsonArray>
#include <QUrlQuery>
#include <QDebug>

ApiClient::ApiClient(QObject *parent)
    : QObject(parent)
    , m_baseUrl(AppConfig::apiBaseUrl)
{
}

void ApiClient::setToken(const QString &token)
{
    m_token = token;
}

void ApiClient::clearToken()
{
    m_token.clear();
}

bool ApiClient::hasToken() const
{
    return !m_token.isEmpty();
}

QNetworkRequest ApiClient::makeRequest(const QString &path) const
{
    QNetworkRequest req;
    req.setUrl(QUrl(m_baseUrl + "/api/v1" + path));
    req.setHeader(QNetworkRequest::ContentTypeHeader, "application/json");
    req.setRawHeader("Accept", "application/json");
    if (!m_token.isEmpty())
        req.setRawHeader("Authorization", ("Bearer " + m_token).toUtf8());
    return req;
}

void ApiClient::handleReply(QNetworkReply *reply, SuccessCallback onSuccess, ErrorCallback onError)
{
    connect(reply, &QNetworkReply::finished, this, [=]() {
        reply->deleteLater();
        int status = reply->attribute(QNetworkRequest::HttpStatusCodeAttribute).toInt();
        QByteArray data = reply->readAll();

        if (reply->error() == QNetworkReply::NoError || (status >= 200 && status < 300)) {
            QJsonObject obj;
            if (!data.isEmpty()) {
                auto doc = QJsonDocument::fromJson(data);
                if (doc.isObject()) obj = doc.object();
            }
            if (onSuccess) onSuccess(obj);
            return;
        }

        if (status == 401) {
            emit unauthorised();
            return;
        }

        QString errorMsg;
        auto doc = QJsonDocument::fromJson(data);
        if (doc.isObject() && doc.object().contains("message"))
            errorMsg = doc.object()["message"].toString();
        else
            errorMsg = reply->errorString();

        qWarning() << "API error" << status << errorMsg;
        emit networkError(status, errorMsg);
        if (onError) onError(status, errorMsg);
    });
}

void ApiClient::sendJsonRequest(const QByteArray &method, const QString &path,
                                const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError)
{
    auto req = makeRequest(path);
    QByteArray payload = QJsonDocument(body).toJson(QJsonDocument::Compact);
    QNetworkReply *reply = m_nam.sendCustomRequest(req, method, payload);
    handleReply(reply, onSuccess, onError);
}

void ApiClient::get(const QString &path, SuccessCallback onSuccess, ErrorCallback onError)
{
    auto req = makeRequest(path);
    QNetworkReply *reply = m_nam.get(req);
    handleReply(reply, onSuccess, onError);
}

void ApiClient::post(const QString &path, const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError)
{
    sendJsonRequest("POST", path, body, onSuccess, onError);
}

void ApiClient::put(const QString &path, const QJsonObject &body, SuccessCallback onSuccess, ErrorCallback onError)
{
    sendJsonRequest("PUT", path, body, onSuccess, onError);
}

void ApiClient::del(const QString &path, SuccessCallback onSuccess, ErrorCallback onError)
{
    sendJsonRequest("DELETE", path, {}, onSuccess, onError);
}

void ApiClient::fetchToken(const QString &email, const QString &password,
                           std::function<void(const QString &, const QString &, int)> onSuccess,
                           ErrorCallback onError)
{
    QNetworkRequest req;
    req.setUrl(QUrl(QString(AppConfig::oauthTokenEndpoint)));
    req.setHeader(QNetworkRequest::ContentTypeHeader, "application/json");
    req.setRawHeader("Accept", "application/json");

    QJsonObject body{
        {"grant_type",     "password"},
        {"client_id",      AppConfig::oauthClientId},
        {"client_secret",  AppConfig::oauthClientSecret},
        {"username",       email},
        {"password",       password},
        {"scope",          "read edit-courses assist-lessons lessons:sign"},
    };

    QNetworkReply *reply = m_nam.post(req, QJsonDocument(body).toJson(QJsonDocument::Compact));
    connect(reply, &QNetworkReply::finished, this, [=]() {
        reply->deleteLater();
        int status = reply->attribute(QNetworkRequest::HttpStatusCodeAttribute).toInt();
        auto doc = QJsonDocument::fromJson(reply->readAll());
        if (status == 200 && doc.isObject()) {
            auto obj = doc.object();
            onSuccess(obj["access_token"].toString(),
                      obj["refresh_token"].toString(),
                      obj["expires_in"].toInt());
        } else {
            QString msg = doc.isObject() ? doc.object()["message"].toString() : reply->errorString();
            if (onError) onError(status, msg);
        }
    });
}

void ApiClient::refreshToken(const QString &refreshToken,
                             std::function<void(const QString &, const QString &, int)> onSuccess,
                             ErrorCallback onError)
{
    QNetworkRequest req;
    req.setUrl(QUrl(QString(AppConfig::oauthTokenEndpoint)));
    req.setHeader(QNetworkRequest::ContentTypeHeader, "application/json");
    req.setRawHeader("Accept", "application/json");

    QJsonObject body{
        {"grant_type",     "refresh_token"},
        {"client_id",      AppConfig::oauthClientId},
        {"client_secret",  AppConfig::oauthClientSecret},
        {"refresh_token",  refreshToken},
        {"scope",          "read edit-courses assist-lessons lessons:sign"},
    };

    QNetworkReply *reply = m_nam.post(req, QJsonDocument(body).toJson(QJsonDocument::Compact));
    connect(reply, &QNetworkReply::finished, this, [=]() {
        reply->deleteLater();
        int status = reply->attribute(QNetworkRequest::HttpStatusCodeAttribute).toInt();
        auto doc = QJsonDocument::fromJson(reply->readAll());
        if (status == 200 && doc.isObject()) {
            auto obj = doc.object();
            onSuccess(obj["access_token"].toString(),
                      obj["refresh_token"].toString(),
                      obj["expires_in"].toInt());
        } else {
            QString msg = doc.isObject() ? doc.object()["message"].toString() : reply->errorString();
            if (onError) onError(status, msg);
        }
    });
}
