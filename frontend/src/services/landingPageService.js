import api from './api'

/**
 * Landing Page API service — public & admin endpoints.
 */

// ─── PUBLIC ───
export function getPublicLandingPage(slug) {
  return api.get(`/landing-page/public/${slug}`).then(r => r.data)
}

// ─── ADMIN ───
export function getMyLandingPage() {
  return api.get('/admin/landing-page').then(r => r.data)
}

export function saveLandingPage(data) {
  return api.post('/admin/landing-page', data).then(r => r.data)
}

export function uploadLandingImage(file, type) {
  const formData = new FormData()
  formData.append('image', file)
  formData.append('type', type)
  return api
    .post('/admin/landing-page/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(r => r.data)
}

export function updateSections(sections) {
  return api.put('/admin/landing-page/sections', { sections }).then(r => r.data)
}

export function addSectionItem(sectionId, data) {
  return api.post(`/admin/landing-page/sections/${sectionId}/items`, data).then(r => r.data)
}

export function updateSectionItem(sectionId, itemId, data) {
  return api
    .put(`/admin/landing-page/sections/${sectionId}/items/${itemId}`, data)
    .then(r => r.data)
}

export function deleteSectionItem(sectionId, itemId) {
  return api.delete(`/admin/landing-page/sections/${sectionId}/items/${itemId}`).then(r => r.data)
}

export function togglePublish() {
  return api.post('/admin/landing-page/publish').then(r => r.data)
}

export function previewLandingPage() {
  return api.get('/admin/landing-page/preview').then(r => r.data)
}
