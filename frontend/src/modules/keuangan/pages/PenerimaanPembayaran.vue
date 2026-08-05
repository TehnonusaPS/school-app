<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Search,
  CheckCircle2,
  History,
  ShieldCheck,
  ArrowLeft,
  X
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { fetchAllSiswa } from '@/services/siswaService'
import { getStudentBills, createSppPayment } from '@/services/sppService'

const router = useRouter()
const route = useRoute()

// Student Search Auto-suggest state
const searchQuery = ref('')
const students = ref([])
const filteredStudents = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return []
  return students.value.filter(s => 
    (s.nama && s.nama.toLowerCase().includes(q)) || 
    (s.nisn && s.nisn.toLowerCase().includes(q))
  )
})

const selectedStudent = ref(null)
const bills = ref([])
const selectedBillIds = ref([])
const notes = ref('')
const paymentMethod = ref('tunai')
const isSubmitting = ref(false)

// Fetch all students on mount
onMounted(async () => {
  try {
    const res = await fetchAllSiswa()
    if (res.status === 'success') {
      students.value = res.data
      
      // Auto-select student if student_id query param is present
      if (route.query.student_id) {
        const found = students.value.find(s => s.id === route.query.student_id)
        if (found) {
          selectStudent(found)
        }
      }
    }
  } catch (err) {
    toast.error('Gagal mengambil daftar siswa.')
  }
})

// Trigger fetch student bills when a student is selected
const selectStudent = async (student) => {
  selectedStudent.value = student
  searchQuery.value = student.nama
  selectedBillIds.value = []
  bills.value = []
  
  try {
    const res = await getStudentBills(student.id)
    if (res.status === 'success') {
      // Filter out bills that are already fully paid
      bills.value = res.data.filter(b => b.status !== 'paid')
    }
  } catch (err) {
    toast.error('Gagal mengambil tagihan aktif siswa.')
  }
}

const clearSelection = () => {
  selectedStudent.value = null
  searchQuery.value = ''
  selectedBillIds.value = []
  bills.value = []
}

// Compute total payment amount from checked bills
const totalPaymentAmount = computed(() => {
  return selectedBillIds.value.reduce((sum, billId) => {
    const bill = bills.value.find(b => b.id === billId)
    if (bill) {
      const unpaidAmount = parseFloat(bill.amount) - parseFloat(bill.paid_amount)
      return sum + unpaidAmount
    }
    return sum
  }, 0)
})

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(val)
}

const handleSubmit = async () => {
  if (!selectedStudent.value) {
    toast.error('Mohon pilih siswa terlebih dahulu!')
    return
  }
  if (selectedBillIds.value.length === 0) {
    toast.error('Mohon pilih minimal satu tagihan untuk dibayar!')
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      student_id: selectedStudent.value.id,
      bill_ids: selectedBillIds.value,
      amount: totalPaymentAmount.value,
      payment_method: paymentMethod.value === 'tunai' ? 'cash' : 'transfer',
      notes: notes.value
    }
    const res = await createSppPayment(payload)
    if (res.status === 'success') {
      toast.success('Pembayaran SPP berhasil dicatat!')
      router.push(`/keuangan/cetak-kwitansi?payment_id=${res.data.id}`)
    } else {
      throw new Error(res.message || 'Gagal menyimpan pembayaran.')
    }
  } catch (err) {
    toast.error(err.message || 'Terjadi kesalahan saat memproses pembayaran.')
  } finally {
    isSubmitting.value = false
  }
}

// Recent activities (mock stubs for sidebar/display)
const recentActivities = ref([
  {
    id: 1,
    name: 'Riana Amalia',
    type: 'SPP Juni 2024',
    time: '10:45 • ID #TX9823',
    method: 'Tunai',
    amount: 'Rp 1.200.000',
    methodClass: 'bg-primary/10 text-primary'
  },
  {
    id: 2,
    name: 'Dimas Setiawan',
    type: 'Biaya Praktikum',
    time: '09:12 • ID #TX9822',
    method: 'Transfer',
    amount: 'Rp 150.000',
    methodClass: 'bg-blue-500/10 text-blue-500'
  },
  {
    id: 3,
    name: 'Eka Putri',
    type: 'SPP Oktober 2023',
    time: 'Kemarin • ID #TX9821',
    method: 'Tunai',
    amount: 'Rp 1.200.000',
    methodClass: 'bg-primary/10 text-primary'
  }
])
</script>

