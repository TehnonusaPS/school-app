import api from './api'

/**
 * Fetch list of announcements.
 */
export async function getAnnouncements() {
  const response = await api.get('/announcements')
  return response.data.data
}

/**
 * Create a new announcement.
 */
export async function createAnnouncement(payload) {
  const response = await api.post('/announcements', {
    title: payload.title,
    content: payload.content,
    category: payload.category,
    target_school_id: payload.target_school_id || null,
    publish_date: payload.publish_date,
  })
  return response.data.data
}

/**
 * Update an existing announcement.
 */
export async function updateAnnouncement(id, payload) {
  const response = await api.put(`/announcements/${id}`, {
    title: payload.title,
    content: payload.content,
    category: payload.category,
    target_school_id: payload.target_school_id || null,
    publish_date: payload.publish_date,
  })
  return response.data.data
}

/**
 * Delete an announcement.
 */
export async function deleteAnnouncement(id) {
  const response = await api.delete(`/announcements/${id}`)
  return response.data
}
