import { computed, ref } from 'vue'

export function usePagination(items, perPage = 10) {
  const currentPage = ref(1)

  const total = computed(() => items.value.length)

  const from = computed(() => {
    return (currentPage.value - 1) * perPage + 1
  })

  const to = computed(() => {
    return Math.min(currentPage.value * perPage, total.value)
  })

  const totalPages = computed(() => {
    return Math.ceil(total.value / perPage)
  })

  const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * perPage
    const end = start + perPage

    return items.value.slice(start, end)
  })

  return {
    currentPage,
    total,
    from,
    to,
    totalPages,
    paginatedItems
  }
}