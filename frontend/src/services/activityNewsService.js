import api from './api'

export async function fetchActivityNews(params) {
  const response = await api.get('/komunikasi/berita-kegiatan', { params })
  return response.data
}

export async function createActivityNews(data) {
  const response = await api.post('/komunikasi/berita-kegiatan', data)
  return response.data
}

export async function updateActivityNews(id, data) {
  const response = await api.put(`/komunikasi/berita-kegiatan/${id}`, data)
  return response.data
}

export async function deleteActivityNews(id) {
  const response = await api.delete(`/komunikasi/berita-kegiatan/${id}`)
  return response.data
}
