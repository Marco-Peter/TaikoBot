#pragma once

#include <QString>
#include <QJsonObject>
#include <QQmlEngine>
#include "Participation.h"

struct LessonParticipantModel {
    Q_GADGET
    QML_VALUE_TYPE(lessonParticipantModel)
    Q_PROPERTY(int userId MEMBER userId)
    Q_PROPERTY(QString nickname MEMBER nickname)
    Q_PROPERTY(QString profilePhotoUrl MEMBER profilePhotoUrl)
    Q_PROPERTY(int participation MEMBER participation)
    Q_PROPERTY(QString message MEMBER message)

public:
    int     userId = 0;
    QString firstName;
    QString lastName;
    QString nickname;
    QString profilePhotoUrl;
    Participation::Value participation = Participation::SignedIn;
    QString message;

    static LessonParticipantModel fromJson(const QJsonObject &obj) {
        LessonParticipantModel m;
        m.userId          = obj["id"].toInt();
        m.firstName       = obj["first_name"].toString();
        m.lastName        = obj["last_name"].toString();
        m.nickname        = obj["nickname"].toString();
        m.profilePhotoUrl = obj["profile_photo_url"].toString();
        m.participation   = Participation::fromString(obj["participation"].toString());
        m.message         = obj["message"].toString();
        return m;
    }
};
