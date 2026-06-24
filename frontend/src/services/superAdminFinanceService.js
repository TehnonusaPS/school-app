import api from './api'

/**
 * Get dashboard statistics and recent activities.
 */
export async function getDashboard() {
  const response = await api.get('/superadmin/finance/dashboard')
  return response.data
}

/**
 * List all foundations.
 */
export async function getFoundations() {
  const response = await api.get('/superadmin/finance/foundations')
  return response.data
}

/**
 * List all subscription plans.
 */
export async function getPlans() {
  const response = await api.get('/superadmin/finance/plans')
  return response.data
}

/**
 * Create a new subscription plan.
 */
export async function createPlan(data) {
  const response = await api.post('/superadmin/finance/plans', data)
  return response.data
}

/**
 * Get a specific subscription plan's detail.
 */
export async function getPlan(id) {
  const response = await api.get(`/superadmin/finance/plans/${id}`)
  return response.data
}

/**
 * Update an existing subscription plan.
 */
export async function updatePlan(id, data) {
  const response = await api.put(`/superadmin/finance/plans/${id}`, data)
  return response.data
}

/**
 * Delete a subscription plan.
 */
export async function deletePlan(id) {
  const response = await api.delete(`/superadmin/finance/plans/${id}`)
  return response.data
}

/**
 * Delete a subscription.
 */
export async function deleteSubscription(id) {
  const response = await api.delete(`/superadmin/finance/subscriptions/${id}`)
  return response.data
}

/**
 * List subscriptions with optional parameters (status, search, page).
 */
export async function getSubscriptions(params) {
  const response = await api.get('/superadmin/finance/subscriptions', { params })
  return response.data
}

/**
 * List payment invoices with optional parameters (status, search, page).
 */
export async function getPayments(params) {
  const response = await api.get('/superadmin/finance/payments', { params })
  return response.data
}

/**
 * Create a new pending payment/invoice manually.
 */
export async function createInvoice(data) {
  const response = await api.post('/superadmin/finance/payments', data)
  return response.data
}

/**
 * Verify a pending payment receipt (approve or reject).
 */
export async function verifyPayment(id, data) {
  const response = await api.post(`/superadmin/finance/payments/${id}/verify`, data)
  return response.data
}
