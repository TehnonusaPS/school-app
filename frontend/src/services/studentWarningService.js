import api from './api'

export async function fetchAllSuratPeringatan(params) {
  const response = await api.get('/komunikasi/surat-peringatan', { params })
  return response.data
}

export async function createSuratPeringatan(data) {
  const response = await api.post('/komunikasi/surat-peringatan', data)
  return response.data
}

export async function updateSuratPeringatan(id, data) {
  const response = await api.put(`/komunikasi/surat-peringatan/${id}`, data)
  return response.data
}

export async function deleteSuratPeringatan(id) {
  const response = await api.delete(`/komunikasi/surat-peringatan/${id}`)
  return response.data
}
