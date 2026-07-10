import api from './api'

export async function fetchAllSuratDispensasi(params) {
  const response = await api.get('/komunikasi/surat-dispensasi', { params })
  return response.data
}

export async function createSuratDispensasi(data) {
  const response = await api.post('/komunikasi/surat-dispensasi', data)
  return response.data
}

export async function updateSuratDispensasi(id, data) {
  const response = await api.put(`/komunikasi/surat-dispensasi/${id}`, data)
  return response.data
}

export async function deleteSuratDispensasi(id) {
  const response = await api.delete(`/komunikasi/surat-dispensasi/${id}`)
  return response.data
}
