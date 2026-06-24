#include <QGuiApplication>
#include <QQmlApplicationEngine>
#include <QQmlContext>
#include <QtQuickControls2/QQuickStyle>

#include "api/ApiClient.h"
#include "stores/AuthStore.h"
#include "stores/CourseStore.h"
#include "stores/LessonStore.h"
#include "stores/UserStore.h"
#include "storage/LocalDatabase.h"
#include "storage/CacheManager.h"
#include "sync/PendingOpQueue.h"
#include "sync/SyncEngine.h"
#include "models/Participation.h"

int main(int argc, char *argv[])
{
    QGuiApplication app(argc, argv);
    app.setApplicationName("TaikoBot");
    app.setOrganizationName("TaikoBot");

    QQuickStyle::setStyle("Material");

    // Storage layer
    auto *db         = new LocalDatabase(&app);
    db->init();
    auto *cache      = new CacheManager(db, &app);

    // API + auth
    auto *apiClient  = new ApiClient(&app);
    auto *authStore  = new AuthStore(&app);

    // Sync layer
    auto *queue      = new PendingOpQueue(db, &app);
    auto *syncEngine = new SyncEngine(apiClient, queue, &app);

    // Data stores
    auto *courseStore = new CourseStore(apiClient, db, cache, &app);
    auto *lessonStore = new LessonStore(apiClient, db, cache, queue, syncEngine, &app);
    auto *userStore   = new UserStore(apiClient, db, cache, &app);

    // Propagate auth token from AuthStore to the shared ApiClient used by all other stores.
    QObject::connect(authStore, &AuthStore::tokenChanged, apiClient, [apiClient](const QString &token) {
        if (token.isEmpty()) apiClient->clearToken();
        else                 apiClient->setToken(token);
    });

    syncEngine->startMonitoring();

    QQmlApplicationEngine engine;

    engine.rootContext()->setContextProperty("ApiClient",   apiClient);
    engine.rootContext()->setContextProperty("AuthStore",   authStore);
    engine.rootContext()->setContextProperty("CourseStore", courseStore);
    engine.rootContext()->setContextProperty("LessonStore", lessonStore);
    engine.rootContext()->setContextProperty("UserStore",   userStore);
    engine.rootContext()->setContextProperty("SyncEngine",  syncEngine);

    const QUrl url(u"qrc:/TaikoBot/qml/main.qml"_qs);
    QObject::connect(
        &engine, &QQmlApplicationEngine::objectCreated,
        &app, [url](QObject *obj, const QUrl &objUrl) {
            if (!obj && url == objUrl) QCoreApplication::exit(-1);
        },
        Qt::QueuedConnection
    );

    engine.load(url);

    // Restore session after engine loads
    QMetaObject::invokeMethod(authStore, "restoreSession", Qt::QueuedConnection);

    return app.exec();
}
