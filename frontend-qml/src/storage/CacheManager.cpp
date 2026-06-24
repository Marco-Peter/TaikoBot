#include "CacheManager.h"

CacheManager::CacheManager(LocalDatabase *db, QObject *parent)
    : QObject(parent)
    , m_db(db)
{}

bool CacheManager::isFresh(const QString &key) const
{
    const qint64 ts = m_db->getSyncTimestamp("cache_" + key);
    if (ts == 0) return false;
    return (QDateTime::currentSecsSinceEpoch() - ts) < TTL_SECONDS;
}

void CacheManager::markFresh(const QString &key)
{
    m_db->setSyncTimestamp("cache_" + key, QDateTime::currentSecsSinceEpoch());
}

void CacheManager::invalidate(const QString &key)
{
    m_db->setSyncTimestamp("cache_" + key, 0);
}

void CacheManager::invalidateAll()
{
    // Reset all known cache keys to force refresh on next load.
    for (const QString &key : {"dashboard", "courses", "lessons", "users"})
        m_db->setSyncTimestamp("cache_" + key, 0);
}
