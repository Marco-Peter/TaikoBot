#pragma once

#include <QObject>
#include <QVariantList>
#include <QVariantMap>
#include <QQmlEngine>

#include "../api/ApiClient.h"
#include "../models/CourseModel.h"
#include "../storage/LocalDatabase.h"
#include "../storage/CacheManager.h"

class CourseStore : public QObject
{
    Q_OBJECT
    QML_ELEMENT

    Q_PROPERTY(QVariantList courses READ courses NOTIFY coursesChanged)
    Q_PROPERTY(QVariantMap currentCourse READ currentCourse NOTIFY currentCourseChanged)
    Q_PROPERTY(bool loading READ isLoading NOTIFY loadingChanged)
    Q_PROPERTY(QString error READ error NOTIFY errorChanged)

public:
    explicit CourseStore(ApiClient *api, LocalDatabase *db, CacheManager *cache,
                         QObject *parent = nullptr);

    QVariantList courses()     const { return m_courses; }
    QVariantMap  currentCourse() const { return m_currentCourse; }
    bool         isLoading()   const { return m_loading; }
    QString      error()       const { return m_error; }

    Q_INVOKABLE void loadCourses();
    Q_INVOKABLE void loadCourse(int courseId);
    Q_INVOKABLE void createCourse(const QVariantMap &data);
    Q_INVOKABLE void updateCourse(int courseId, const QVariantMap &data);
    Q_INVOKABLE void deleteCourse(int courseId);
    Q_INVOKABLE void signup(int courseId);
    Q_INVOKABLE void markPaid(int courseId, int userId);

signals:
    void coursesChanged();
    void currentCourseChanged();
    void loadingChanged();
    void errorChanged();
    void courseUpdated(int courseId);
    void courseDeleted(int courseId);

private:
    ApiClient    *m_api;
    LocalDatabase *m_db;
    CacheManager  *m_cache;

    QVariantList m_courses;
    QVariantMap  m_currentCourse;
    bool         m_loading = false;
    QString      m_error;

    void setLoading(bool v);
    void setError(const QString &e);
    static QVariantMap jsonToVariant(const QJsonObject &obj);
};
