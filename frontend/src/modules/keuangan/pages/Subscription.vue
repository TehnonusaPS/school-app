<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Banknote,
  Building2,
  UserMinus,
  Cloud,
  Plus,
  Download,
  Pencil,
  Trash2,
  MoreVertical,
  ShieldCheck,
  RefreshCw,
  Upload,
  TrendingUp,
  PlusCircle,
  TrendingDown
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'

import { useRouter } from 'vue-router'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { toast } from 'vue-sonner'

// Data for StatCard
const kpiData = [
  {
    label: 'TOTAL MRR',
    value: 'Rp 485.200k',
    trend: '+8.4% bln ini',
    trendDirection: 'up',
    icon: Banknote,
    variant: 'emerald'
  },
  {
    label: 'TENANT AKTIF',
    value: '142 Institusi',
    trend: '+12 Baru',
    trendDirection: 'up',
    icon: Building2,
    variant: 'blue'
  },
  {
    label: 'CHURN RATE',
    value: '1.2%',
    trend: '-0.5% (Sehat)',
    trendDirection: 'down',
    icon: UserMinus,
    variant: 'amber'
  },
  {
    label: 'PLATFORM HEALTH',
    value: '99.9%',
    progress: 99.9,
    icon: Cloud,
    variant: 'primary'
  }
]

const router = useRouter()

const formLangganan = () => {
  router.push('/keuangan/subscription/tambah')
}

// Reactive subscriptions state & CRUD
const selectedPaket = ref('semua')
const selectedStatus = ref('semua')
const subscriptions = ref([])

const defaultSubscriptions = [
  {
    id: 'T-10024',
    name: 'SMA Terpadu Harapan',
    paket: 'enterprise',
    status: 'aktif',
    tglAktivasi: '12 Jan 2024',
    tglAktivasiRaw: { year: 2024, month: 1, day: 12 },
    jatuhTempo: '12 Jan 2025',
    jatuhTempoRaw: { year: 2025, month: 1, day: 12 },
    nilaiKontrak: 120000000,
    avatar: 'ST',
    emailAdmin: 'admin.sma@terpaduharapan.sch.id',
    telepon: '081234567890',
    catatan: 'Pembayaran tahunan via transfer bank.'
  },
  {
    id: 'T-09882',
    name: 'Madrasah Pusat Al-Ikhlas',
    paket: 'professional',
    status: 'overdue',
    tglAktivasi: '05 Okt 2023',
    tglAktivasiRaw: { year: 2023, month: 10, day: 5 },
    jatuhTempo: '05 Okt 2024',
    jatuhTempoRaw: { year: 2024, month: 10, day: 5 },
    nilaiKontrak: 45000000,
    avatar: 'MP',
    emailAdmin: 'info@alikhlas.sch.id',
    telepon: '082134567890',
    catatan: 'Hubungi bendahara sebelum jatuh tempo.'
  },
  {
    id: 'T-10255',
    name: 'SMP IT Al-Azhar',
    paket: 'basic',
    status: 'trialing',
    tglAktivasi: '20 Jun 2024',
    tglAktivasiRaw: { year: 2024, month: 6, day: 20 },
    jatuhTempo: '04 Jul 2024',
    jatuhTempoRaw: { year: 2024, month: 7, day: 4 },
    nilaiKontrak: 0,
    avatar: 'AZ',
    emailAdmin: 'admin@alazhar.sch.id',
    telepon: '083134567890',
    catatan: 'Uji coba gratis 14 hari.'
  }
]

onMounted(() => {
  const stored = localStorage.getItem('cerdasbangsa_subscriptions')
  if (stored) {
    subscriptions.value = JSON.parse(stored)
  } else {
    subscriptions.value = [...defaultSubscriptions]
    localStorage.setItem('cerdasbangsa_subscriptions', JSON.stringify(subscriptions.value))
  }
})

