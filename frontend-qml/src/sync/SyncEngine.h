#pragma once

#include <QObject>
#include <QQmlEngine>
#include "PendingOpQueue.h"
#include "../api/ApiClient.h"

// Monitors connectivity and flushes the pending_ops queue when online.
// Connectivity is detected via Emscripten online/offline browser events
// in WASM builds, and via ApiClient network errors on native.
class SyncEngine : public QObject
{
    Q_OBJECT
    QML_ELEMENT

    Q_PROPERTY(bool online READ isOnline NOTIFY onlineChanged)
    Q_PROPERTY(bool syncing READ isSyncing NOTIFY syncingChanged)
    Q_PROPERTY(int pendingCount READ pendingCount NOTIFY pendingCountChanged)

public:
    explicit SyncEngine(ApiClient *api, PendingOpQueue *queue, QObject *parent = nullptr);

    void startMonitoring();

    bool isOnline()   const { return m_online; }
    bool isSyncing()  const { return m_syncing; }
    int  pendingCount() const;

    Q_INVOKABLE void flushQueue();

signals:
    void onlineChanged();
    void syncingChanged();
    void pendingCountChanged();
    void conflictDetected(QString endpoint, QString serverMessage);
    void syncError(QString opId, QString error);

private:
    ApiClient       *m_api;
    PendingOpQueue  *m_queue;
    bool             m_online  = true;
    bool             m_syncing = false;
    int              m_inFlight = 0;

    void processOp(const PendingOpQueue::Op &op);
    void setOnline(bool online);
    void setSyncing(bool syncing);

#ifdef Q_OS_WASM
    static void onBrowserOnline(void *userData);
    static void onBrowserOffline(void *userData);
#endif
};
