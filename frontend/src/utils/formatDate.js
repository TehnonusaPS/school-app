import { getLocalTimeZone } from '@internationalized/date'

/**
 * Format objek tanggal menjadi teks bahasa Indonesia (tanggal bulan tahun).
 * Mendukung objek Date standar JavaScript dan objek DateValue dari @internationalized/date.
 * 
 * @param {Date|Object|null|undefined} dateVal 
 * @returns {string} Tanggal terformat atau string kosong
 */
export const formatDate = (dateVal) => {
  if (!dateVal) return ''
  
  // Menangani objek DateValue dari @internationalized/date (Radix/Reka UI)
  if (dateVal && typeof dateVal.toDate === 'function') {
    return dateVal.toDate(getLocalTimeZone()).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })
  }
  
  // Menangani objek Date bawaan JavaScript
  if (dateVal instanceof Date) {
    return dateVal.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })
  }
  
  return String(dateVal)
}
