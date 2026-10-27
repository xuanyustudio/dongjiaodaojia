function read(key) {
  const pattern = new RegExp('(?:^|; )' + encodeURIComponent(key).replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)')
  const matched = document.cookie.match(pattern)
  if (!matched) return null
  const value = decodeURIComponent(matched[1])
  if (value.startsWith('{') && value.endsWith('}')) {
    try { return JSON.parse(value) } catch (error) { return value }
  }
  return value
}

const cookies = {
  get(key) {
    return read(key)
  },
  set(key, value, expires) {
    let maxAge = expires
    if (maxAge && typeof maxAge === 'object') maxAge = maxAge.expires
    const stored = value && typeof value === 'object' ? JSON.stringify(value) : value
    const age = typeof maxAge === 'number' ? `; max-age=${maxAge}` : ''
    document.cookie = `${encodeURIComponent(key)}=${encodeURIComponent(stored ?? '')}${age}; path=/`
  },
  remove(key) {
    document.cookie = `${encodeURIComponent(key)}=; max-age=0; path=/`
  }
}

export default cookies
