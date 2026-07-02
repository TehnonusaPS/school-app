import api from './api'

/**
 * Fetch all feedbacks for the authenticated user.
 */
export async function getFeedbacks() {
  const response = await api.get('/feedbacks')
  return response.data.data
}

/**
 * Create a new feedback aduan.
 */
export async function createFeedback(payload) {
  const response = await api.post('/feedbacks', payload)
  return response.data.data
}