const filteredSubscriptions = computed(() => {
  return subscriptions.value.filter(sub => {
    const matchPaket = selectedPaket.value === 'semua' || sub.paket.toLowerCase() === selectedPaket.value.toLowerCase()
    const matchStatus = selectedStatus.value === 'semua' || sub.status.toLowerCase() === selectedStatus.value.toLowerCase()
    return matchPaket && matchStatus
  })
})

const editSubscription = (id) => {
  router.push(`/keuangan/subscription/tambah?id=${id}`)
}

const deleteSubscription = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus data langganan ini?')) {
    subscriptions.value = subscriptions.value.filter(sub => sub.id !== id)
    localStorage.setItem('cerdasbangsa_subscriptions', JSON.stringify(subscriptions.value))
    toast.success('Data langganan berhasil dihapus!')
  }
}

const formatContractValue = (val) => {
  if (val === 0) return 'Rp 0 (Trial)'
  return `Rp ${new Intl.NumberFormat('id-ID').format(val)}`
}

const formatDateWithBr = (dateStr) => {
  if (!dateStr) return ''
  const parts = dateStr.split(' ')
  if (parts.length >= 3) {
    return `${parts[0]} ${parts[1]}<br/>${parts[2]}`
  }
  return dateStr
}

const getAvatarClass = (avatar) => {
  if (avatar === 'ST') {
    return 'bg-primary text-primary-foreground'
  } else if (avatar === 'MP') {
    return 'bg-muted text-muted-foreground'
  } else if (avatar === 'AZ') {
    return 'bg-muted-foreground/20 text-foreground'
  } else {
    return 'bg-primary/10 text-primary'
  }
}
</script>

