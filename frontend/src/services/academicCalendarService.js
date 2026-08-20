import api from './api'

export async function fetchEvents(academicYearId) {
  const response = await api.get('/academic-calendar/events', {
    params: { academic_year_id: academicYearId }
  })
  return response.data
}

export async function storeEvent(data) {
  const response = await api.post('/academic-calendar/events', data)
  return response.data
}

export async function batchStoreEvents(data) {
  const response = await api.post('/academic-calendar/events/batch', data)
  return response.data
}

export async function updateEvent(id, data) {
  const response = await api.put(`/academic-calendar/events/${id}`, data)
  return response.data
}

export async function deleteEvent(id) {
  const response = await api.delete(`/academic-calendar/events/${id}`)
  return response.data
}

export async function fetchCalendarStatus() {
  const response = await api.get('/academic-calendar/status')
  return response.data
}

export async function submitCalendar(academicYearId) {
  const response = await api.post('/academic-calendar/submit', {
    academic_year_id: academicYearId
  })
  return response.data
}

export async function approveCalendar(academicYearId) {
  const response = await api.post('/academic-calendar/approve', {
    academic_year_id: academicYearId
  })
  return response.data
}

export async function rejectCalendar(academicYearId, reason) {
  const response = await api.post('/academic-calendar/reject', {
    academic_year_id: academicYearId,
    reason
  })
  return response.data
}

export async function fetchPublicEvents() {
  const response = await api.get('/academic-calendar/public-events')
  return response.data
}

export async function fetchParentSchedule(params) {
  const response = await api.get('/orang-tua/schedule', { params })
  return response.data
}

export async function fetchParentDashboard(params) {
  const response = await api.get('/orang-tua/jadwal-anak', { params })
  return response.data
}

export async function resetCalendar(academicYearId) {
  const response = await api.post('/academic-calendar/reset', {
    academic_year_id: academicYearId
  })
  return response.data
}

export async function setupCalendarDates(data) {
  const response = await api.post('/academic-calendar/setup-dates', data)
  return response.data
}
