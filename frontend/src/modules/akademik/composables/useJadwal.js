import { ref, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { fetchIndonesianHolidays } from '@/services/api/nagerDate';
import { mockScheduleSiswa, mockScheduleGuru, mockExams, mockAssignments } from '../data/jadwalData';

// Helper to format Date to YYYY-MM-DD in local time
export function formatDateISO(date) {
  if (!date) return '';
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

export function useJadwal() {
  const authStore = useAuthStore();
  const role = computed(() => authStore.user?.role || 'siswa');

  // Selected date (default: today)
  const selectedDate = ref(new Date());

  // Visible month & year in calendar (default: today's month/year)
  const visibleMonth = ref(new Date().getMonth()); // 0-indexed (0 = Jan, 11 = Dec)
  const visibleYear = ref(new Date().getFullYear());

  // Holidays state
  const holidays = ref([]);
  const isLoadingHolidays = ref(false);

  // Fetch holidays for the visible year
  const loadHolidays = async (year) => {
    isLoadingHolidays.value = true;
    try {
      const fetched = await fetchIndonesianHolidays(year);
      holidays.value = fetched;
    } catch (e) {
      console.error(e);
    } finally {
      isLoadingHolidays.value = false;
    }
  };

  // Watch visible year to load new holidays
  watch(visibleYear, (newYear) => {
    loadHolidays(newYear);
  }, { immediate: true });

  // Get active schedule list for the current role
  const scheduleData = computed(() => {
    return role.value === 'guru' ? mockScheduleGuru : mockScheduleSiswa;
  });

  // Check if a specific date string is a Sunday or National Holiday
  const getHolidayForDate = (dateStr) => {
    // 1. Check National Holidays
    const holiday = holidays.value.find(h => h.date === dateStr);
    if (holiday) return holiday;

    // 2. Check Sunday (Day 0)
    const dateObj = new Date(dateStr);
    if (dateObj.getDay() === 0) {
      return {
        date: dateStr,
        localName: 'Hari Minggu',
        name: 'Sunday'
      };
    }

    return null;
  };

  // Get lesson schedules for a date based on day of week (1-6)
  const getLessonsForDate = (dateStr) => {
    const holiday = getHolidayForDate(dateStr);
    // If it's Sunday or a national holiday, typically there are no regular classes
    if (holiday && holiday.localName !== 'Hari Minggu') {
      return [];
    }

    const dateObj = new Date(dateStr);
    const dayOfWeek = dateObj.getDay(); // 0: Sunday, 1: Monday, ..., 6: Saturday
    if (dayOfWeek === 0) return []; // Sunday has no lessons

    return scheduleData.value[dayOfWeek] || [];
  };

  // Get exams on a specific date
  const getExamsForDate = (dateStr) => {
    return mockExams.filter(exam => exam.tanggal === dateStr);
  };

  // Get assignments on a specific date
  const getAssignmentsForDate = (dateStr) => {
    // Assignments are typically only visible/relevant for siswa or teachers to collect
    return mockAssignments.filter(task => task.tanggal === dateStr);
  };

  // Combine everything for a specific date
  const getDetailsForDate = (date) => {
    const dateStr = formatDateISO(date);
    const holiday = getHolidayForDate(dateStr);
    const lessons = getLessonsForDate(dateStr);
    const exams = getExamsForDate(dateStr);
    const assignments = getAssignmentsForDate(dateStr);

    return {
      dateStr,
      dateObj: date,
      holiday,
      lessons,
      exams,
      assignments,
      hasEvent: !!holiday || exams.length > 0 || assignments.length > 0
    };
  };

  // Computed details of the currently selected date
  const selectedDateDetails = computed(() => {
    return getDetailsForDate(selectedDate.value);
  });

  // Get all upcoming events (holidays, exams, assignments) from today onwards
  const upcomingEvents = computed(() => {
    const todayStr = formatDateISO(new Date());
    const list = [];

    // Add national holidays
    holidays.value.forEach(h => {
      if (h.date >= todayStr) {
        list.push({
          id: `holiday-${h.date}`,
          tanggal: h.date,
          type: 'holiday',
          title: h.localName,
          subtitle: 'Libur Nasional',
          time: 'Sepanjang Hari',
          location: ''
        });
      }
    });

    // Add exams
    mockExams.forEach(e => {
      if (e.tanggal >= todayStr) {
        list.push({
          id: `exam-${e.id}`,
          tanggal: e.tanggal,
          type: 'exam',
          title: `${e.nama} - ${e.mapel}`,
          subtitle: e.kelas,
          time: e.waktu,
          location: e.ruang
        });
      }
    });

    // Add assignments
    mockAssignments.forEach(a => {
      if (a.tanggal >= todayStr) {
        list.push({
          id: `assignment-${a.id}`,
          tanggal: a.tanggal,
          type: 'assignment',
          title: a.nama,
          subtitle: `${a.mapel} (${a.kelas})`,
          time: `Kumpul s.d. ${a.deadline}`,
          location: a.deskripsi
        });
      }
    });

    // Sort list chronologically by date
    return list.sort((a, b) => a.tanggal.localeCompare(b.tanggal));
  });

  // Custom function to return classes for a date (for badge markings)
  const getDateMarkers = (dateStr) => {
    const holiday = getHolidayForDate(dateStr);
    const exams = getExamsForDate(dateStr);
    const assignments = getAssignmentsForDate(dateStr);

    return {
      isHoliday: !!holiday,
      isSunday: new Date(dateStr).getDay() === 0,
      isExam: exams.length > 0,
      isAssignment: assignments.length > 0
    };
  };

  return {
    role,
    selectedDate,
    visibleMonth,
    visibleYear,
    holidays,
    isLoadingHolidays,
    selectedDateDetails,
    upcomingEvents,
    getHolidayForDate,
    getLessonsForDate,
    getExamsForDate,
    getAssignmentsForDate,
    getDetailsForDate,
    getDateMarkers,
    formatDateISO
  };
}
