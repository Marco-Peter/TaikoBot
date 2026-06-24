#include "PendingOpQueue.h"

#include <QDateTime>
#include <QUuid>
#include <QSqlQuery>
#include <QSqlError>
#include <QJsonDocument>
#include <QDebug>

PendingOpQueue::PendingOpQueue(LocalDatabase *db, QObject *parent)
    : QObject(parent)
    , m_db(db)
{}

QString PendingOpQueue::enqueue(const QString &method, const QString &endpoint,
                                const QJsonObject &payload)
{
    const QString opId = QUuid::createUuid().toString(QUuid::WithoutBraces);
    const qint64 now   = QDateTime::currentSecsSinceEpoch();
    const QString json = QJsonDocument(payload).toJson(QJsonDocument::Compact);

    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.prepare("INSERT INTO pending_ops(op_id, created_at, http_method, endpoint, payload, status)"
              " VALUES(?,?,?,?,?,'pending')");
    q.addBindValue(opId);
    q.addBindValue(now);
    q.addBindValue(method);
    q.addBindValue(endpoint);
    q.addBindValue(json);

    if (!q.exec())
        qWarning() << "PendingOpQueue::enqueue error:" << q.lastError().text();

    notifyCount();
    return opId;
}

QList<PendingOpQueue::Op> PendingOpQueue::pending() const
{
    return query("WHERE status IN ('pending','failed') ORDER BY created_at ASC");
}

void PendingOpQueue::markSyncing(const QString &opId)
{
    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.prepare("UPDATE pending_ops SET status='syncing' WHERE op_id=?");
    q.addBindValue(opId);
    q.exec();
}

void PendingOpQueue::markDone(const QString &opId)
{
    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.prepare("DELETE FROM pending_ops WHERE op_id=?");
    q.addBindValue(opId);
    q.exec();
    notifyCount();
}

void PendingOpQueue::markFailed(const QString &opId, const QString &error)
{
    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.prepare("UPDATE pending_ops SET status='failed', last_error=?, retry_count=retry_count+1"
              " WHERE op_id=?");
    q.addBindValue(error);
    q.addBindValue(opId);
    q.exec();
    notifyCount();
}

int PendingOpQueue::pendingCount() const
{
    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.exec("SELECT COUNT(*) FROM pending_ops WHERE status IN ('pending','syncing','failed')");
    if (q.next()) return q.value(0).toInt();
    return 0;
}

QList<PendingOpQueue::Op> PendingOpQueue::query(const QString &whereClause) const
{
    QList<Op> ops;
    QSqlDatabase db = QSqlDatabase::database("localcache");
    QSqlQuery q(db);
    q.exec("SELECT op_id, http_method, endpoint, payload, created_at, retry_count, status, last_error"
           " FROM pending_ops " + whereClause);

    while (q.next()) {
        Op op;
        op.opId       = q.value(0).toString();
        op.method     = q.value(1).toString();
        op.endpoint   = q.value(2).toString();
        op.payload    = QJsonDocument::fromJson(q.value(3).toByteArray()).object();
        op.createdAt  = q.value(4).toLongLong();
        op.retryCount = q.value(5).toInt();
        op.status     = q.value(6).toString();
        op.lastError  = q.value(7).toString();
        ops.append(op);
    }
    return ops;
}

void PendingOpQueue::notifyCount()
{
    emit pendingCountChanged();
}
