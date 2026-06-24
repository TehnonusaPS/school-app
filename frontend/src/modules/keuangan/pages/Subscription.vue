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
  Upload
} from 'lucide-vue-next'
import StatCard from '@/components/stat-card/StatCard.vue'

import { useRouter } from 'vue-router'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { toast } from 'vue-sonner'
import * as financeService from '@/services/superAdminFinanceService'

const router = useRouter()

const formLangganan = () => {
  router.push('/keuangan/subscription/tambah')
}

// Stats & KPI
const kpiData = ref([
  {
    label: 'TOTAL MRR',
    value: 'Rp 0',
    trend: 'Bulan ini',
    trendDirection: 'up',
    icon: Banknote,
    illustration: 'bag',
    variant: 'emerald'
  },
  {
    label: 'TENANT AKTIF',
    value: '0 Institusi',
    trend: 'Aktif di platform',
    trendDirection: 'up',
    icon: Building2,
    illustration: 'globe',
    variant: 'blue'
  },
  {
    label: 'VERIFIKASI PENDING',
    value: '0 Pembayaran',
    trend: 'Butuh tindakan',
    trendDirection: 'down',
    icon: UserMinus,
    illustration: 'star',
    variant: 'amber'
  },
  {
    label: 'TOTAL REVENUE',
    value: 'Rp 0',
    trend: 'Akumulasi pendapatan',
    trendDirection: 'up',
    icon: Cloud,
    illustration: 'abc_board',
    variant: 'primary'
  }
])

// State
const selectedPaket = ref('semua')
const selectedStatus = ref('semua')
const subscriptions = ref([])
const activities = ref([])
const isLoading = ref(false)

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(val)
}

const loadData = async () => {
  isLoading.value = true
  try {
    // 1. Fetch dashboard stats
    const dashRes = await financeService.getDashboard()
    if (dashRes.status === 'success' && dashRes.data) {
      const stats = dashRes.data.stats
      kpiData.value[0].value = formatCurrency(stats.monthly_recurring_revenue)
      kpiData.value[1].value = `${stats.active_subscriptions_count} Institusi`
      kpiData.value[2].value = `${stats.pending_verifications_count} Pembayaran`
      kpiData.value[3].value = formatCurrency(stats.total_revenue)

      // Map recent activities/logs
      const tempActivities = []
      
      // Process recent subscriptions
      if (dashRes.data.recent_subscriptions) {
        dashRes.data.recent_subscriptions.forEach(sub => {
          const createdAt = new Date(sub.created_at)
          const timeStr = createdAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
          tempActivities.push({
            id: 'sub-' + sub.id,
            time: timeStr,
            title: `Registrasi Tenant Baru: ${sub.foundation?.name || 'Yayasan'}`,
            desc: `ID: F-${sub.foundation_id} | Paket: ${sub.plan?.name || 'Custom'}`,
            badge: 'SIGNUP',
            badgeClass: 'bg-emerald-100 hover:bg-emerald-100 text-emerald-700',
            icon: ShieldCheck,
            iconClass: 'text-emerald-600',
            timestamp: createdAt.getTime()
          })
        })
      }

      // Process recent payments
      if (dashRes.data.recent_payments) {
        dashRes.data.recent_payments.forEach(pay => {
          const payDate = new Date(pay.created_at)
          const timeStr = payDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
          const isPaid = pay.status === 'paid'
          
          tempActivities.push({
            id: 'pay-' + pay.id,
            time: timeStr,
            title: `Invoice ${pay.invoice_number}`,
            desc: `${pay.foundation?.name || 'Yayasan'} | Status: ${pay.status.toUpperCase()}`,
            amount: isPaid ? `+ ${formatCurrency(pay.amount)}` : formatCurrency(pay.amount),
            amountClass: isPaid ? 'text-emerald-600' : 'text-amber-600',
            badge: pay.status.toUpperCase(),
            badgeClass: isPaid 
              ? 'bg-emerald-100 hover:bg-emerald-100 text-emerald-700' 
              : pay.status === 'pending'
                ? 'bg-amber-100 hover:bg-amber-100 text-amber-700'
                : 'bg-red-100 hover:bg-red-100 text-red-700',
            icon: isPaid ? RefreshCw : Upload,
            iconClass: isPaid ? 'text-emerald-600' : 'text-amber-500',
            timestamp: payDate.getTime()
          })
        })
      }

      // Sort activities latest first
      activities.value = tempActivities.sort((a, b) => b.timestamp - a.timestamp)
    }

    // 2. Fetch subscriptions
    const subRes = await financeService.getSubscriptions()
    if (subRes.status === 'success' && subRes.data) {
      // Map API subscriptions to table rows structure
      const items = Array.isArray(subRes.data) ? subRes.data : (subRes.data.data || [])
      subscriptions.value = items.map(sub => {
        const startsAt = new Date(sub.starts_at)
        const endsAt = new Date(sub.ends_at)
        
        const option = { day: 'numeric', month: 'short', year: 'numeric' }
        const startsAtStr = startsAt.toLocaleDateString('id-ID', option)
        const endsAtStr = endsAt.toLocaleDateString('id-ID', option)

        const name = sub.foundation?.name || 'Yayasan Tanpa Nama'
        const avatar = name.split(' ').filter(Boolean).map(w => w[0]).join('').slice(0, 2).toUpperCase() || 'N'

        return {
          id: sub.id,
          name: name,
          paket: sub.plan?.name || 'Custom',
          status: sub.status === 'active' ? 'aktif' : sub.status === 'trial' ? 'trialing' : sub.status,
          tglAktivasi: startsAtStr,
          jatuhTempo: endsAtStr,
          nilaiKontrak: parseFloat(sub.plan?.price || 0),
          avatar: avatar,
          emailAdmin: sub.foundation?.email || '-',
          telepon: sub.foundation?.phone || '-',
          catatan: sub.notes || ''
        }
      })
    }
  } catch (error) {
    toast.error('Gagal mengambil data dari server', {
      description: error.message || 'Koneksi ke backend bermasalah.'
    })
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
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

const deleteSubscription = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus data langganan ini?')) {
    try {
      await financeService.deleteSubscription(id)
      subscriptions.value = subscriptions.value.filter(sub => sub.id !== id)
      toast.success('Data langganan berhasil dihapus!')
      loadData() // reload dashboard stats too
    } catch (error) {
      toast.error('Gagal menghapus langganan', {
        description: error.message || 'Terjadi kesalahan pada server.'
      })
    }
  }
}

