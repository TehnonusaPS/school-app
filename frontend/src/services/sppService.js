import api from './api'

/**
 * Get student SPP dashboard statistics.
 */
export async function getSppDashboard() {
  const response = await api.get('/finance/spp/dashboard')
  return response.data
}

/**
 * Get active student bills list.
 */
export async function getStudentBills(studentId = null) {
  const params = studentId ? { student_id: studentId } : {}
  const response = await api.get('/finance/spp/bills', { params })
  return response.data
}

/**
 * Create a new payment/transaction (Student/Parent paying online or Admin manually processing).
 */
export async function createSppPayment(data) {
  const response = await api.post('/finance/spp/payments', data)
  return response.data
}

/**
 * Verify a pending student payment (Admin/Tata Usaha).
 */
export async function verifySppPayment(id, status, notes = '') {
  const response = await api.post(`/finance/spp/payments/${id}/verify`, { status, notes })
  return response.data
}

/**
 * Get list of SPP Tariffs.
 */
export async function getSppTariffs() {
  const response = await api.get('/finance/spp/tariffs')
  return response.data
}

/**
 * Create a new SPP Tariff.
 */
export async function storeSppTariff(data) {
  const response = await api.post('/finance/spp/tariffs', data)
  return response.data
}

/**
 * Update an existing SPP Tariff.
 */
export async function updateSppTariff(id, data) {
  const response = await api.put(`/finance/spp/tariffs/${id}`, data)
  return response.data
}

/**
 * Delete an SPP Tariff.
 */
export async function deleteSppTariff(id) {
  const response = await api.delete(`/finance/spp/tariffs/${id}`)
  return response.data
}
