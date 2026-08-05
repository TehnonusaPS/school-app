<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import {
  Filter,
  X,
  Download,
  Printer,
  Search,
  Loader2,
  AlertCircle
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { getSppPayments, getSppPaymentDetails } from '@/services/sppService'

const route = useRoute()

// State
const payments = ref([])
const selectedPayment = ref(null)
const isLoadingList = ref(false)
const isLoadingDetail = ref(false)

// Filters
const search = ref('')
const startDate = ref('')
const endDate = ref('')
const paymentMethod = ref('semua')

// Fetch payments list
const loadPayments = async () => {
  try {
    isLoadingList.value = true
    const params = {}
    if (search.value.trim()) params.search = search.value.trim()
    if (startDate.value) params.start_date = startDate.value
    if (endDate.value) params.end_date = endDate.value
    if (paymentMethod.value && paymentMethod.value !== 'semua') params.payment_method = paymentMethod.value

    const res = await getSppPayments(params)
    if (res.status === 'success') {
      payments.value = res.data
      
      // If we don't have a selected payment and list is not empty, auto-select first
      if (!selectedPayment.value && payments.value.length > 0) {
        selectPayment(payments.value[0].id)
      }
    }
  } catch (err) {
    toast.error('Gagal mengambil daftar kuitansi.')
  } finally {
    isLoadingList.value = false
  }
}

// Fetch single payment details
const selectPayment = async (id) => {
  try {
    isLoadingDetail.value = true
    const res = await getSppPaymentDetails(id)
    if (res.status === 'success') {
      selectedPayment.value = res.data
    }
  } catch (err) {
    toast.error('Gagal mengambil detail kuitansi.')
  } finally {
    isLoadingDetail.value = false
  }
}

// On Mount
onMounted(async () => {
  const queryPaymentId = route.query.payment_id
  if (queryPaymentId) {
    await selectPayment(queryPaymentId)
  }
  await loadPayments()
})

// Helpers
const formatRupiah = (val) => {
  if (!val && val !== 0) return '-'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(val)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Spell numbers in Indonesian words (Terbilang)
const terbilang = (nilai) => {
  const bilangan = [
    '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 
    'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
  ]
  nilai = Math.floor(nilai)
  if (nilai < 12) {
    return bilangan[nilai]
  } else if (nilai < 20) {
    return terbilang(nilai - 10) + ' Belas'
  } else if (nilai < 100) {
    const sisa = nilai % 10
    return terbilang(nilai / 10) + ' Puluh' + (sisa ? ' ' + terbilang(sisa) : '')
  } else if (nilai < 200) {
    return 'Seratus ' + terbilang(nilai - 100)
  } else if (nilai < 1000) {
    const sisa = nilai % 100
    return terbilang(nilai / 100) + ' Ratus' + (sisa ? ' ' + terbilang(sisa) : '')
  } else if (nilai < 2000) {
    return 'Seribu ' + terbilang(nilai - 1000)
  } else if (nilai < 1000000) {
    const sisa = nilai % 1000
    return terbilang(nilai / 1000) + ' Ribu' + (sisa ? ' ' + terbilang(sisa) : '')
  } else if (nilai < 1000000000) {
    const sisa = nilai % 1000000
    return terbilang(nilai / 1000000) + ' Juta' + (sisa ? ' ' + terbilang(sisa) : '')
  } else if (nilai < 1000000000000) {
    const sisa = nilai % 1000000000
    return terbilang(nilai / 1000000000) + ' Miliar' + (sisa ? ' ' + terbilang(sisa) : '')
  }
  return ''
}

// Print Handler using hidden Iframe to capture printable area
const handlePrint = () => {
  const el = document.getElementById('printable-area')
  if (!el) return

  const iframe = document.createElement('iframe')
  Object.assign(iframe.style, { position: 'absolute', width: '0', height: '0', border: 'none' })
  document.body.appendChild(iframe)

  const styles = Array.from(document.styleSheets)
    .flatMap((ss) => {
      try { return Array.from(ss.cssRules).map((r) => r.cssText) }
      catch { return [] }
    })
    .join('\n')

  const doc = iframe.contentWindow.document
  doc.open()
  doc.write(`
    <html><head>
      <title>Kuitansi Pembayaran #${selectedPayment.value?.reference_number}</title>
      <style>
        ${styles}
        body { background: #fff !important; margin: 0; padding: 0; }
        ::-webkit-scrollbar { display: none; }
      </style>
    </head><body>
      <div style="padding: 15px; max-width: 360px; margin: auto;">${el.innerHTML}</div>
    </body></html>
  `)
  doc.close()

  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    setTimeout(() => document.body.removeChild(iframe), 1000)
  }, 500)
}
</script>

