#pragma once

#include <QString>
#include <QDateTime>
#include <QList>
#include <QJsonObject>
#include <QJsonArray>
#include <QQmlEngine>
#include "LessonParticipantModel.h"

struct LessonModel {
    Q_GADGET
    QML_VALUE_TYPE(lessonModel)
    Q_PROPERTY(int id MEMBER id)
    Q_PROPERTY(int courseId MEMBER courseId)
    Q_PROPERTY(QString title MEMBER title)
    Q_PROPERTY(QDateTime start MEMBER start)
    Q_PROPERTY(QDateTime finish MEMBER finish)
    Q_PROPERTY(QString notes MEMBER notes)

public:
    int      id = 0;
    int      courseId = 0;
    QString  title;
    QDateTime start;
    QDateTime finish;
    QString  notes;
    QString  courseName;
    int      participantsCount = 0;
    QList<LessonParticipantModel> participants;
    QList<LessonParticipantModel> teachers;

    static LessonModel fromJson(const QJsonObject &obj) {
        LessonModel m;
        m.id               = obj["id"].toInt();
        m.courseId         = obj["course_id"].toInt();
        m.title            = obj["title"].toString();
        m.start            = QDateTime::fromString(obj["start"].toString(), Qt::ISODate);
        m.finish           = QDateTime::fromString(obj["finish"].toString(), Qt::ISODate);
        m.notes            = obj["notes"].toString();
        m.participantsCount = obj["participants_count"].toInt();

        if (obj.contains("course") && obj["course"].isObject())
            m.courseName = obj["course"].toObject()["name"].toString();

        if (obj.contains("participants") && obj["participants"].isObject()) {
            const auto arr = obj["participants"].toObject()["data"].toArray();
            for (const auto &p : arr)
                m.participants.append(LessonParticipantModel::fromJson(p.toObject()));
        }

        if (obj.contains("teachers") && obj["teachers"].isObject()) {
            const auto arr = obj["teachers"].toObject()["data"].toArray();
            for (const auto &t : arr)
                m.teachers.append(LessonParticipantModel::fromJson(t.toObject()));
        }

        return m;
    }
};
