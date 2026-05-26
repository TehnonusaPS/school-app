import { ref } from 'vue'
import { fetchAllSiswa } from '@/services/siswaService'

/**
 * COMPOSABLE UMUM: useApiExample
 * Sekarang ia tidak punya URL hardcoded.
 * Ia menerima 'apiFunction' (fungsi dari Service) sebagai argumen.
 */
export function useApiExample(apiFunction = fetchAllSiswa) {
  const data = ref(null)
  const error = ref(null)
  const isLoading = ref(false)

  const execute = async (...args) => {
    isLoading.value = true
    error.value = null

    try {
      // Menjalankan fungsi service yang dikirim
      data.value = await apiFunction(...args)
    } catch (err) {
      error.value = err.message
    } finally {
      isLoading.value = false
    }
  }

  return { data, error, isLoading, execute }
}
