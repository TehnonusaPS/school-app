<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/stores/authStore'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { CalendarDays, Coffee, FileText, Plus, AlertCircle } from 'lucide-vue-next'
import { submitLeaveRequest, getStaffLeaveRequests } from '@/services/api/absensi'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { glassFade, glassSlide, tableRowFade } from '@/config/motion'

// ─── STATE ──────────────────────────────────────────────────
const authStore = useAuthStore()
const isPageLoading = ref(true)
const isSubmitting = ref(false)
const isDialogOpen = ref(false)

const leaveList = ref([])

// Form Fields
const form = ref({
  type: 'Izin',
  start_date: '',
  end_date: '',
  reason: '',
})

// ─── METHODS ────────────────────────────────────────────────
async function loadLeaveRequests() {
  isPageLoading.value = true
  try {
    const response = await getStaffLeaveRequests()
    if (response.success) {
      leaveList.value = response.data
    }
  } catch (error) {
    console.error(error)
    toast.error('Gagal memuat riwayat pengajuan cuti/izin')
  } finally {
    isPageLoading.value = false
  }
}

async function handleLeaveSubmit() {
  if (!form.value.start_date || !form.value.end_date || !form.value.reason) {
    toast.error('Formulir tidak lengkap', { description: 'Semua bidang wajib diisi.' })
    return
  }

  isSubmitting.value = true
  try {
    const response = await submitLeaveRequest(form.value)
    if (response.success) {
      toast.success('Pengajuan Berhasil', { description: response.message })
      isDialogOpen.value = false
      // Reset form
      form.value = {
        type: 'Izin',
        start_date: '',
        end_date: '',
        reason: '',
      }
      loadLeaveRequests()
    }
  } catch (error) {
    console.error(error)
    const errorMsg = error.response?.data?.message || 'Gagal mengajukan izin/cuti.'
    toast.error('Pengajuan Gagal', { description: errorMsg })
  } finally {
    isSubmitting.value = false
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// ─── LIFECYCLE ──────────────────────────────────────────────
onMounted(() => {
  loadLeaveRequests()
})
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 animate-in fade-in duration-300 w-full pb-10"
  >
    <!-- ── Header ── -->
    <PageHeader
      title="Pengajuan Cuti / Izin Staff"
      description="Ajukan permintaan cuti atau izin ketidakhadiran kerja secara mandiri."
    />

    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold text-foreground">Daftar Pengajuan</h2>
      
      <!-- New Request Dialog -->
      <Dialog :open="isDialogOpen" @update:open="(val) => isDialogOpen = val">
        <DialogTrigger asChild>
          <Button class="gap-2">
            <Plus class="size-4" />
            Ajukan Izin/Cuti
          </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>Pengajuan Izin / Cuti Baru</DialogTitle>
            <DialogDescription>
              Silakan isi formulir di bawah ini dengan lengkap untuk mengajukan cuti atau izin.
            </DialogDescription>
          </DialogHeader>
          
          <div class="space-y-4 py-4">
            <!-- Tipe -->
            <div class="space-y-1">
              <Label for="leave-type">Tipe Pengajuan</Label>
              <Select v-model="form.type">
                <SelectTrigger id="leave-type" class="w-full">
                  <SelectValue placeholder="Pilih Tipe" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Izin">Izin (Sakit / Kepentingan)</SelectItem>
                  <SelectItem value="Cuti">Cuti (Tahunan / Melahirkan, dll)</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Start Date & End Date -->
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1">
                <Label for="start-date">Tanggal Mulai</Label>
                <Input 
                  id="start-date" 
                  type="date" 
                  v-model="form.start_date" 
                  :min="new Date().toISOString().split('T')[0]"
                />
              </div>
              <div class="space-y-1">
                <Label for="end-date">Tanggal Selesai</Label>
                <Input 
                  id="end-date" 
                  type="date" 
                  v-model="form.end_date" 
                  :min="form.start_date || new Date().toISOString().split('T')[0]"
                />
              </div>
            </div>

            <!-- Alasan -->
            <div class="space-y-1">
              <Label for="reason">Alasan Pengajuan</Label>
              <Textarea 
                id="reason" 
                placeholder="Tulis alasan lengkap pengajuan..." 
                v-model="form.reason"
                rows="4"
              />
            </div>
          </div>

          <DialogFooter>
            <Button variant="outline" @click="isDialogOpen = false" :disabled="isSubmitting">Batal</Button>
            <Button @click="handleLeaveSubmit" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></span>
              Kirim Pengajuan
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>

    <!-- History list -->
    <Card
      v-motion
      :initial="glassSlide.initial"
      :visible-once="{ ...glassSlide.visible, transition: { ...glassSlide.visible.transition, delay: 100 } }"
    >
      <CardContent class="p-0 overflow-auto">
        <Table>
          <TableHeader>
            <TableRow class="bg-muted/50 hover:bg-muted/50">
              <TableHead>Tipe</TableHead>
              <TableHead>Mulai Tanggal</TableHead>
              <TableHead>Hingga Tanggal</TableHead>
              <TableHead class="max-w-xs">Alasan</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Catatan Ditolak</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isPageLoading">
              <TableRow v-for="i in 3" :key="`skel-leave-${i}`">
                <TableCell><Skeleton class="h-5 w-16" /></TableCell>
                <TableCell><Skeleton class="h-5 w-24" /></TableCell>
                <TableCell><Skeleton class="h-5 w-24" /></TableCell>
                <TableCell><Skeleton class="h-5 w-40" /></TableCell>
                <TableCell><Skeleton class="h-6 w-20 rounded-full" /></TableCell>
                <TableCell><Skeleton class="h-5 w-32" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow 
                v-for="(leave, index) in leaveList" 
                :key="leave.id"
                v-motion
                :initial="tableRowFade.initial"
                :visible-once="{ ...tableRowFade.visible, transition: { ...tableRowFade.visible.transition, delay: index * 60 } }"
                class="hover:bg-muted/30 transition-colors"
              >
                <TableCell class="font-bold">
                  <Badge 
                    :variant="leave.type === 'Cuti' ? 'default' : 'secondary'"
                    :class="leave.type === 'Cuti' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'"
                  >
                    {{ leave.type }}
                  </Badge>
                </TableCell>
                <TableCell>{{ formatDate(leave.start_date) }}</TableCell>
                <TableCell>{{ formatDate(leave.end_date) }}</TableCell>
                <TableCell class="max-w-xs truncate text-muted-foreground text-sm" :title="leave.reason">
                  {{ leave.reason }}
                </TableCell>
                <TableCell>
                  <Badge
                    :variant="leave.status === 'approved' ? 'default' : (leave.status === 'rejected' ? 'destructive' : 'outline')"
                    :class="{
                      'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400 border-none': leave.status === 'approved',
                      'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400 border-none': leave.status === 'rejected',
                      'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/20 dark:text-yellow-400 border-yellow-200': leave.status === 'pending',
                    }"
                  >
                    {{ leave.status === 'approved' ? 'Disetujui' : leave.status === 'rejected' ? 'Ditolak' : 'Menunggu' }}
                  </Badge>
                </TableCell>
                <TableCell class="text-sm text-red-500 font-medium">
                  {{ leave.rejection_reason || '-' }}
                </TableCell>
              </TableRow>

              <TableRow v-if="leaveList.length === 0">
                <TableCell colspan="6" class="h-32 text-center text-muted-foreground">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <AlertCircle class="size-8 text-muted-foreground" />
                    <span>Belum ada pengajuan cuti atau izin.</span>
                  </div>
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
