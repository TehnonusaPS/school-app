import { ref, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { fetchIndonesianHolidays } from '@/services/api/nagerDate';
import { mockExams, mockAssignments } from '../data/jadwalData';
import { getMySchedule, getStudentSchedule } from '@/services/scheduleService';
import { fetchParentSchedule, fetchPublicEvents } from '@/services/academicCalendarService';

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

  // Calendar events (database academic calendar)
  const calendarEvents = ref([]);
  const isLoadingCalendarEvents = ref(false);

  // Weekly schedule from API
  const apiScheduleData = ref({});
  const isLoadingSchedule = ref(false);

  // Parent child selection state
  const parentChildren = ref([]);
  const selectedChildId = ref(null);

  const fetchSchedule = async () => {
    isLoadingSchedule.value = true;
    try {
      let res;
      if (role.value === 'guru' || role.value === 'wali_kelas') {
        res = await getMySchedule();
        apiScheduleData.value = res.data;
      } else if (role.value === 'orang_tua') {
        res = await fetchParentSchedule({ child_id: selectedChildId.value || undefined });
        apiScheduleData.value = res.data?.schedule || {};
        parentChildren.value = res.data?.children || [];
        if (res.data?.selected_child && !selectedChildId.value) {
          selectedChildId.value = res.data.selected_child.id;
        }
      } else {
        res = await getStudentSchedule();
        apiScheduleData.value = res.data;
      }
    } catch (e) {
      console.error('Failed to load schedule from API', e);
    } finally {
      isLoadingSchedule.value = false;
    }
  };

  const fetchCalendar = async () => {
    isLoadingCalendarEvents.value = true;
    try {
      const res = await fetchPublicEvents();
      calendarEvents.value = res.data || [];
    } catch (e) {
      console.error('Failed to load academic calendar events', e);
    } finally {
      isLoadingCalendarEvents.value = false;
    }
  };

  watch(role, () => {
    fetchSchedule();
    fetchCalendar();
  }, { immediate: true });

  watch(selectedChildId, (newId) => {
    if (role.value === 'orang_tua' && newId) {
      fetchSchedule();
    }
  });

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
    fetchCalendar();
  }, { immediate: true });

  // Get active schedule list for the current role
  const scheduleData = computed(() => {
    return apiScheduleData.value || {};
  });

  const isDateInBetween = (dateStr, startStr, endStr) => {
    return dateStr >= startStr && dateStr <= endStr;
  };

  // Check if a specific date string is a Sunday or National Holiday
  const getHolidayForDate = (dateStr) => {
    // 1. Check custom DB calendar events (priority)
    const dbEvent = calendarEvents.value.find(e => 
      ['libur_nasional', 'libur_semester', 'libur_khusus', 'tanggal_merah'].includes(e.type) && 
      isDateInBetween(dateStr, e.startDate, e.endDate)
    );
    if (dbEvent) {
      return {
        date: dateStr,
        localName: dbEvent.title,
        name: dbEvent.title
      };
    }

    // 2. Check National Holidays
    const holiday = holidays.value.find(h => h.date === dateStr);
    if (holiday) return holiday;

    // 3. Check Sunday (Day 0)
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
    return mockAssignments.filter(task => task.tanggal === dateStr);
  };

  // Combine everything for a specific date
  const getDetailsForDate = (date) => {
    const dateStr = formatDateISO(date);
    const holiday = getHolidayForDate(dateStr);
    const lessons = getLessonsForDate(dateStr);
    const exams = getExamsForDate(dateStr);
    const assignments = getAssignmentsForDate(dateStr);
    const dbEvents = calendarEvents.value.filter(e => isDateInBetween(dateStr, e.startDate, e.endDate));

    return {
      dateStr,
      dateObj: date,
      holiday,
      lessons,
      exams,
      assignments,
      calendarEvents: dbEvents,
      hasEvent: !!holiday || exams.length > 0 || assignments.length > 0 || dbEvents.length > 0
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

    // Add database calendar events
    calendarEvents.value.forEach(e => {
      if (e.startDate >= todayStr) {
        let sub = 'Kegiatan Sekolah';
        if (['libur_nasional', 'libur_semester', 'libur_khusus', 'tanggal_merah'].includes(e.type)) sub = 'Hari Libur';
        else if (['uts', 'uas', 'us', 'anbk', 'ujian'].includes(e.type)) sub = 'Ujian / Asesmen';
        else if (['mpls', 'rapor', 'remedi', 'rapat_guru'].includes(e.type)) sub = 'Agenda Akademik';
        
        list.push({
          id: `db-event-${e.id}`,
          tanggal: e.startDate,
          type: e.type,
          title: e.title,
          subtitle: sub + (e.classroom_name && e.classroom_name !== 'Semua Kelas' ? ` (${e.classroom_name})` : ''),
          time: e.startDate === e.endDate ? 'Satu Hari' : `${e.startDate} s.d ${e.endDate}`,
          location: e.description || ''
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
    const lessons = getLessonsForDate(dateStr);
    const dbEvents = calendarEvents.value.filter(e => isDateInBetween(dateStr, e.startDate, e.endDate));

    return {
      isHoliday: !!holiday || dbEvents.some(e => ['libur_nasional', 'libur_semester', 'libur_khusus', 'tanggal_merah'].includes(e.type)),
      isSunday: new Date(dateStr).getDay() === 0,
      isExam: exams.length > 0 || dbEvents.some(e => ['uts', 'uas', 'us', 'anbk', 'ujian'].includes(e.type)),
      isAssignment: assignments.length > 0,
      isLesson: lessons.length > 0,
      isActivity: dbEvents.some(e => ['kegiatan', 'p5', 'mpls', 'rapor', 'remedi', 'rapat_guru'].includes(e.type))
    };
  };

  return {
    role,
    selectedDate,
    visibleMonth,
    visibleYear,
    holidays,
    isLoadingHolidays,
    calendarEvents,
    isLoadingCalendarEvents,
    parentChildren,
    selectedChildId,
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
