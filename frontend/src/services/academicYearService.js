import api from './api'

export async function fetchAllAcademicYears(params) {
  const response = await api.get('/management/academic-years', { params })
  return response.data
}

export async function createAcademicYear(data) {
  const response = await api.post('/management/academic-years', data)
  return response.data
}

export async function getAcademicYear(id) {
  const response = await api.get(`/management/academic-years/${id}`)
  return response.data
}

export async function updateAcademicYear(id, data) {
  const response = await api.put(`/management/academic-years/${id}`, data)
  return response.data
}

export async function deleteAcademicYear(id) {
  const response = await api.delete(`/management/academic-years/${id}`)
  return response.data
}

