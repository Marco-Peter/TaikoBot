#pragma once

#include <QString>
#include <QJsonObject>
#include <QQmlEngine>

struct UserModel {
    Q_GADGET
    QML_VALUE_TYPE(userModel)
    Q_PROPERTY(int id MEMBER id)
    Q_PROPERTY(QString firstName MEMBER firstName)
    Q_PROPERTY(QString lastName MEMBER lastName)
    Q_PROPERTY(QString nickname MEMBER nickname)
    Q_PROPERTY(QString email MEMBER email)
    Q_PROPERTY(QString role MEMBER role)
    Q_PROPERTY(QVariant karma MEMBER karma)  // null = infinite
    Q_PROPERTY(QString profilePhotoUrl MEMBER profilePhotoUrl)
    Q_PROPERTY(int openPayment MEMBER openPayment)

public:
    int     id = 0;
    QString firstName;
    QString lastName;
    QString nickname;
    QString email;
    QString role;
    QVariant karma;      // QVariant::isNull() == true means infinite karma
    QString profilePhotoUrl;
    int     openPayment = 0;

    bool canEditCourses() const { return role == "admin" || role == "teacher"; }
    bool canEditUsers()   const { return role == "admin"; }
    bool canAssistLessons() const { return role == "admin" || role == "teacher"; }

    static UserModel fromJson(const QJsonObject &obj) {
        UserModel m;
        m.id              = obj["id"].toInt();
        m.firstName       = obj["first_name"].toString();
        m.lastName        = obj["last_name"].toString();
        m.nickname        = obj["nickname"].toString();
        m.email           = obj["email"].toString();
        m.role            = obj["role"].toString();
        m.karma           = obj["karma"].isNull() ? QVariant() : QVariant(obj["karma"].toInt());
        m.profilePhotoUrl = obj["profile_photo_url"].toString();
        m.openPayment     = obj["open_payment"].toInt();
        return m;
    }
};
