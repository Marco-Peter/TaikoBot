#pragma once

#include <QObject>
#include <QJsonObject>
#include <QList>
#include "../storage/LocalDatabase.h"

// Durable FIFO queue for mutations that must reach the server.
// Each op is persisted to SQLite before the network call, so
// a page refresh cannot lose queued changes.
class PendingOpQueue : public QObject
{
    Q_OBJECT
    Q_PROPERTY(int pendingCount READ pendingCount NOTIFY pendingCountChanged)

public:
    struct Op {
        QString opId;
        QString method;
        QString endpoint;
        QJsonObject payload;
        qint64 createdAt = 0;
        int retryCount = 0;
        QString status;
        QString lastError;
    };

    explicit PendingOpQueue(LocalDatabase *db, QObject *parent = nullptr);

    // Add op to queue; returns the generated opId.
    QString enqueue(const QString &method, const QString &endpoint, const QJsonObject &payload);

    QList<Op> pending() const;

    void markSyncing(const QString &opId);
    void markDone(const QString &opId);
    void markFailed(const QString &opId, const QString &error);

    int pendingCount() const;

signals:
    void pendingCountChanged();

private:
    LocalDatabase *m_db;

    QList<Op> query(const QString &whereClause = {}) const;
    void notifyCount();
};
