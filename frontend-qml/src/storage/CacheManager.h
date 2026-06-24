#pragma once

#include <QObject>
#include <QDateTime>
#include "LocalDatabase.h"

// Stale-while-revalidate TTL helper.
// Keys use the format "resource/id", e.g. "lesson/42", "dashboard".
class CacheManager : public QObject
{
    Q_OBJECT
public:
    static constexpr int TTL_SECONDS = 900;  // 15 minutes

    explicit CacheManager(LocalDatabase *db, QObject *parent = nullptr);

    bool isFresh(const QString &key) const;
    void markFresh(const QString &key);
    void invalidate(const QString &key);
    void invalidateAll();

private:
    LocalDatabase *m_db;
};
