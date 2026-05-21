<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'

const sekolah = [
  { nama: 'SDN 01 Sentosa',     kepala: 'Drs. Ahmad Fauzi',      jenjang: 'SD',   akreditasi: 'A', kelas: 18, siswa: 100 },
  { nama: 'SMPN 02 Harapan',    kepala: 'Siti Rahayu, M.Pd',     jenjang: 'SMP',  akreditasi: 'B', kelas: 9,  siswa: 30  },
  { nama: 'SMAN 03 Bangsa',     kepala: 'H. Bambang Irawan',     jenjang: 'SMA',  akreditasi: 'A', kelas: 24, siswa: 300 },
  { nama: 'SMK 04 Mandiri',     kepala: 'Dra. Lestari Dewi',     jenjang: 'SMK',  akreditasi: 'A', kelas: 20, siswa: 200 },
  { nama: 'MTs Al-Hidayah',     kepala: 'Ust. Mukhlis, S.Ag',   jenjang: 'MTs',  akreditasi: 'B', kelas: 9,  siswa: 90  },
  { nama: 'SD Islam Terpadu',   kepala: 'Nur Aini, S.Pd',        jenjang: 'SD',   akreditasi: 'A', kelas: 12, siswa: 145 },
  { nama: 'SMP Cendekia',       kepala: 'Agus Prasetyo, M.Pd',  jenjang: 'SMP',  akreditasi: 'A', kelas: 12, siswa: 210 },
  { nama: 'MA Al-Furqon',       kepala: 'Kiai Hasan Basri',      jenjang: 'MA',   akreditasi: 'B', kelas: 9,  siswa: 175 },
  { nama: 'SMK Teknologi Maju', kepala: 'Ir. Dwi Santoso',       jenjang: 'SMK',  akreditasi: 'A', kelas: 21, siswa: 260 },
  { nama: 'SMA Bina Insani',    kepala: 'Dr. Ratna Sari, M.Ed', jenjang: 'SMA',  akreditasi: 'A', kelas: 27, siswa: 320 }
]

const sdm = [
  { label: 'Guru Tetap',   value: 45, persen: 44 },
  { label: 'Guru Honorer', value: 38, persen: 37 },
  { label: 'Staff',        value: 19, persen: 19 }
]

const quickStats = [
  { label: 'Total SDM',        value: '102' },
  { label: 'Sudah Sertifikasi', value: '67'  },
  { label: 'Rasio Guru:Siswa', value: '1:25' },
  { label: 'Rata-rata Masa Kerja', value: '8 thn' }
]
</script>

<template>
  <div class="grid gap-4 lg:grid-cols-5">
    <!-- Tabel Sekolah -->
    <Card class="lg:col-span-3">
      <CardHeader class="pb-3">
        <CardTitle class="text-base font-semibold">Daftar Sekolah</CardTitle>
        <CardDescription class="text-xs">
          Sekolah yang terdaftar di bawah yayasan ini
        </CardDescription>
      </CardHeader>
      <CardContent class="p-0">
        <!-- Header fixed — tidak ikut scroll -->
        <table class="w-full text-sm border-b">
          <colgroup>
            <col style="width: 28%" />
            <col style="width: 26%" />
            <col style="width: 12%" />
            <col style="width: 12%" />
            <col style="width: 10%" />
            <col style="width: 12%" />
          </colgroup>
          <thead>
            <tr class="border-b bg-muted/50">
              <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Nama Sekolah</th>
              <th class="py-3 text-left text-xs font-medium text-muted-foreground">Kepala Sekolah</th>
              <th class="py-3 text-center text-xs font-medium text-muted-foreground">Jenjang</th>
              <th class="py-3 text-center text-xs font-medium text-muted-foreground">Akreditasi</th>
              <th class="py-3 text-right text-xs font-medium text-muted-foreground">Kelas</th>
              <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Siswa</th>
            </tr>
          </thead>
        </table>

        <!-- Body scrollable -->
        <div class="h-[272px] overflow-y-auto">
          <table class="w-full text-sm">
            <colgroup>
              <col style="width: 28%" />
              <col style="width: 26%" />
              <col style="width: 12%" />
              <col style="width: 12%" />
              <col style="width: 10%" />
              <col style="width: 12%" />
            </colgroup>
            <tbody>
              <tr
                v-for="(s, i) in sekolah"
                :key="i"
                class="border-b transition-colors hover:bg-muted/50 cursor-default"
              >
                <td class="pl-6 py-3 font-medium text-sm">{{ s.nama }}</td>
                <td class="py-3 text-muted-foreground text-sm">{{ s.kepala }}</td>
                <td class="py-3 text-center">
                  <Badge variant="outline" class="text-xs font-semibold">{{ s.jenjang }}</Badge>
                </td>
                <td class="py-3 text-center">
                  <Badge
                    :variant="s.akreditasi === 'A' ? 'default' : 'secondary'"
                    class="text-xs font-bold"
                  >
                    {{ s.akreditasi }}
                  </Badge>
                </td>
                <td class="py-3 text-right text-sm text-muted-foreground">{{ s.kelas }}</td>
                <td class="pr-6 py-3 text-right">
                  <Badge variant="secondary" class="font-mono text-xs">
                    {{ s.siswa.toLocaleString('id-ID') }}
                  </Badge>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Distribusi SDM -->
    <Card class="lg:col-span-2">
      <CardHeader class="pb-3">
        <CardTitle class="text-base font-semibold">Distribusi SDM</CardTitle>
        <CardDescription class="text-xs">
          Komposisi tenaga pendidik & staff
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <!-- Progress bars distribusi -->
        <div v-for="item in sdm" :key="item.label" class="space-y-1.5">
          <div class="flex items-center justify-between text-sm">
            <span class="font-medium">{{ item.label }}</span>
            <span class="text-muted-foreground text-xs">
              {{ item.value }} orang &bull; {{ item.persen }}%
            </span>
          </div>
          <Progress :model-value="item.persen" class="h-2" />
        </div>

        <!-- Divider -->
        <div class="border-t pt-4">
          <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Ringkasan</p>
          <div class="grid grid-cols-2 gap-3">
            <div
              v-for="stat in quickStats"
              :key="stat.label"
              class="rounded-lg bg-muted/50 p-3 space-y-0.5"
            >
              <p class="text-[10px] text-muted-foreground leading-tight">{{ stat.label }}</p>
              <p class="text-sm font-bold">{{ stat.value }}</p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
