import api from './api'

export async function fetchAllSubjects(params) {
  const response = await api.get('/management/subjects', { params })
  return response.data
}

export async function createSubject(data) {
  const response = await api.post('/management/subjects', data)
  return response.data
}

export async function getSubject(id) {
  const response = await api.get(`/management/subjects/${id}`)
  return response.data
}

export async function updateSubject(id, data) {
  const response = await api.put(`/management/subjects/${id}`, data)
  return response.data
}

export async function deleteSubject(id) {
  const response = await api.delete(`/management/subjects/${id}`)
  return response.data
}
