import api from './api'

export async function fetchExamSchedules(eventId, grade = null) {
  const params = { academic_calendar_event_id: eventId }
  if (grade) params.grade = grade

  const response = await api.get('/academic-calendar/exam-schedules', { params })
  return response.data
}

export async function bulkStoreExamSchedules(eventId, sessions) {
  const response = await api.post('/academic-calendar/exam-schedules/bulk', {
    academic_calendar_event_id: eventId,
    sessions
  })
  return response.data
}

export async function deleteExamSession(sessionId) {
  const response = await api.delete(`/academic-calendar/exam-schedules/sessions/${sessionId}`)
  return response.data
}

export async function fetchMyExamSchedule(params = {}) {
  const response = await api.get('/exam-schedules/my-schedule', { params })
  return response.data
}
