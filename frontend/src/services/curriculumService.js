import api from './api'

export async function fetchCurriculums(params = {}) {
  const response = await api.get('/management/curriculums', { params })
  return response.data
}

export async function fetchCurriculumDetail(id) {
  const response = await api.get(`/management/curriculums/${id}`)
  return response.data
}

export async function createCurriculum(data) {
  const response = await api.post('/management/curriculums', data)
  return response.data
}

export async function updateCurriculum(id, data) {
  const response = await api.put(`/management/curriculums/${id}`, data)
  return response.data
}

export async function deleteCurriculum(id) {
  const response = await api.delete(`/management/curriculums/${id}`)
  return response.data
}
