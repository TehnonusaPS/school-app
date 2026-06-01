/**
 * Format angka menjadi format yang lebih mudah dibaca.
 * Contoh:
 * 1239 -> 1.2K
 * 500000 -> 500K
 * 1500000 -> 1.5M
 * Jika di bawah 1000, akan diformat dengan titik ribuan standar Indonesia.
 *
 * @param {number|string} num Angka yang akan diformat
 * @returns {string} Angka yang sudah diformat
 */
export const formatNumber = (num) => {
  const parsedNum = Number(num)
  if (isNaN(parsedNum)) return num

  if (parsedNum >= 1000000) {
    return (parsedNum / 1000000).toFixed(1).replace('.0', '') + 'M'
  }
  if (parsedNum >= 1000) {
    return (parsedNum / 1000).toFixed(1).replace('.0', '') + 'K'
  }
  
  return new Intl.NumberFormat('id-ID').format(parsedNum)
}

/**
 * Format angka perubahan (delta) dengan menambahkan tanda '+' untuk angka positif.
 * Angka negatif sudah otomatis memiliki tanda '-'.
 * 
 * @param {number|string} num Angka perubahan (contoh: pertumbuhan persen)
 * @returns {string} Angka dengan tanda + atau -
 */
export const formatDelta = (num) => {
  const parsedNum = Number(num)
  if (isNaN(parsedNum)) return num
  return parsedNum > 0 ? `+${parsedNum}` : `${parsedNum}`
}
