import { ref, computed } from 'vue'

export function useCalendarGrid(events = ref([])) {
  const calendarMonths = ref([])
  const activeMonthIdx = ref(0)
  const selectedDateStr = ref('')

  function generateDynamicMonths(oddStartStr, evenEndStr) {
    const months = []
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

    const start = new Date(oddStartStr || '2026-07-05')
    start.setMonth(start.getMonth() - 1)
    start.setDate(1)

    const end = new Date(evenEndStr || '2027-06-18')
    end.setMonth(end.getMonth() + 1)

    const cur = new Date(start)
    while (cur <= end) {
      const year = cur.getFullYear()
      const monthIdx = cur.getMonth()
      months.push({
        year,
        monthVal: monthIdx,
        name: monthNames[monthIdx],
        label: `${monthNames[monthIdx]} ${year}`
      })
      cur.setMonth(cur.getMonth() + 1)
    }

    calendarMonths.value = months
    activeMonthIdx.value = months.length > 1 ? 1 : 0
  }

  const currentMonthObj = computed(() => {
    if (calendarMonths.value.length === 0) return { name: 'Juli', year: 2026, monthVal: 6, label: 'Juli 2026' }
    return calendarMonths.value[activeMonthIdx.value] || calendarMonths.value[0]
  })

  function isDateInBetween(dateStr, startStr, endStr) {
    if (!dateStr || !startStr || !endStr) return false
    return dateStr >= startStr && dateStr <= endStr
  }

  const calendarDays = computed(() => {
    const year = currentMonthObj.value.year
    const month = currentMonthObj.value.monthVal

    const firstDayOfMonth = new Date(year, month, 1)
    const lastDayOfMonth = new Date(year, month + 1, 0)

    let startDayOfWeek = firstDayOfMonth.getDay() - 1
    if (startDayOfWeek === -1) startDayOfWeek = 6

    const daysInMonth = lastDayOfMonth.getDate()
    const days = []

    const prevMonthLastDay = new Date(year, month, 0).getDate()
    for (let i = startDayOfWeek - 1; i >= 0; i--) {
      days.push({
        dayNumber: prevMonthLastDay - i,
        isCurrentMonth: false,
        dateStr: ''
      })
    }

    const currentEvents = Array.isArray(events.value) ? events.value : (events || [])
    for (let d = 1; d <= daysInMonth; d++) {
      const mStr = String(month + 1).padStart(2, '0')
      const dStr = String(d).padStart(2, '0')
      const dateStr = `${year}-${mStr}-${dStr}`

      const dayEvents = currentEvents.filter(e => isDateInBetween(dateStr, e.startDate, e.endDate))
      const isSunday = new Date(year, month, d).getDay() === 0

      days.push({
        dayNumber: d,
        isCurrentMonth: true,
        dateStr,
        isSunday,
        events: dayEvents
      })
    }

    const remaining = 42 - days.length
    for (let i = 1; i <= remaining; i++) {
      days.push({
        dayNumber: i,
        isCurrentMonth: false,
        dateStr: ''
      })
    }

    return days
  })

  function prevMonth() {
    if (activeMonthIdx.value > 0) activeMonthIdx.value--
  }

  function nextMonth() {
    if (activeMonthIdx.value < calendarMonths.value.length - 1) activeMonthIdx.value++
  }

  return {
    calendarMonths,
    activeMonthIdx,
    selectedDateStr,
    generateDynamicMonths,
    currentMonthObj,
    calendarDays,
    isDateInBetween,
    prevMonth,
    nextMonth
  }
}
