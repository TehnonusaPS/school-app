import api from './api'

export async function getMyClassrooms() {
  const res = await api.get('/siswa/akademik/my-classrooms')
  return res.data
}

export async function getSubjects(classroomId) {
  const res = await api.get('/siswa/akademik/subjects', {
    params: { classroom_id: classroomId }
  })
  return res.data
}

export async function getSubjectOverview(subjectId, classroomId) {
  const res = await api.get('/siswa/akademik/overview', {
    params: {
      subject_id: subjectId,
      classroom_id: classroomId
    }
  })
  return res.data
}

export async function getGlobalStats(classroomId) {
  const res = await api.get('/siswa/akademik/stats', {
    params: { classroom_id: classroomId }
  })
  return res.data
}

export async function downloadMaterial(id) {
  const res = await api.get(`/siswa/akademik/materials/${id}/download`, {
    responseType: 'blob'
  })
  return res
}
