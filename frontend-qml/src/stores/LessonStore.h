#pragma once

#include <QObject>
#include <QVariantList>
#include <QVariantMap>
#include <QQmlEngine>

#include "../api/ApiClient.h"
#include "../storage/LocalDatabase.h"
#include "../storage/CacheManager.h"
#include "../sync/PendingOpQueue.h"
#include "../sync/SyncEngine.h"

class LessonStore : public QObject
{
    Q_OBJECT
    QML_ELEMENT

    Q_PROPERTY(QVariantList dashboardLessons READ dashboardLessons NOTIFY dashboardLessonsChanged)
    Q_PROPERTY(bool dashboardLoading READ isDashboardLoading NOTIFY dashboardLoadingChanged)
    Q_PROPERTY(QVariantMap currentLesson READ currentLesson NOTIFY currentLessonChanged)
    Q_PROPERTY(QVariantList participants READ participants NOTIFY participantsChanged)
    Q_PROPERTY(bool loading READ isLoading NOTIFY loadingChanged)
    Q_PROPERTY(QString error READ error NOTIFY errorChanged)

public:
    explicit LessonStore(ApiClient *api, LocalDatabase *db, CacheManager *cache,
                         PendingOpQueue *queue, SyncEngine *sync, QObject *parent = nullptr);

    QVariantList dashboardLessons()  const { return m_dashboardLessons; }
    bool         isDashboardLoading() const { return m_dashboardLoading; }
    QVariantMap  currentLesson()     const { return m_currentLesson; }
    QVariantList participants()      const { return m_participants; }
    bool         isLoading()         const { return m_loading; }
    QString      error()             const { return m_error; }

    Q_INVOKABLE void loadDashboard();
    Q_INVOKABLE void loadLesson(int lessonId);
    Q_INVOKABLE void createLesson(const QVariantMap &data);
    Q_INVOKABLE void updateLesson(int lessonId, const QVariantMap &data);
    Q_INVOKABLE void deleteLesson(int lessonId);

    // Participant actions
    Q_INVOKABLE void signIn(int lessonId, const QString &message = {});
    Q_INVOKABLE void signOut(int lessonId);
    Q_INVOKABLE void compensate(int lessonId, const QString &message = {});
    Q_INVOKABLE void assist(int lessonId);
    Q_INVOKABLE void sendMessage(int lessonId, const QString &message);

    // Teacher-only attendance (offline-safe)
    Q_INVOKABLE void setAttendance(int lessonId, int userId, const QString &participation);

    // Teacher management
    Q_INVOKABLE void addTeacher(int lessonId, int userId);
    Q_INVOKABLE void removeTeacher(int lessonId, int userId);

signals:
    void dashboardLessonsChanged();
    void dashboardLoadingChanged();
    void currentLessonChanged();
    void participantsChanged();
    void loadingChanged();
    void errorChanged();
    void participationChanged(int lessonId);
    void lessonDeleted(int lessonId);

private:
    ApiClient      *m_api;
    LocalDatabase  *m_db;
    CacheManager   *m_cache;
    PendingOpQueue *m_queue;
    SyncEngine     *m_sync;

    QVariantList m_dashboardLessons;
    bool         m_dashboardLoading = false;
    QVariantMap  m_currentLesson;
    QVariantList m_participants;
    bool         m_loading = false;
    QString      m_error;

    void setLoading(bool v);
    void setError(const QString &e);
    void reloadLesson(int lessonId);
};
