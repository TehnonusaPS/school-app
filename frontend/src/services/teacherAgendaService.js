import api from './api'

export async function fetchTeacherAgendas(params) {
  const response = await api.get('/teacher-agendas', { params })
  return response.data
}

export async function fetchMyClassrooms() {
  const response = await api.get('/teacher-agendas/my-classrooms')
  return response.data
}

export async function createTeacherAgenda(data) {
  const response = await api.post('/teacher-agendas', data)
  return response.data
}

export async function updateTeacherAgenda(id, data) {
  const response = await api.put(`/teacher-agendas/${id}`, data)
  return response.data
}

export async function deleteTeacherAgenda(id) {
  const response = await api.delete(`/teacher-agendas/${id}`)
  return response.data
}