<template>
  <div class="space-y-6 w-full mx-auto px-0">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard
        v-for="(stat, index) in kpiData"
        :key="index"
        :label="stat.label"
        :value="stat.value"
        :trend="stat.trend"
        :trendDirection="stat.trendDirection"
        :icon="stat.icon"
        :progress="stat.progress"
        :variant="stat.variant"
      />
    </div>
    
    <Card>
      <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b gap-4">
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-muted-foreground">Filter Paket:</span>
            <Select v-model="selectedPaket">
              <SelectTrigger class="w-[140px] h-8">
                <SelectValue placeholder="Semua Paket" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Paket</SelectItem>
                <SelectItem value="enterprise">Enterprise</SelectItem>
                <SelectItem value="professional">Professional</SelectItem>
                <SelectItem value="basic">Basic</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-muted-foreground">Status:</span>
            <Select v-model="selectedStatus">
              <SelectTrigger class="w-[140px] h-8">
                <SelectValue placeholder="Semua Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="semua">Semua Status</SelectItem>
                <SelectItem value="aktif">Aktif</SelectItem>
                <SelectItem value="overdue">Overdue</SelectItem>
                <SelectItem value="trialing">Trialing</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button @click="formLangganan" class="h-8">
            <Plus class="w-4 h-4 mr-1" /> Tambah Langganan Baru
          </Button>
          <Button variant="outline" class="h-8">
            <Download class="w-4 h-4 mr-1" /> Ekspor Data
          </Button>
        </div>
      </div>
      
      <Table>
        <TableHeader class="bg-muted/50">
          <TableRow>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center w-[50px]">NO</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">INSTITUSI (TENANT)</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">PAKET</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">STATUS</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center">TGL AKTIVASI</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground text-center">JATUH TEMPO</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">NILAI KONTRAK</TableHead>
            <TableHead class="font-semibold text-xs uppercase text-muted-foreground">AKSI</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(sub, index) in filteredSubscriptions" :key="sub.id">
            <TableCell class="text-center font-medium text-muted-foreground">
              {{ index + 1 }}
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-10 h-10 rounded flex items-center justify-center font-bold text-xs shrink-0',
                  getAvatarClass(sub.avatar)
                ]">
                  {{ sub.avatar }}
                </div>
                <div>
                  <div class="font-bold text-foreground">{{ sub.name }}</div>
                  <div class="text-xs text-muted-foreground">ID: {{ sub.id }}</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge :variant="sub.paket === 'enterprise' ? 'default' : sub.paket === 'professional' ? 'secondary' : 'outline'" :class="[
                sub.paket === 'enterprise' ? 'bg-primary hover:bg-primary/90 text-primary-foreground' : '',
                'rounded-[4px] text-[10px] px-2 py-0.5 uppercase'
              ]">{{ sub.paket }}</Badge>
            </TableCell>
            <TableCell>
              <Badge variant="outline" :class="[
                sub.status === 'aktif' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' :
                sub.status === 'overdue' ? 'text-red-500 bg-red-50 border-red-100' :
                'text-blue-600 bg-blue-50 border-blue-100',
                'px-2 py-0.5 font-medium'
              ]" showDot>{{ sub.status === 'aktif' ? 'Aktif' : sub.status === 'overdue' ? 'Overdue' : 'Trialing' }}</Badge>
            </TableCell>
            <TableCell>
              <div class="text-sm text-muted-foreground text-center" v-html="formatDateWithBr(sub.tglAktivasi)"></div>
            </TableCell>
            <TableCell>
              <div :class="[
                sub.status === 'overdue' ? 'text-red-500 font-bold' : 'text-muted-foreground',
                'text-sm text-center'
              ]" v-html="formatDateWithBr(sub.jatuhTempo)"></div>
            </TableCell>
            <TableCell>
              <div class="font-bold text-foreground">{{ formatContractValue(sub.nilaiKontrak) }}</div>
            </TableCell>
            <TableCell>
              <div class="flex items-center gap-3 text-muted-foreground">
                <button @click="editSubscription(sub.id)" class="hover:text-foreground"><Pencil class="w-4 h-4" /></button>
                <button @click="deleteSubscription(sub.id)" class="hover:text-destructive"><Trash2 class="w-4 h-4" /></button>
                <button class="hover:text-foreground"><MoreVertical class="w-4 h-4" /></button>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-if="filteredSubscriptions.length === 0">
            <TableCell colspan="8" class="text-center py-8 text-muted-foreground font-medium">
              Tidak ada data langganan yang cocok dengan filter.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      
      <div class="p-4 border-t flex justify-between items-center text-sm text-muted-foreground">
        <div>Menampilkan {{ filteredSubscriptions.length }} dari {{ subscriptions.length }} Institusi</div>
        <div class="flex items-center gap-1">
          <Button variant="outline" class="w-8 h-8 p-0">&lt;</Button>
          <Button class="w-8 h-8 p-0">1</Button>
          <Button variant="outline" class="w-8 h-8 p-0">2</Button>
          <Button variant="outline" class="w-8 h-8 p-0">3</Button>
          <Button variant="outline" class="w-8 h-8 p-0">&gt;</Button>
        </div>
      </div>
    </Card>

    <Card>
      <CardHeader class="border-b pb-4 pt-5">
        <CardTitle class="text-xl font-bold">Log Aktivitas Langganan</CardTitle>
      </CardHeader>
      <CardContent class="p-0">
        <div class="divide-y text-sm border-border">
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">14:45 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Registrasi Tenant Baru: SMP IT Al-Azhar</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-10255 | Paket: Basic (Trial)</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <Badge class="bg-emerald-100 hover:bg-emerald-100 text-emerald-700 border-none font-semibold px-2 text-[10px]">SIGNUP</Badge>
              <ShieldCheck class="w-5 h-5 text-emerald-600" />
            </div>
          </div>
          
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">12:30 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Pembaruan Langganan: SMA Negeri 1 Pusat</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-08442 | Paket: Professional (Tahunan)</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-emerald-600 font-medium">+ Rp 18.000.000</span>
              <RefreshCw class="w-5 h-5 text-emerald-600" />
            </div>
          </div>
          
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">09:15 WIB</div>
              <div>
                <div class="font-bold text-foreground text-base">Upgrade Paket: SMK Teknik Global</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">ID: T-09110 | Basic &rarr; Professional</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="font-medium text-foreground">+ Rp 1.500.000 (Prorated)</span>
              <Upload class="w-5 h-5 text-foreground" />
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
