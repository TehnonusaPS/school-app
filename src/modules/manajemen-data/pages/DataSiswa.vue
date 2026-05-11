<script setup>
import { onMounted } from 'vue'
import { useApiExample } from '@/composables/useApiExample'
import { fetchAllSiswa } from '@/services/siswaService'

// Shadcn UI Components
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import { RefreshCw, UserPlus } from 'lucide-vue-next'

// Inisialisasi Composable dengan Service
const { data: students, error, isLoading, execute } = useApiExample(fetchAllSiswa)

onMounted(() => {
  execute()
})
</script>

<template>
  <div class="space-y-6 p-1">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Data Siswa</h1>
        <p class="text-muted-foreground">Kelola informasi data siswa sekolah Anda di sini.</p>
      </div>
      <div class="flex items-center gap-2">
        <Button variant="outline" @click="execute" :disabled="isLoading">
          <RefreshCw class="mr-2 h-4 w-4" :class="{ 'animate-spin': isLoading }" />
          Refresh
        </Button>
        <Button @click="$router.push('/manajemen-data/siswa/tambah')">
          <UserPlus class="mr-2 h-4 w-4" />
          Tambah Siswa
        </Button>
      </div>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>Daftar Siswa Aktif</CardTitle>
        <CardDescription>
          Menampilkan total {{ students?.length || 0 }} siswa yang terdaftar di sistem.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <!-- State: Error -->
        <div
          v-if="error"
          class="bg-destructive/10 p-4 rounded-lg text-destructive text-center mb-4"
        >
          Gagal memuat data: {{ error }}
        </div>

        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="w-[100px]">ID</TableHead>
                <TableHead>Nama Lengkap</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Kota</TableHead>
                <TableHead class="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <!-- State: Loading (Skeletons) -->
              <template v-if="isLoading">
                <TableRow v-for="i in 5" :key="i">
                  <TableCell><Skeleton class="h-4 w-8" /></TableCell>
                  <TableCell><Skeleton class="h-4 w-32" /></TableCell>
                  <TableCell><Skeleton class="h-4 w-40" /></TableCell>
                  <TableCell><Skeleton class="h-4 w-20" /></TableCell>
                  <TableCell class="text-right"><Skeleton class="h-8 w-16 ml-auto" /></TableCell>
                </TableRow>
              </template>

              <!-- State: Success -->
              <template v-else>
                <TableRow v-for="student in students" :key="student.id">
                  <TableCell class="font-mono text-xs text-muted-foreground"
                    >#{{ student.id }}</TableCell
                  >
                  <TableCell class="font-medium">{{ student.name }}</TableCell>
                  <TableCell>{{ student.email }}</TableCell>
                  <TableCell>{{ student.address.city }}</TableCell>
                  <TableCell class="text-right">
                    <Button variant="ghost" size="sm">Detail</Button>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
