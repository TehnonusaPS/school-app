<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Pencil, Trash2, Shield, Zap, HelpCircle } from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { toast } from 'vue-sonner'
import * as financeService from '@/services/superAdminFinanceService'

const plans = ref([])
const isLoading = ref(false)
const isModalOpen = ref(false)
const isEditing = ref(false)
const currentPlanId = ref(null)

const form = ref({
  name: '',
  code: '',
  price: 0,
  billing_cycle: 'monthly',
  max_schools: 1,
  max_students: 150,
  features: '',
  is_active: true
})

const loadPlans = async () => {
  isLoading.value = true
  try {
    const res = await financeService.getPlans()
    if (res.status === 'success') {
      plans.value = res.data
    }
  } catch (error) {
    toast.error('Gagal memuat paket langganan.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadPlans()
})

const openAddModal = () => {
  isEditing.value = false
  currentPlanId.value = null
  form.value = {
    name: '',
    code: '',
    price: 0,
    billing_cycle: 'monthly',
    max_schools: 1,
    max_students: 150,
    features: '',
    is_active: true
  }
  isModalOpen.value = true
}

const openEditModal = (plan) => {
  isEditing.value = true
  currentPlanId.value = plan.id
  form.value = {
    name: plan.name,
    code: plan.code,
    price: parseFloat(plan.price),
    billing_cycle: plan.billing_cycle,
    max_schools: plan.max_schools,
    max_students: plan.max_students,
    features: Array.isArray(plan.features) ? plan.features.join('\n') : '',
    is_active: plan.is_active
  }
  isModalOpen.value = true
}

const handleSave = async () => {
  if (!form.value.name || !form.value.code) {
    toast.error('Mohon isi nama dan kode paket!')
    return
  }

  const payload = {
    ...form.value,
    features: form.value.features ? form.value.features.split('\n').filter(Boolean) : []
  }

  try {
    if (isEditing.value) {
      await financeService.updatePlan(currentPlanId.value, payload)
      toast.success('Paket langganan berhasil diperbarui!')
    } else {
      await financeService.createPlan(payload)
      toast.success('Paket langganan baru berhasil dibuat!')
    }
    isModalOpen.value = false
    loadPlans()
  } catch (error) {
    toast.error('Gagal menyimpan paket langganan.', {
      description: error.message || 'Pastikan kode paket unik.'
    })
  }
}

const handleDelete = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus paket langganan ini?')) {
    try {
      await financeService.deletePlan(id)
      toast.success('Paket langganan berhasil dihapus!')
      loadPlans()
    } catch (error) {
      toast.error('Gagal menghapus paket langganan.', {
        description: error.message || 'Paket mungkin sedang aktif digunakan.'
      })
    }
  }
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(val)
}
</script>

<template>
  <div class="space-y-6 w-full text-left">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Manajemen Paket Langganan</h1>
        <p class="text-muted-foreground mt-2 text-sm">Kelola produk, harga, limitasi, dan fitur paket lisensi CerdasBangsa.</p>
      </div>
      <Button @click="openAddModal" class="h-10 bg-primary text-primary-foreground font-semibold flex items-center gap-2">
        <Plus class="w-4 h-4" /> Buat Paket Baru
      </Button>
    </div>

    <!-- plans list -->
    <Card>
      <Table>
        <TableHeader class="bg-muted/50">
          <TableRow>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground w-[60px] text-center">NO</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">PAKET (CODE)</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">TARIF</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">BILLING CYCLE</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">LIMITASI</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">STATUS</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">AKSI</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(plan, index) in plans" :key="plan.id">
            <TableCell class="text-center font-medium text-muted-foreground">
              {{ index + 1 }}
            </TableCell>
            <TableCell>
              <div>
                <div class="font-bold text-foreground">{{ plan.name }}</div>
                <div class="text-xs text-muted-foreground font-mono">{{ plan.code }}</div>
              </div>
            </TableCell>
            <TableCell>
              <div class="font-bold text-foreground">{{ formatCurrency(plan.price) }}</div>
            </TableCell>
            <TableCell>
              <Badge variant="outline" class="uppercase text-[10px]">{{ plan.billing_cycle }}</Badge>
            </TableCell>
            <TableCell>
              <div class="text-xs space-y-0.5 text-muted-foreground font-medium">
                <div>Max Unit: <strong class="text-foreground">{{ plan.max_schools }}</strong></div>
                <div>Max Siswa: <strong class="text-foreground">{{ plan.max_students }}</strong></div>
              </div>
            </TableCell>
            <TableCell>
              <Badge :variant="plan.is_active ? 'default' : 'secondary'">
                {{ plan.is_active ? 'Aktif' : 'Non-Aktif' }}
              </Badge>
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3 text-muted-foreground">
                <button @click="openEditModal(plan)" class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
                <button @click="handleDelete(plan.id)" class="hover:text-destructive"><Trash2 class="w-4 h-4" /></button>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-if="plans.length === 0">
            <TableCell colspan="7" class="text-center py-8 text-muted-foreground font-medium">
              {{ isLoading ? 'Memuat data paket...' : 'Tidak ada paket langganan yang tersedia.' }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>

    <!-- Modal Form (Simple Overlay) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <Card class="w-full max-w-lg bg-card border-border overflow-hidden">
        <CardHeader class="border-b pb-4">
          <CardTitle class="text-xl font-bold">{{ isEditing ? 'Edit Paket Langganan' : 'Buat Paket Langganan Baru' }}</CardTitle>
        </CardHeader>
        <CardContent class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Nama Paket</label>
              <input v-model="form.name" type="text" placeholder="Contoh: Paket Premium" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Kode Paket (Unique Code)</label>
              <input v-model="form.code" type="text" placeholder="Contoh: plan_premium" :disabled="isEditing" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary disabled:opacity-50" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Harga Paket (IDR)</label>
              <input v-model.number="form.price" type="number" placeholder="0" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Siklus Penagihan</label>
              <select v-model="form.billing_cycle" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary">
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
                <option value="lifetime">Sekali Bayar (Lifetime)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Batas Maks Unit Sekolah</label>
              <input v-model.number="form.max_schools" type="number" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-muted-foreground uppercase">Batas Maks Siswa</label>
              <input v-model.number="form.max_students" type="number" class="w-full h-10 px-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary" />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-xs font-bold text-muted-foreground uppercase">Fitur Unggulan (Satu fitur per baris)</label>
            <textarea v-model="form.features" placeholder="Contoh:&#10;Akses modul Akademik lengkap&#10;WhatsApp Notifikasi Otomatis" rows="4" class="w-full p-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:border-primary resize-none"></textarea>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <input v-model="form.is_active" type="checkbox" id="isActive" class="w-4 h-4 rounded text-primary border-border" />
            <label for="isActive" class="text-sm font-semibold select-none cursor-pointer">Aktifkan dan Publikasikan Paket Ini</label>
          </div>

          <div class="border-t border-border pt-4 flex justify-end gap-3">
            <Button variant="outline" @click="isModalOpen = false">Batal</Button>
            <Button @click="handleSave">Simpan</Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
