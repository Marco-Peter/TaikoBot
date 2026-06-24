#include "AuthStore.h"

#include <QDateTime>
#include <QDebug>

#ifdef Q_OS_WASM
#include <emscripten/val.h>
#endif

AuthStore::AuthStore(QObject *parent)
    : QObject(parent)
    , m_api(new ApiClient(this))
{
    connect(m_api, &ApiClient::unauthorised, this, [this]() {
        // Try refresh first, then give up
        if (!m_refreshToken.isEmpty()) {
            m_api->refreshToken(m_refreshToken,
                [this](const QString &access, const QString &refresh, int expiresIn) {
                    saveTokens(access, refresh, expiresIn);
                    m_api->setToken(access);
                },
                [this](int, const QString &) {
                    clearTokens();
                    emit sessionExpired();
                }
            );
        } else {
            clearTokens();
            emit sessionExpired();
        }
    });
}

void AuthStore::login(const QString &email, const QString &password)
{
    m_loading = true;
    emit loadingChanged();

    m_api->fetchToken(email, password,
        [this](const QString &access, const QString &refresh, int expiresIn) {
            saveTokens(access, refresh, expiresIn);
            m_api->setToken(access);
            fetchCurrentUser();
        },
        [this](int, const QString &msg) {
            m_loading = false;
            emit loadingChanged();
            emit loginFailed(msg);
        }
    );
}

void AuthStore::logout()
{
    m_api->post("/auth/logout", {}, nullptr);
    clearTokens();
    m_user = {};
    m_authenticated = false;
    emit authChanged();
}

void AuthStore::restoreSession()
{
    loadStoredTokens();
    if (m_accessToken.isEmpty()) {
        emit sessionExpired();
        return;
    }
    m_api->setToken(m_accessToken);
    fetchCurrentUser();
}

void AuthStore::fetchCurrentUser()
{
    m_loading = true;
    emit loadingChanged();

    m_api->get("/auth/user",
        [this](const QJsonObject &obj) {
            m_user = UserModel::fromJson(obj["data"].toObject());
            m_authenticated = true;
            m_loading = false;
            emit authChanged();
            emit loadingChanged();
            emit sessionRestored();
        },
        [this](int status, const QString &) {
            if (status == 401 && !m_refreshToken.isEmpty()) {
                m_api->refreshToken(m_refreshToken,
                    [this](const QString &access, const QString &refresh, int expiresIn) {
                        saveTokens(access, refresh, expiresIn);
                        m_api->setToken(access);
                        fetchCurrentUser();
                    },
                    [this](int, const QString &) {
                        m_loading = false;
                        emit loadingChanged();
                        clearTokens();
                        emit sessionExpired();
                    }
                );
            } else {
                m_loading = false;
                emit loadingChanged();
                clearTokens();
                emit sessionExpired();
            }
        }
    );
}

void AuthStore::saveTokens(const QString &access, const QString &refresh, int expiresIn)
{
    m_accessToken  = access;
    m_refreshToken = refresh;
    m_tokenExpiresAt = QDateTime::currentSecsSinceEpoch() + expiresIn;

    localStorageSet("tb_access_token",   access);
    localStorageSet("tb_refresh_token",  refresh);
    localStorageSet("tb_token_expires",  QString::number(m_tokenExpiresAt));
    emit tokenChanged(m_accessToken);
}

void AuthStore::clearTokens()
{
    m_accessToken.clear();
    m_refreshToken.clear();
    m_tokenExpiresAt = 0;
    m_api->clearToken();

    localStorageRemove("tb_access_token");
    localStorageRemove("tb_refresh_token");
    localStorageRemove("tb_token_expires");
    emit tokenChanged(QString{});
}

void AuthStore::loadStoredTokens()
{
    m_accessToken    = localStorageGet("tb_access_token");
    m_refreshToken   = localStorageGet("tb_refresh_token");
    m_tokenExpiresAt = localStorageGet("tb_token_expires").toLongLong();
}

bool AuthStore::isTokenExpiringSoon() const
{
    return m_tokenExpiresAt > 0 &&
           QDateTime::currentSecsSinceEpoch() >= m_tokenExpiresAt - 300;
}

// ── Platform-specific localStorage wrappers ─────────────────────────────────

QString AuthStore::localStorageGet(const QString &key)
{
#ifdef Q_OS_WASM
    auto val = emscripten::val::global("localStorage").call<std::string>(
        "getItem", key.toStdString());
    if (val.isNull() || val.isUndefined()) return {};
    return QString::fromStdString(val.as<std::string>());
#else
    // Desktop/native: use QSettings as fallback
    QSettings settings("TaikoBot", "auth");
    return settings.value(key).toString();
#endif
}

void AuthStore::localStorageSet(const QString &key, const QString &value)
{
#ifdef Q_OS_WASM
    emscripten::val::global("localStorage").call<void>(
        "setItem", key.toStdString(), value.toStdString());
#else
    QSettings settings("TaikoBot", "auth");
    settings.setValue(key, value);
#endif
}

void AuthStore::localStorageRemove(const QString &key)
{
#ifdef Q_OS_WASM
    emscripten::val::global("localStorage").call<void>(
        "removeItem", key.toStdString());
#else
    QSettings settings("TaikoBot", "auth");
    settings.remove(key);
#endif
}