<template>
  <div class="h-full flex flex-col xl:flex-row gap-6 text-foreground text-left">
    
    <!-- Left Section: Data & Filter -->
    <div class="flex-1 space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Cetak Kuitansi Pembayaran</h1>
        <p class="text-muted-foreground mt-2 text-sm">
          Kelola, cari, dan cetak bukti pembayaran siswa dengan mudah.
        </p>
      </div>

      <!-- Filter Card -->
      <Card class="p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
          <div class="space-y-2">
            <label class="text-sm font-semibold">Cari Siswa</label>
            <div class="relative">
              <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <Input type="text" v-model="search" placeholder="Nama / NISN..." class="pl-8 w-full" @keyup.enter="loadPayments" />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-semibold">Rentang Tanggal</label>
            <div class="flex items-center gap-2">
              <Input type="date" v-model="startDate" class="w-full text-muted-foreground" />
              <span class="text-muted-foreground">-</span>
              <Input type="date" v-model="endDate" class="w-full text-muted-foreground" />
            </div>
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-semibold">Jenis Pembayaran</label>
            <Select v-model="paymentMethod">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Semua Jenis" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Jenis</SelectItem>
                <SelectItem value="cash">Tunai (Cash)</SelectItem>
                <SelectItem value="transfer">Transfer Bank</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Button @click="loadPayments" class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 px-6 w-full md:w-auto justify-center">
            <Filter class="w-4 h-4" />
            Terapkan Filter
          </Button>
        </div>
      </Card>

      <!-- Table Section -->
      <Card class="overflow-hidden shadow-sm relative">
        <div v-if="isLoadingList" class="absolute inset-0 bg-background/50 z-10 flex items-center justify-center">
          <Loader2 class="w-8 h-8 animate-spin text-primary" />
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead
              class="bg-muted/50 text-muted-foreground text-xs uppercase font-semibold border-b border-border"
            >
              <tr>
                <th class="px-6 py-4 tracking-wider">NO. KUITANSI</th>
                <th class="px-6 py-4 tracking-wider">SISWA</th>
                <th class="px-6 py-4 tracking-wider">TANGGAL</th>
                <th class="px-6 py-4 tracking-wider">JUMLAH</th>
                <th class="px-6 py-4 tracking-wider">STATUS</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr 
                v-for="tx in payments" 
                :key="tx.id" 
                @click="selectPayment(tx.id)"
                class="hover:bg-muted/30 transition-colors cursor-pointer"
                :class="selectedPayment?.id === tx.id ? 'bg-primary/5 hover:bg-primary/5' : ''"
              >
                <td class="px-6 py-4 font-semibold text-foreground">#{{ tx.reference_number }}</td>
                <td class="px-6 py-4">
                  <p class="font-bold text-foreground">{{ tx.student?.name }}</p>
                  <p class="text-xs text-muted-foreground mt-1">
                    {{ tx.student?.student_profile?.classroom?.name || 'Tanpa Kelas' }}
                  </p>
                </td>
                <td class="px-6 py-4 text-muted-foreground">{{ formatDate(tx.payment_date) }}</td>
                <td class="px-6 py-4 font-bold text-foreground">{{ formatRupiah(tx.amount) }}</td>
                <td class="px-6 py-4">
                  <span
                    class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase"
                    :class="tx.status === 'success' ? 'bg-emerald-500/10 text-emerald-600' : tx.status === 'failed' ? 'bg-destructive/10 text-destructive' : 'bg-amber-500/10 text-amber-600'"
                  >
                    {{ tx.status === 'success' ? 'BERHASIL' : tx.status === 'failed' ? 'GAGAL' : 'PENDING' }}
                  </span>
                </td>
              </tr>
              <tr v-if="payments.length === 0">
                <td colspan="5" class="px-6 py-10 text-center text-muted-foreground">
                  Tidak ada kuitansi pembayaran ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </Card>
    </div>

    <!-- Right Section: Preview -->
    <div class="w-full xl:w-[400px] flex-shrink-0 space-y-5">
      <div class="flex items-center justify-between pt-1">
        <h2 class="text-lg font-bold tracking-tight">Pratinjau Kuitansi</h2>
        <Button 
          v-if="selectedPayment"
          variant="ghost" 
          size="icon" 
          @click="selectedPayment = null"
          class="text-muted-foreground rounded-full hover:bg-muted w-8 h-8"
        >
          <X class="w-4 h-4" />
        </Button>
      </div>

      <!-- Preview loader -->
      <div v-if="isLoadingDetail" class="border border-border rounded-xl p-10 flex flex-col items-center justify-center gap-2 bg-card min-h-[400px]">
        <Loader2 class="w-8 h-8 animate-spin text-primary" />
        <p class="text-xs text-muted-foreground font-medium">Memuat kuitansi...</p>
      </div>

      <!-- No Selection State -->
      <div v-else-if="!selectedPayment" class="border border-border rounded-xl p-10 flex flex-col items-center justify-center gap-3 bg-card min-h-[400px] text-center">
        <AlertCircle class="w-10 h-10 text-muted-foreground/60" />
        <h4 class="font-bold text-sm text-foreground">Kuitansi Belum Dipilih</h4>
        <p class="text-xs text-muted-foreground leading-relaxed max-w-[240px]">
          Pilih salah satu transaksi pembayaran di tabel sebelah kiri untuk menampilkan dan mencetak bukti pembayaran.
        </p>
      </div>

      <!-- Receipt Paper (Always Light theme explicitly for printing preview realism) -->
      <div v-else class="space-y-5">
        <div id="printable-area" class="bg-white text-slate-900 p-6 shadow-sm border border-slate-200 relative rounded-sm mx-auto w-full max-w-[360px]">
          <!-- Watermark Status -->
          <div class="absolute bottom-28 right-4 opacity-[0.12] rotate-[-12deg] pointer-events-none flex items-center justify-center">
            <div 
              class="border-[4px] text-4xl font-black px-4 py-2 rounded-lg tracking-widest"
              :class="selectedPayment.status === 'success' ? 'border-emerald-600 text-emerald-600' : selectedPayment.status === 'failed' ? 'border-rose-600 text-rose-600' : 'border-amber-600 text-amber-600'"
            >
              {{ selectedPayment.status === 'success' ? 'LUNAS' : selectedPayment.status === 'failed' ? 'BATAL' : 'PENDING' }}
            </div>
          </div>

          <!-- Header -->
          <div class="text-center mb-5">
            <h3 class="text-base font-black tracking-tight uppercase text-black">
              {{ selectedPayment.student?.school?.name || 'Sekolah Nusantara' }}
            </h3>
            <p class="text-[9px] mt-1 text-slate-600 font-medium">
              {{ selectedPayment.student?.school?.address || 'Jl. Pendidikan' }}
            </p>
            <p class="text-[9px] text-slate-600 font-medium">
              Telp: {{ selectedPayment.student?.school?.phone || '-' }} | Email: {{ selectedPayment.student?.school?.email || '-' }}
            </p>
          </div>

          <div class="border-b-[1.5px] border-black mb-5"></div>

          <!-- Receipt Meta -->
          <div class="flex justify-between items-start mb-5">
            <div>
              <p class="text-[8px] font-bold text-slate-500 tracking-wider">KUITANSI PEMBAYARAN</p>
              <p class="text-xs font-bold mt-1 text-black">#{{ selectedPayment.reference_number }}</p>
            </div>
            <div class="text-right">
              <p class="text-[8px] font-bold text-slate-500 tracking-wider">TANGGAL PEMBAYARAN</p>
              <p class="text-xs font-bold mt-1 text-black">{{ formatDate(selectedPayment.payment_date) }}</p>
            </div>
          </div>

          <!-- Payer Info -->
          <div class="mb-5">
            <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-1.5">TELAH TERIMA DARI:</p>
            <p class="font-bold text-sm text-black">{{ selectedPayment.student?.name }}</p>
            <p class="text-[10px] font-medium text-slate-600 mt-0.5">
              Kelas: {{ selectedPayment.student?.student_profile?.classroom?.name || '-' }} (NISN: {{ selectedPayment.student?.student_profile?.nisn || '-' }})
            </p>
          </div>

          <div class="border-b border-dashed border-slate-300 mb-5"></div>

          <!-- Details -->
          <div class="mb-5">
            <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-2">UNTUK PEMBAYARAN:</p>
            <ul class="text-xs space-y-1 font-semibold text-black">
              <li v-for="bill in selectedPayment.bills" :key="bill.id">
                ✓ {{ bill.title }}
                <span class="text-slate-500 font-normal">({{ formatRupiah(bill.pivot.amount_paid) }})</span>
              </li>
              <li v-if="selectedPayment.notes" class="text-[10px] text-slate-500 font-normal italic mt-1.5">
                *Catatan: {{ selectedPayment.notes }}
              </li>
            </ul>
          </div>

          <div class="border-b border-dashed border-slate-300 mb-5"></div>

          <!-- Total Box -->
          <div class="bg-slate-50 p-4 flex items-center justify-between mb-4 border border-slate-200">
            <p class="text-[10px] font-bold tracking-wider text-black">JUMLAH TOTAL:</p>
            <p class="text-lg font-black text-black">{{ formatRupiah(selectedPayment.amount) }}</p>
          </div>

          <!-- Terbilang -->
          <div class="mb-8">
            <p class="text-[8px] font-bold text-slate-500 tracking-wider mb-1">TERBILANG:</p>
            <p class="text-[11px] italic font-bold text-black leading-relaxed">
              "{{ terbilang(selectedPayment.amount) }} Rupiah"
            </p>
          </div>

          <!-- Signatures -->
          <div class="flex justify-between items-end mt-12 text-xs">
            <div class="text-center w-[120px]">
              <p class="mb-10 font-medium text-[10px]">Penyetor,</p>
              <p class="text-[10px] font-bold truncate">({{ selectedPayment.student?.name?.split(' ')[0] }}...)</p>
            </div>
            <div class="text-center w-[120px]">
              <p class="mb-10 font-medium text-[10px]">Kasir / Verifikator,</p>
              <p class="text-[10px] font-bold underline underline-offset-4 truncate">
                {{ selectedPayment.verifier?.name || 'Sistem Keuangan' }}
              </p>
              <p class="text-[8px] font-medium text-slate-500 mt-0.5">
                {{ selectedPayment.verifier?.email || '' }}
              </p>
            </div>
          </div>

          <!-- Bottom decoration -->
          <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-black rounded-b-sm"></div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 pt-2">
          <Button @click="handlePrint" variant="outline" class="flex-1 flex items-center gap-2 h-12 border-border bg-card hover:bg-accent font-semibold text-sm">
            <Download class="w-4 h-4" />
            Simpan PDF
          </Button>
          <Button @click="handlePrint" class="flex-1 flex items-center gap-2 h-12 bg-primary text-primary-foreground hover:bg-primary/90 font-semibold text-sm shadow-md">
            <Printer class="w-4 h-4" />
            Cetak Sekarang
          </Button>
        </div>

        <!-- Tips -->
        <div class="p-6 border border-border bg-card rounded-xl shadow-sm">
          <h4 class="text-xs font-extrabold tracking-wider uppercase mb-2">TIPS CETAK</h4>
          <p class="text-xs text-muted-foreground leading-relaxed font-medium">
            Gunakan kertas HVS A5 atau printer thermal 80mm untuk hasil terbaik. Pastikan opsi 'Headers and Footers' dinatikan pada dialog browser sebelum mencetak.
          </p>
        </div>
      </div>

    </div>
  </div>
</template>