<template>
  <div class="h-full space-y-6 text-foreground pb-10 w-full text-left">
    
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="outline" size="icon" @click="router.push('/keuangan/spp')" class="rounded-full">
        <ArrowLeft class="w-4 h-4" />
      </Button>
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Penerimaan Pembayaran</h1>
        <p class="text-muted-foreground mt-2 text-sm">Rekam pembayaran SPP siswa secara instan dan akurat.</p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-6">
      
      <!-- Left Column -->
      <div class="space-y-6">
        
        <!-- Cari Siswa -->
        <Card class="p-6 shadow-sm relative overflow-visible">
          <label class="block text-sm font-bold mb-3">Cari Siswa (Nama atau NIS)</label>
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <Input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Masukkan nama siswa atau nomor induk siswa..." 
              class="pl-9 pr-9 h-11"
              :disabled="!!selectedStudent"
            />
            <button 
              v-if="selectedStudent" 
              @click="clearSelection" 
              class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Suggestions Dropdown -->
          <div 
            v-if="filteredStudents.length > 0 && !selectedStudent" 
            class="absolute left-6 right-6 mt-1 bg-card border border-border rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-border"
          >
            <div 
              v-for="s in filteredStudents" 
              :key="s.id" 
              @click="selectStudent(s)" 
              class="px-4 py-3 hover:bg-muted/40 cursor-pointer flex items-center justify-between transition-colors"
            >
              <div>
                <h4 class="font-bold text-sm text-foreground">{{ s.nama }}</h4>
                <p class="text-xs text-muted-foreground mt-0.5">NISN: {{ s.nisn || '-' }} • Kelas: {{ s.kelas }}</p>
              </div>
              <span class="text-xs font-semibold text-primary">Pilih ✓</span>
            </div>
          </div>
          
          <!-- Selected Student Details -->
          <div v-if="selectedStudent" class="bg-primary/5 border border-primary/20 rounded-xl p-4 flex items-center justify-between mt-4">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold">
                {{ selectedStudent.nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
              </div>
              <div>
                <h4 class="font-bold text-sm text-foreground">{{ selectedStudent.nama }}</h4>
                <p class="text-xs text-muted-foreground mt-0.5">NISN: {{ selectedStudent.nisn || '-' }} • Kelas: {{ selectedStudent.kelas }}</p>
              </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-bold text-primary">
              <CheckCircle2 class="w-4 h-4" /> Terpilih
            </div>
          </div>
        </Card>

        <!-- Tagihan Aktif -->
        <Card class="p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4 border-b border-border pb-4">
            <h2 class="text-lg font-bold">Tagihan Aktif</h2>
            <span 
              class="text-xs font-bold px-3 py-1 rounded-md"
              :class="bills.length > 0 ? 'bg-destructive/10 text-destructive' : 'bg-emerald-500/10 text-emerald-500'"
            >
              {{ bills.length }} Tagihan Belum Lunas
            </span>
          </div>
          
          <div v-if="!selectedStudent" class="text-center py-10 text-sm text-muted-foreground font-medium">
            Silakan pilih siswa terlebih dahulu untuk melihat daftar tagihan aktif.
          </div>
          <div v-else-if="bills.length === 0" class="text-center py-10 text-sm text-emerald-500 font-bold">
            Semua tagihan untuk siswa ini telah lunas! ✓
          </div>
          <div v-else class="space-y-3">
            <!-- Dynamic Bill Items -->
            <div 
              v-for="bill in bills" 
              :key="bill.id" 
              class="flex items-center justify-between border border-border p-4 rounded-lg transition-colors hover:bg-muted/10"
              :class="selectedBillIds.includes(bill.id) ? 'bg-primary/[0.02] border-primary/30' : ''"
            >
              <div class="flex items-start gap-4">
                <input 
                  type="checkbox" 
                  :id="'tagihan-' + bill.id" 
                  :value="bill.id"
                  v-model="selectedBillIds"
                  class="mt-1 cursor-pointer w-4 h-4 rounded border-border text-primary focus:ring-primary" 
                />
                <div class="grid gap-1">
                  <label :for="'tagihan-' + bill.id" class="text-sm font-bold leading-none cursor-pointer text-foreground">
                    {{ bill.title }}
                  </label>
                  <p class="text-xs text-muted-foreground">Jatuh tempo: {{ new Date(bill.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</p>
                  
                  <!-- Paid & Outstanding Details -->
                  <div class="flex items-center gap-3 mt-1 text-[11px] font-semibold">
                    <span class="text-emerald-600 bg-emerald-500/10 px-1.5 py-0.2 rounded" v-if="parseFloat(bill.paid_amount) > 0">
                      Telah dibayar: {{ formatRupiah(bill.paid_amount) }}
                    </span>
                    <span class="text-amber-600 bg-amber-500/10 px-1.5 py-0.2 rounded">
                      Sisa tagihan: {{ formatRupiah(parseFloat(bill.amount) - parseFloat(bill.paid_amount)) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Total Nilai</div>
                <div class="font-bold text-sm mt-0.5">{{ formatRupiah(bill.amount) }}</div>
              </div>
            </div>
          </div>
        </Card>

        <!-- Rincian Pembayaran -->
        <Card class="p-6 shadow-sm">
          <h2 class="text-lg font-bold mb-5">Rincian Pembayaran</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div class="space-y-2">
              <label class="text-sm font-bold text-muted-foreground">Jumlah yang Dibayar (Rp)</label>
              <Input 
                type="text" 
                :value="formatRupiah(totalPaymentAmount)" 
                disabled 
                class="h-11 font-bold bg-muted/60 cursor-not-allowed" 
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-muted-foreground">Metode Pembayaran</label>
              <Select v-model="paymentMethod">
                <SelectTrigger class="h-11 font-medium">
                  <SelectValue placeholder="Pilih Metode" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="tunai">Tunai (Cash)</SelectItem>
                  <SelectItem value="transfer">Transfer Bank</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="space-y-2 mb-6">
            <label class="text-sm font-bold text-muted-foreground">Catatan Tambahan (Opsional)</label>
            <Textarea v-model="notes" placeholder="Contoh: Pembayaran tunai via loket keuangan..." class="resize-none min-h-[80px]" />
          </div>

          <div class="border-t border-border pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <p class="text-xs font-bold text-muted-foreground tracking-widest uppercase mb-1">TOTAL PEMBAYARAN</p>
              <h2 class="text-2xl font-black text-foreground">{{ formatRupiah(totalPaymentAmount) }}</h2>
            </div>
            <Button 
              @click="handleSubmit" 
              :disabled="isSubmitting"
              class="bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-6 font-bold flex items-center gap-2 shadow-sm"
            >
              <span v-if="isSubmitting" class="flex items-center gap-1.5">
                <span class="w-4 h-4 border-2 border-primary-foreground border-t-transparent animate-spin rounded-full"></span>
                Menyimpan...
              </span>
              <span v-else class="flex items-center gap-2">
                <CheckCircle2 class="w-5 h-5" />
                Catat Pembayaran
              </span>
            </Button>
          </div>
        </Card>

      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        
        <!-- Aktivitas Terkini -->
        <Card class="shadow-sm">
          <div class="p-5 flex items-center gap-2 border-b border-border">
            <History class="w-5 h-5 text-muted-foreground" />
            <h3 class="font-bold text-lg">Aktivitas Terkini</h3>
          </div>
          <div class="divide-y divide-border">
            <div v-for="act in recentActivities" :key="act.id" class="p-5 flex items-start justify-between">
              <div>
                <h4 class="font-bold text-sm">{{ act.name }}</h4>
                <p class="text-[11px] font-medium text-muted-foreground mt-1">{{ act.type }}</p>
                <p class="text-[10px] text-muted-foreground mt-0.5">{{ act.time }}</p>
              </div>
              <div class="text-right flex flex-col items-end">
                <span :class="['text-[9px] px-2 py-0.5 rounded font-bold tracking-wider mb-1', act.methodClass]">
                  {{ act.method }}
                </span>
                <span class="font-bold text-sm">{{ act.amount }}</span>
              </div>
            </div>
          </div>
          <div class="p-4 border-t border-border text-center">
            <a href="#" class="text-sm font-bold text-muted-foreground hover:text-foreground">
              Lihat Semua Riwayat
            </a>
          </div>
        </Card>

        <!-- Sistem Aman Box -->
        <div class="bg-primary/5 p-6 rounded-xl shadow-md border border-border">
          <div class="flex items-center gap-2 mb-4">
            <ShieldCheck class="w-5 h-5 text-primary" />
            <h3 class="font-bold text-sm text-foreground">Sistem Aman</h3>
          </div>
          <p class="text-xs leading-relaxed font-medium text-muted-foreground">
            Sesi Anda diaudit oleh sistem keamanan sekolah. Setiap transaksi akan langsung dicatat ke buku besar keuangan sekolah.
          </p>
        </div>

      </div>

    </div>
  </div>
</template>
