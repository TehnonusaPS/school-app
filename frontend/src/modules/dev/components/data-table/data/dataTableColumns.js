/**
 * Prop kolom yang tersedia:
 *   key          : nama field dari data item
 *   label        : teks header
 *   type         : 'number' | 'date' | 'code' | 'muted'
 *   truncate     : true → teks dipotong otomatis
 *   avatar       : true → render Avatar + teks
 *   avatarKey    : nama field URL foto dari backend (fallback ke inisial jika null)
 *   badge        : true → render Badge
 *   badgeVariant : string | object | (value) => variant
 */
export const tableColumns = [
  {
    key: 'nama',
    label: 'Nama Siswa',
    avatar: true,
    avatarKey: 'foto'
  },
  {
    key: 'email',
    label: 'Email',
    type: 'muted',
    truncate: true
  },
  {
    key: 'kelas',
    label: 'Kelas'
  },
  {
    key: 'nisn',
    label: 'NISN',
    type: 'code'
  },
  {
    key: 'status',
    label: 'Status',
    badge: true,
    badgeVariant: {
      Aktif: 'green',
      Nonaktif: 'gray',
      Pending: 'amber',
      Alumni: 'blue'
    }
  },
  {
    key: 'nilai',
    label: 'Nilai',
    type: 'number'
  },
  {
    key: 'tanggal_masuk',
    label: 'Tgl. Masuk',
    type: 'date'
  },
  {
    key: 'alamat',
    label: 'Alamat',
    type: 'muted',
    truncate: true
  },
  {
    key: 'actions',
    label: 'Aksi'
  }
]

export const tableFilters = [
  {
    type: 'search',
    key: 'search',
    placeholder: 'Cari nama atau email...'
  },
  {
    type: 'select',
    key: 'kelas',
    label: 'Kelas:',
    placeholder: 'Semua Kelas',
    options: [
      { label: 'Kelas 10', value: '10' },
      { label: 'Kelas 11', value: '11' },
      { label: 'Kelas 12', value: '12' }
    ]
  },
  {
    type: 'select',
    key: 'status',
    label: 'Status:',
    placeholder: 'Semua Status',
    options: [
      { label: 'Aktif', value: 'Aktif' },
      { label: 'Nonaktif', value: 'Nonaktif' },
      { label: 'Pending', value: 'Pending' },
      { label: 'Alumni', value: 'Alumni' }
    ]
  }
]
