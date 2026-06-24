import { computed, ref, unref, watch } from 'vue'

export function usePagination(items, perPage = 10) {
  const currentPage = ref(1)

  // Mendukung perPage sebagai plain number ATAU reactive ref
  const pp = () => unref(perPage)

  // Reset ke halaman 1 jika perPage berubah
  watch(
    () => unref(perPage),
    () => {
      currentPage.value = 1
    }
  )

  const total = computed(() => items.value.length)
  const from = computed(() => (currentPage.value - 1) * pp() + 1)
  const to = computed(() => Math.min(currentPage.value * pp(), total.value))
  const totalPages = computed(() => Math.ceil(total.value / pp()))
  const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * pp()
    return items.value.slice(start, start + pp())
  })

  return { currentPage, total, from, to, totalPages, paginatedItems }
}