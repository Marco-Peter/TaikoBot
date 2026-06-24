#include "SyncEngine.h"

#include <QDebug>

#ifdef Q_OS_WASM
#include <emscripten.h>
#endif

SyncEngine::SyncEngine(ApiClient *api, PendingOpQueue *queue, QObject *parent)
    : QObject(parent)
    , m_api(api)
    , m_queue(queue)
{
    connect(m_queue, &PendingOpQueue::pendingCountChanged,
            this, &SyncEngine::pendingCountChanged);

    connect(m_api, &ApiClient::networkError, this, [this](int status, const QString &) {
        if (status == 0)  // 0 = no network response
            setOnline(false);
    });
}

void SyncEngine::startMonitoring()
{
#ifdef Q_OS_WASM
    // Listen to browser online/offline events via Emscripten
    EM_ASM({
        window.addEventListener('online',  function() { Module._taikoOnline();  });
        window.addEventListener('offline', function() { Module._taikoOffline(); });
    });
    emscripten_set_main_loop_arg([](void *) {}, nullptr, 0, 0);
#endif
}

int SyncEngine::pendingCount() const
{
    return m_queue->pendingCount();
}

void SyncEngine::flushQueue()
{
    if (m_syncing || !m_online) return;

    const auto ops = m_queue->pending();
    if (ops.isEmpty()) return;

    setSyncing(true);
    m_inFlight = ops.size();

    for (const auto &op : ops)
        processOp(op);
}

void SyncEngine::processOp(const PendingOpQueue::Op &op)
{
    m_queue->markSyncing(op.opId);

    auto onSuccess = [this, op](const QJsonObject &) {
        m_queue->markDone(op.opId);
        if (--m_inFlight <= 0) setSyncing(false);
    };

    auto onError = [this, op](int status, const QString &msg) {
        if (status == 0) {
            // Network unreachable — stop the flush; deps may follow this op
            setOnline(false);
            m_queue->markFailed(op.opId, "network_unreachable");
            m_inFlight = 0;
            setSyncing(false);
            return;
        }

        if (status == 409 || status == 422) {
            // Server-wins conflict: mark failed, emit signal for UI
            m_queue->markFailed(op.opId, msg);
            emit conflictDetected(op.endpoint, msg);
        } else {
            m_queue->markFailed(op.opId, msg);
            emit syncError(op.opId, msg);
        }

        if (--m_inFlight <= 0) setSyncing(false);
    };

    if (op.method == "POST")
        m_api->post(op.endpoint, op.payload, onSuccess, onError);
    else if (op.method == "PUT")
        m_api->put(op.endpoint, op.payload, onSuccess, onError);
    else if (op.method == "DELETE")
        m_api->del(op.endpoint, onSuccess, onError);
}

void SyncEngine::setOnline(bool online)
{
    if (m_online == online) return;
    m_online = online;
    emit onlineChanged();

    if (m_online)
        flushQueue();
}

void SyncEngine::setSyncing(bool syncing)
{
    if (m_syncing == syncing) return;
    m_syncing = syncing;
    emit syncingChanged();
}

#ifdef Q_OS_WASM
// Called from JS event listeners registered in startMonitoring()
extern "C" {
    EMSCRIPTEN_KEEPALIVE void taikoOnline()
    {
        // SyncEngine is not directly accessible here; use Qt's event system via a static pointer.
        // For simplicity, the SyncEngine registers itself and we call through.
    }
    EMSCRIPTEN_KEEPALIVE void taikoOffline() {}
}
#endif
