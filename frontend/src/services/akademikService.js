import api from './api'

// ─── Data Lookup ──────────────────────────────────────
export async function getMySubjects() {
  const res = await api.get('/akademik/my-subjects')
  return res.data
}

export async function getMyClassrooms(subjectId) {
  const res = await api.get(`/akademik/subjects/${subjectId}/my-classrooms`)
  return res.data
}

export async function getStudentsByClassroom(classroomId) {
  const res = await api.get(`/akademik/classrooms/${classroomId}/students`)
  return res.data
}

export async function getActiveAcademicYear() {
  const res = await api.get('/akademik/active-academic-year')
  return res.data
}

// ─── Materi Pelajaran ─────────────────────────────────
export async function getMaterials(params) {
  const res = await api.get('/akademik/materials', { params })
  return res.data
}

export async function createMaterial(formData) {
  const res = await api.post('/akademik/materials', formData)
  return res.data
}

export async function updateMaterial(id, formData) {
  formData.append('_method', 'PUT')
  const res = await api.post(`/akademik/materials/${id}`, formData)
  return res.data
}

export async function deleteMaterial(id) {
  const res = await api.delete(`/akademik/materials/${id}`)
  return res.data
}

export async function downloadMaterial(id) {
  const res = await api.get(`/akademik/materials/${id}/download`, {
    responseType: 'blob'
  })
  return res
}

export async function toggleMaterialStatus(id) {
  const res = await api.patch(`/akademik/materials/${id}/toggle-status`)
  return res.data
}

// ─── Penilaian (Tugas & Ujian) ────────────────────────
export async function getAssessments(params) {
  const res = await api.get('/akademik/assessments', { params })
  return res.data
}

export async function createAssessment(data) {
  const res = await api.post('/akademik/assessments', data)
  return res.data
}

export async function getAssessment(id) {
  const res = await api.get(`/akademik/assessments/${id}`)
  return res.data
}

export async function updateAssessment(id, data) {
  const res = await api.put(`/akademik/assessments/${id}`, data)
  return res.data
}

export async function deleteAssessment(id) {
  const res = await api.delete(`/akademik/assessments/${id}`)
  return res.data
}

export async function toggleAssessmentStatus(id) {
  const res = await api.patch(`/akademik/assessments/${id}/toggle-status`)
  return res.data
}
