#pragma once

#include <QString>
#include <QDateTime>
#include <QList>
#include <QJsonObject>
#include <QJsonArray>
#include <QQmlEngine>
#include "LessonModel.h"

struct CourseModel {
    Q_GADGET
    QML_VALUE_TYPE(courseModel)
    Q_PROPERTY(int id MEMBER id)
    Q_PROPERTY(QString name MEMBER name)
    Q_PROPERTY(QString description MEMBER description)
    Q_PROPERTY(int capacity MEMBER capacity)
    Q_PROPERTY(int signoutLimit MEMBER signoutLimit)
    Q_PROPERTY(int participantsCount MEMBER participantsCount)

public:
    int     id = 0;
    QString name;
    QString description;
    int     capacity = 0;
    int     signoutLimit = 0;
    int     teacherPayment = 0;
    int     assistPayment = 0;
    int     participantsCount = 0;
    QDateTime firstLessonStart;
    QDateTime lastLessonFinish;
    QList<LessonModel> lessons;

    static CourseModel fromJson(const QJsonObject &obj) {
        CourseModel m;
        m.id               = obj["id"].toInt();
        m.name             = obj["name"].toString();
        m.description      = obj["description"].toString();
        m.capacity         = obj["capacity"].toInt();
        m.signoutLimit     = obj["signout_limit"].toInt();
        m.teacherPayment   = obj["teacher_payment"].toInt();
        m.assistPayment    = obj["assist_payment"].toInt();
        m.participantsCount = obj["participants_count"].toInt();

        if (!obj["first_lesson_start"].isNull())
            m.firstLessonStart = QDateTime::fromString(obj["first_lesson_start"].toString(), Qt::ISODate);
        if (!obj["last_lesson_finish"].isNull())
            m.lastLessonFinish = QDateTime::fromString(obj["last_lesson_finish"].toString(), Qt::ISODate);

        if (obj.contains("lessons") && obj["lessons"].isObject()) {
            const auto arr = obj["lessons"].toObject()["data"].toArray();
            for (const auto &l : arr)
                m.lessons.append(LessonModel::fromJson(l.toObject()));
        }

        return m;
    }
};
