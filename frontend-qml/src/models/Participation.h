#pragma once

#include <QObject>
#include <QString>
#include <QQmlEngine>

class Participation : public QObject
{
    Q_OBJECT
    QML_ELEMENT
    QML_UNCREATABLE("Participation is a namespace for the enum")

public:
    enum Value {
        SignedIn,
        SignedOut,
        Teacher,
        Assistance,
        Late,
        NoShow,
        Waitlist
    };
    Q_ENUM(Value)

    static Value fromString(const QString &s) {
        if (s == "signed_in")  return SignedIn;
        if (s == "signed_out") return SignedOut;
        if (s == "teacher")    return Teacher;
        if (s == "assistance") return Assistance;
        if (s == "late")       return Late;
        if (s == "no_show")    return NoShow;
        if (s == "waitlist")   return Waitlist;
        return SignedOut;
    }

    static QString toString(Value v) {
        switch (v) {
        case SignedIn:   return "signed_in";
        case SignedOut:  return "signed_out";
        case Teacher:    return "teacher";
        case Assistance: return "assistance";
        case Late:       return "late";
        case NoShow:     return "no_show";
        case Waitlist:   return "waitlist";
        }
        return "signed_out";
    }

    // Display label for UI
    static QString label(Value v) {
        switch (v) {
        case SignedIn:   return "Signed In";
        case SignedOut:  return "Signed Out";
        case Teacher:    return "Teacher";
        case Assistance: return "Assistance";
        case Late:       return "Late";
        case NoShow:     return "No Show";
        case Waitlist:   return "Waitlist";
        }
        return "";
    }
};
