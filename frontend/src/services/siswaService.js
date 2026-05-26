/**
 * SERVICE: Murni logika Networking.
 * Tidak ada reaktivitas Vue di sini.
 * Fokus: Ambil data mentah dari API.
 */
export async function fetchAllSiswa() {
  const response = await fetch('https://jsonplaceholder.typicode.com/users')
  if (!response.ok) throw new Error('Gagal mengambil data dari server')
  return response.json()
}

export async function createSiswa(data) {
  const response = await fetch('https://jsonplaceholder.typicode.com/users', {
    method: 'POST',
    body: JSON.stringify(data),
    headers: { 'Content-Type': 'application/json' }
  })
  return response.json()
}
