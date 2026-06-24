#include "LocalDatabase.h"

#include <QSqlQuery>
#include <QSqlError>
#include <QJsonDocument>
#include <QDebug>
#include <QStandardPaths>
#include <QDir>

#ifdef Q_OS_WASM
#include <emscripten.h>
#endif

LocalDatabase::LocalDatabase(QObject *parent)
    : QObject(parent)
{}

bool LocalDatabase::init()
{
    QString dbPath;
#ifdef Q_OS_WASM
    // Emscripten MEMFS — persisted to IndexedDB via FS.syncfs
    EM_ASM({
        FS.mkdir('/taikobot_data');
        FS.mount(IDBFS, {}, '/taikobot_data');
        FS.syncfs(true, function(err) {});  // load from IndexedDB
    });
    dbPath = "/taikobot_data/cache.db";
#else
    const QString dir = QStandardPaths::writableLocation(QStandardPaths::AppDataLocation);
    QDir().mkpath(dir);
    dbPath = dir + "/cache.db";
#endif

    m_db = QSqlDatabase::addDatabase("QSQLITE", "localcache");
    m_db.setDatabaseName(dbPath);

    if (!m_db.open()) {
        qWarning() << "LocalDatabase: cannot open:" << m_db.lastError().text();
        return false;
    }

    createTables();
    return true;
}

void LocalDatabase::createTables()
{
    const QStringList ddl = {
        "CREATE TABLE IF NOT EXISTS kv_cache ("
        "  key TEXT PRIMARY KEY,"
        "  value TEXT,"
        "  cached_at INTEGER DEFAULT 0"
        ")",

        "CREATE TABLE IF NOT EXISTS lessons_cache ("
        "  id INTEGER PRIMARY KEY,"
        "  data TEXT,"
        "  cached_at INTEGER DEFAULT 0"
        ")",

        "CREATE TABLE IF NOT EXISTS courses_cache ("
        "  id INTEGER PRIMARY KEY,"
        "  data TEXT,"
        "  cached_at INTEGER DEFAULT 0"
        ")",

        "CREATE TABLE IF NOT EXISTS lesson_participants_cache ("
        "  lesson_id INTEGER,"
        "  user_id INTEGER,"
        "  participation TEXT,"
        "  data TEXT,"
        "  cached_at INTEGER DEFAULT 0,"
        "  PRIMARY KEY (lesson_id, user_id)"
        ")",

        "CREATE TABLE IF NOT EXISTS sync_state ("
        "  key TEXT PRIMARY KEY,"
        "  value TEXT"
        ")",

        "CREATE TABLE IF NOT EXISTS pending_ops ("
        "  id INTEGER PRIMARY KEY AUTOINCREMENT,"
        "  op_id TEXT UNIQUE,"
        "  created_at INTEGER,"
        "  http_method TEXT,"
        "  endpoint TEXT,"
        "  payload TEXT,"
        "  status TEXT DEFAULT 'pending',"
        "  retry_count INTEGER DEFAULT 0,"
        "  last_error TEXT"
        ")"
    };

    QSqlQuery q(m_db);
    for (const auto &sql : ddl) {
        if (!q.exec(sql))
            qWarning() << "LocalDatabase DDL error:" << q.lastError().text();
    }
}

bool LocalDatabase::execSql(const QString &sql, const QVariantList &binds) const
{
    QSqlQuery q(m_db);
    q.prepare(sql);
    for (int i = 0; i < binds.size(); ++i)
        q.addBindValue(binds[i]);
    if (!q.exec()) {
        qWarning() << "LocalDatabase SQL error:" << q.lastError().text() << sql;
        return false;
    }
    return true;
}

void LocalDatabase::syncToIndexedDB()
{
#ifdef Q_OS_WASM
    EM_ASM({ FS.syncfs(false, function(err) {}); });
#endif
}

// ── Dashboard ────────────────────────────────────────────────────────────────

void LocalDatabase::saveDashboard(const QJsonObject &data)
{
    const QString json = QJsonDocument(data).toJson(QJsonDocument::Compact);
    execSql("INSERT OR REPLACE INTO kv_cache(key, value, cached_at) VALUES(?,?,strftime('%s','now'))",
            {"dashboard", json});
    syncToIndexedDB();
}

