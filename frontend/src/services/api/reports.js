import api from '../api'

// Yayasan Reports
export const getFoundationConsolidation = async () => {
    const response = await api.get('/reports/foundation/consolidation')
    return response.data
}
export const getFoundationAcademic = async () => {
    const response = await api.get('/reports/foundation/academic')
    return response.data
}
export const getFoundationInfrastructure = async () => {
    const response = await api.get('/reports/foundation/infrastructure')
    return response.data
}
export const getFoundationFinance = async () => {
    const response = await api.get('/reports/foundation/finance')
    return response.data
}
export const getFoundationHR = async () => {
    const response = await api.get('/reports/foundation/hr')
    return response.data
}
export const getFoundationStudents = async () => {
    const response = await api.get('/reports/foundation/students')
    return response.data
}

// School Reports
export const getSchoolAttendance = async () => {
    const response = await api.get('/reports/school/attendance')
    return response.data
}
export const getSchoolAcademic = async () => {
    const response = await api.get('/reports/school/academic')
    return response.data
}
export const getSchoolFinance = async () => {
    const response = await api.get('/reports/school/finance')
    return response.data
}
export const getSchoolGrades = async () => {
    const response = await api.get('/reports/school/grades')
    return response.data
}
export const getSchoolStudentDevelopment = async () => {
    const response = await api.get('/reports/school/student-development')
    return response.data
}
export const getSchoolAccountability = async () => {
    const response = await api.get('/reports/school/accountability')
    return response.data
}
export const getSchoolStaff = async () => {
    const response = await api.get('/reports/school/staff')
    return response.data
}

export const getStudentAttendance = getSchoolAttendance
