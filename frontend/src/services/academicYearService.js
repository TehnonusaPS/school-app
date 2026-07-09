import api from './api'

export async function fetchAllAcademicYears(params) {
  const response = await api.get('/management/academic-years', { params })
  return response.data
}
