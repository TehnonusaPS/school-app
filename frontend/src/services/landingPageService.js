import api from './api'

/**
 * Landing Page API service — public & admin endpoints.
 *
 * Website Builder uses the SAME endpoints as Konfigurasi Global,
 * routing to /landing-page/foundations/{id} or /landing-page/schools/{id}
 * based on the logged-in user's role.
 */

// ─── PUBLIC ───────────────────────────────────────────────────────────────────
export function getPublicLandingPage(slug) {
  return api.get(`/landing-page/public/${slug}`).then(r => r.data)
}

// ─── ADMIN: Fetch landing page config for the current user's entity ───────────
/**
 * Get the landing page config for the entity the current user belongs to.
 * @param {Object} user - The current auth user (must have role, foundation_id, school_id)
 */
export function getMyEntityLandingPage(user) {
  if (!user) return Promise.reject(new Error('User tidak ditemukan.'))

  const role = user.role

  if (role === 'admin_yayasan' && user.foundation_id) {
    return api.get(`/landing-page/foundations/${user.foundation_id}`).then(r => r.data)
  }

  if (['admin_sekolah', 'kepala_sekolah'].includes(role) && user.school_id) {
    return api.get(`/landing-page/schools/${user.school_id}`).then(r => r.data)
  }

  return Promise.reject(new Error(`Role "${role}" tidak memiliki akses builder atau ID entitas tidak ditemukan.`))
}

/**
 * Save the landing page config for the entity the current user belongs to.
 * @param {Object} user - The current auth user
 * @param {Object} data - The payload to save
 */
export function saveMyEntityLandingPage(user, data) {
  if (!user) return Promise.reject(new Error('User tidak ditemukan.'))

  const role = user.role

  if (role === 'admin_yayasan' && user.foundation_id) {
    return api.put(`/landing-page/foundations/${user.foundation_id}`, data).then(r => r.data)
  }

  if (['admin_sekolah', 'kepala_sekolah'].includes(role) && user.school_id) {
    return api.put(`/landing-page/schools/${user.school_id}`, data).then(r => r.data)
  }

  return Promise.reject(new Error(`Role "${role}" tidak memiliki akses builder atau ID entitas tidak ditemukan.`))
}

// ─── IMAGE UPLOAD ─────────────────────────────────────────────────────────────
export function uploadLandingImage(file) {
  const formData = new FormData()
  formData.append('image', file)
  return api
    .post('/landing-page/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(r => r.data)
}
