import api from './api'

export async function fetchAllSiswa(params) {
  const response = await api.get('/management/students', { params })
  return response.data
}

export async function createSiswa(data) {
  const response = await api.post('/management/students', data)
  return response.data
}

export async function getSiswaDetail(id) {
  const response = await api.get(`/management/students/${id}`)
  return response.data
}

export async function updateSiswa(id, data) {
  const response = await api.put(`/management/students/${id}`, data)
  return response.data
}

export async function deleteSiswa(id) {
  const response = await api.delete(`/management/students/${id}`)
  return response.data
}
