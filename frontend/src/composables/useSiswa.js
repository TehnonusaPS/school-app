import { ref } from 'vue'
import { fetchAllSiswa } from '@/services/siswaService'

/**
 * COMPOSABLE: Logika Reaktif (UI Logic).
 * Menggunakan ref, computed, dll.
 * Fokus: Mengelola status data (Loading, Error, Data).
 */
export function useSiswa() {
  const listSiswa = ref([])
  const isLoading = ref(false)
  const errorMessage = ref(null)

  const loadData = async () => {
    isLoading.value = true
    errorMessage.value = null
    
    try {
      // Memanggil Service
      const result = await fetchAllSiswa()
      listSiswa.value = result
    } catch (err) {
      errorMessage.value = err.message
    } finally {
      isLoading.value = false
    }
  }

  return {
    listSiswa,
    isLoading,
    errorMessage,
    loadData
  }
}