const formatContractValue = (val) => {
  if (val === 0) return 'Rp 0 (Trial)'
  return formatCurrency(val)
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
    <!-- Stat Cards -->
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
        :illustration="stat.illustration"
      />
    </div>
    
    <!-- Table Card -->
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
      
      <!-- Subscriptions Table -->
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
                  <div class="text-xs text-muted-foreground">ID Sub: {{ sub.id }}</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <Badge :variant="sub.paket.toLowerCase() === 'enterprise' ? 'default' : sub.paket.toLowerCase() === 'professional' ? 'secondary' : 'outline'" :class="[
                sub.paket.toLowerCase() === 'enterprise' ? 'bg-primary hover:bg-primary/90 text-primary-foreground' : '',
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
              {{ isLoading ? 'Memuat data dari server...' : 'Tidak ada data langganan yang cocok dengan filter.' }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      
      <div class="p-4 border-t flex justify-between items-center text-sm text-muted-foreground">
        <div>Menampilkan {{ filteredSubscriptions.length }} dari {{ subscriptions.length }} Institusi</div>
        <div class="flex items-center gap-1">
          <Button variant="outline" class="w-8 h-8 p-0" :disabled="isLoading">&lt;</Button>
          <Button class="w-8 h-8 p-0" :disabled="isLoading">1</Button>
          <Button variant="outline" class="w-8 h-8 p-0" :disabled="isLoading">&gt;</Button>
        </div>
      </div>
    </Card>

    <!-- Activity Log Card -->
    <Card>
      <CardHeader class="border-b pb-4 pt-5">
        <CardTitle class="text-xl font-bold">Log Aktivitas Langganan</CardTitle>
      </CardHeader>
      <CardContent class="p-0">
        <div class="divide-y text-sm border-border">
          <div v-for="act in activities" :key="act.id" class="flex items-center justify-between p-5">
            <div class="flex items-center gap-8">
              <div class="text-muted-foreground w-24">{{ act.time }}</div>
              <div>
                <div class="font-bold text-foreground text-base">{{ act.title }}</div>
                <div class="text-muted-foreground italic text-xs mt-0.5">{{ act.desc }}</div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span v-if="act.amount" :class="[act.amountClass, 'font-medium']">{{ act.amount }}</span>
              <Badge :class="[act.badgeClass, 'border-none font-semibold px-2 text-[10px]']">{{ act.badge }}</Badge>
              <component :is="act.icon" :class="['w-5 h-5', act.iconClass]" />
            </div>
          </div>
          
          <div v-if="activities.length === 0" class="p-8 text-center text-muted-foreground font-medium">
            {{ isLoading ? 'Memuat aktivitas...' : 'Belum ada aktivitas terbaru.' }}
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