QJsonObject LocalDatabase::loadDashboard() const
{
    QSqlQuery q(m_db);
    q.prepare("SELECT value FROM kv_cache WHERE key = 'dashboard'");
    if (q.exec() && q.next())
        return QJsonDocument::fromJson(q.value(0).toByteArray()).object();
    return {};
}

// ── Courses ──────────────────────────────────────────────────────────────────

void LocalDatabase::saveCourse(const QJsonObject &course)
{
    const int id = course["id"].toInt();
    const QString json = QJsonDocument(course).toJson(QJsonDocument::Compact);
    execSql("INSERT OR REPLACE INTO courses_cache(id, data, cached_at) VALUES(?,?,strftime('%s','now'))",
            {id, json});
    syncToIndexedDB();
}

QJsonObject LocalDatabase::loadCourse(int id) const
{
    QSqlQuery q(m_db);
    q.prepare("SELECT data FROM courses_cache WHERE id = ?");
    q.addBindValue(id);
    if (q.exec() && q.next())
        return QJsonDocument::fromJson(q.value(0).toByteArray()).object();
    return {};
}

QList<QJsonObject> LocalDatabase::loadCourses() const
{
    QList<QJsonObject> list;
    QSqlQuery q(m_db);
    q.exec("SELECT data FROM courses_cache ORDER BY id");
    while (q.next())
        list.append(QJsonDocument::fromJson(q.value(0).toByteArray()).object());
    return list;
}

// ── Lessons ──────────────────────────────────────────────────────────────────

void LocalDatabase::saveLesson(const QJsonObject &lesson)
{
    const int id = lesson["id"].toInt();
    const QString json = QJsonDocument(lesson).toJson(QJsonDocument::Compact);
    execSql("INSERT OR REPLACE INTO lessons_cache(id, data, cached_at) VALUES(?,?,strftime('%s','now'))",
            {id, json});
    syncToIndexedDB();
}

QJsonObject LocalDatabase::loadLesson(int id) const
{
    QSqlQuery q(m_db);
    q.prepare("SELECT data FROM lessons_cache WHERE id = ?");
    q.addBindValue(id);
    if (q.exec() && q.next())
        return QJsonDocument::fromJson(q.value(0).toByteArray()).object();
    return {};
}

// ── Participants ──────────────────────────────────────────────────────────────

void LocalDatabase::saveParticipants(int lessonId, const QJsonArray &participants)
{
    execSql("DELETE FROM lesson_participants_cache WHERE lesson_id = ?", {lessonId});

    for (const auto &val : participants) {
        const QJsonObject p = val.toObject();
        const int userId = p["user_id"].toInt();
        const QString participation = p["participation"].toString();
        const QString json = QJsonDocument(p).toJson(QJsonDocument::Compact);
        execSql("INSERT OR REPLACE INTO lesson_participants_cache"
                "(lesson_id, user_id, participation, data, cached_at)"
                " VALUES(?,?,?,?,strftime('%s','now'))",
                {lessonId, userId, participation, json});
    }
    syncToIndexedDB();
}

QJsonArray LocalDatabase::loadParticipants(int lessonId) const
{
    QJsonArray arr;
    QSqlQuery q(m_db);
    q.prepare("SELECT data FROM lesson_participants_cache WHERE lesson_id = ? ORDER BY user_id");
    q.addBindValue(lessonId);
    if (q.exec()) {
        while (q.next())
            arr.append(QJsonDocument::fromJson(q.value(0).toByteArray()).object());
    }
    return arr;
}

void LocalDatabase::updateParticipant(int lessonId, int userId, const QString &participation)
{
    execSql("UPDATE lesson_participants_cache SET participation = ?, cached_at = strftime('%s','now')"
            " WHERE lesson_id = ? AND user_id = ?",
            {participation, lessonId, userId});
    syncToIndexedDB();
}

// ── Sync state ────────────────────────────────────────────────────────────────

void LocalDatabase::setSyncTimestamp(const QString &key, qint64 ts)
{
    execSql("INSERT OR REPLACE INTO sync_state(key, value) VALUES(?,?)",
            {key, QString::number(ts)});
}

qint64 LocalDatabase::getSyncTimestamp(const QString &key) const
{
    QSqlQuery q(m_db);
    q.prepare("SELECT value FROM sync_state WHERE key = ?");
    q.addBindValue(key);
    if (q.exec() && q.next())
        return q.value(0).toString().toLongLong();
    return 0;
}
