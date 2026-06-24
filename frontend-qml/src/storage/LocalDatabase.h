#pragma once

#include <QObject>
#include <QSqlDatabase>
#include <QJsonObject>
#include <QJsonArray>
#include <QVariantList>

class LocalDatabase : public QObject
{
    Q_OBJECT
public:
    explicit LocalDatabase(QObject *parent = nullptr);

    bool init();

    // Dashboard
    void saveDashboard(const QJsonObject &data);
    QJsonObject loadDashboard() const;

    // Courses
    void saveCourse(const QJsonObject &course);
    QJsonObject loadCourse(int id) const;
    QList<QJsonObject> loadCourses() const;

    // Lessons
    void saveLesson(const QJsonObject &lesson);
    QJsonObject loadLesson(int id) const;

    // Participants
    void saveParticipants(int lessonId, const QJsonArray &participants);
    QJsonArray loadParticipants(int lessonId) const;
    void updateParticipant(int lessonId, int userId, const QString &participation);

    // Sync state
    void setSyncTimestamp(const QString &key, qint64 ts);
    qint64 getSyncTimestamp(const QString &key) const;

private:
    QSqlDatabase m_db;

    void createTables();
    void syncToIndexedDB();  // WASM: flush Emscripten FS to IndexedDB
    bool execSql(const QString &sql, const QVariantList &binds = {}) const;
};
