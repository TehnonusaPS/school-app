import api from './api'

// --- Time Slots API ---
export async function getTimeSlots() {
  const response = await api.get('/management/time-slots')
  return response.data
}

export async function saveTimeSlotsBulk(data) {
  const response = await api.post('/management/time-slots/bulk', { slots: data })
  return response.data
}

export async function deleteTimeSlot(id) {
  const response = await api.delete(`/management/time-slots/${id}`)
  return response.data
}

// --- Schedules API ---
export async function getSchedules(params) {
  const response = await api.get('/management/schedules', { params })
  return response.data
}

export async function createSchedule(data) {
  const response = await api.post('/management/schedules', data)
  return response.data
}

export async function createSchedulesBulk(data) {
  const response = await api.post('/management/schedules/bulk', data)
  return response.data
}

export async function updateSchedule(id, data) {
  const response = await api.put(`/management/schedules/${id}`, data)
  return response.data
}

export async function deleteSchedule(id) {
  const response = await api.delete(`/management/schedules/${id}`)
  return response.data
}

export async function getUnassignedSubjects(params) {
  const response = await api.get('/management/schedules/unassigned-subjects', { params })
  return response.data
}

export async function checkTeacherConflicts(params) {
  const response = await api.get('/management/schedules/teacher-conflicts', { params })
  return response.data
}

// --- Roles-specific schedule lookups ---
export async function getMySchedule() {
  const response = await api.get('/akademik/my-schedule')
  return response.data
}

export async function getStudentSchedule() {
  const response = await api.get('/siswa/akademik/schedule')
  return response.data
}
