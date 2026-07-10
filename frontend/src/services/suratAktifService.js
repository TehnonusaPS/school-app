import api from './api'

export async function fetchAllSuratAktif(params) {
  const response = await api.get('/komunikasi/surat-aktif', { params })
  return response.data
}

export async function createSuratAktif(data) {
  const response = await api.post('/komunikasi/surat-aktif', data)
  return response.data
}

export async function updateSuratAktif(id, data) {
  const response = await api.put(`/komunikasi/surat-aktif/${id}`, data)
  return response.data
}

export async function deleteSuratAktif(id) {
  const response = await api.delete(`/komunikasi/surat-aktif/${id}`)
  return response.data
}
