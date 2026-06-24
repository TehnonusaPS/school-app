import { BookOpen } from 'lucide-vue-next'

/**
 * Icon untuk header GradeDetailCard
 */
export const gradeDetailIcon = BookOpen

/**
 * Kategori nilai dengan progress bar dan detail item.
 * Setiap kategori berisi: label, bobot, skor (dari 100), dan sub-items.
 */
export const gradeDetailCategories = [
  {
    label: 'Tugas',
    weight: 40,
    score: 92,
    maxScore: 100,
    items: [
      { label: 'Tugas I : Bab 1 - Lorem Ipsum', value: 95 },
      { label: 'Tugas II : Bab 2 - Lorem Ipsum', value: 89 }
    ]
  },
  {
    label: 'Ujian',
    weight: 60,
    score: 85,
    maxScore: 100,
    items: [
      { label: 'Ujian Harian I', value: 85 },
      { label: 'UTS Semester Ganjil', value: 88 }
    ]
  }
]

/**
 * Data untuk ScoreCircleCard rata-rata nilai.
 */
export const averageScoreData = {
  title: 'Rata-rata Nilai Ujian Matematika',
  score: '82,5',
  label: 'Cukup Baik !',
  description: 'Anda berada di 5% teratas kelas Matematika',
  percentage: 82.5,
  variant: 'purple'
}

/**
 * Data untuk ScoreCircleCard persentase kehadiran.
 */
export const attendanceData = {
  title: 'Rata-Rata Nilai Tugas Matematika',
  score: '98%',
  label: 'Sangat Baik !',
  description: 'Anda berada di 2% teratas kelas Matematika',
  percentage: 98,
  variant: 'amber'
}
