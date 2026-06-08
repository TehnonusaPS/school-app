<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Badge } from '@/components/ui/badge'
import { toast } from 'vue-sonner'
import PageHeader from '@/components/page-header/PageHeader.vue'
import DatePicker from '@/components/date-picker/DatePicker.vue'
import { getLocalTimeZone, today } from '@internationalized/date'
import { formatDate } from '@/utils/formatDate'
import { glassFade } from '@/config/motion'
import {
  Field,
  FieldContent,
  FieldDescription,
  FieldLabel,
} from '@/components/ui/field'
import {
  InputGroup,
  InputGroupText,
  InputGroupInput,
} from '@/components/ui/input-group'
import {
  Building2,
  Banknote,
  CheckCircle2,
  Sparkles,
  ShieldCheck,
  Zap,
  Info,
  Mail,
  Phone,
  HelpCircle,
  Key,
  Layers
} from 'lucide-vue-next'

const router = useRouter()

const activeDate = ref(today(getLocalTimeZone()))
const dueDate = ref(today(getLocalTimeZone()).add({ years: 1 }))

const form = ref({
  namaInstitusi: '',
  tenantId: '',
  paket: 'professional',
  status: 'aktif',
  nilaiKontrak: 45000000,
  emailAdmin: '',
  telepon: '',
  catatan: ''
})

// Auto-generate Tenant ID on typing Nama Institusi
watch(() => form.value.namaInstitusi, (newVal) => {
  if (newVal) {
    const clean = newVal
      .toLowerCase()
      .replace(/[^a-z0-9 ]/g, '')
      .split(' ')
      .filter(Boolean)
      .map(w => w.slice(0, 3))
      .join('-')
    form.value.tenantId = `T-${clean || 'new'}-${Math.floor(1000 + Math.random() * 9000)}`
  } else {
    form.value.tenantId = ''
  }
})

// Autoselect contract value and date on change of package type
const updatePackageDetails = (val) => {
  form.value.paket = val
  if (val === 'enterprise') {
    form.value.nilaiKontrak = 120000000
    form.value.status = 'aktif'
  } else if (val === 'professional') {
    form.value.nilaiKontrak = 45000000
    form.value.status = 'aktif'
  } else if (val === 'basic') {
    form.value.nilaiKontrak = 15000000
    form.value.status = 'aktif'
  } else if (val === 'trial') {
    form.value.nilaiKontrak = 0
    form.value.status = 'trialing'
  }
}

// Watch active date & package selection to calculate due date
watch([activeDate, () => form.value.paket], () => {
  if (activeDate.value) {
    if (form.value.paket === 'trial') {
      dueDate.value = activeDate.value.add({ days: 14 })
    } else {
      dueDate.value = activeDate.value.add({ years: 1 })
    }
  }
}, { immediate: true })

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(val)
}

const packageBadgeStyle = (pkg) => {
  switch (pkg) {
    case 'enterprise':
      return 'bg-violet-500/20 text-violet-300 border border-violet-500/30 text-[10px] px-2.5 py-0.5 font-semibold tracking-wider'
    case 'professional':
      return 'bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] px-2.5 py-0.5 font-semibold tracking-wider'
    case 'basic':
      return 'bg-slate-500/20 text-slate-300 border border-slate-500/30 text-[10px] px-2.5 py-0.5 font-semibold tracking-wider'
    case 'trial':
      return 'bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] px-2.5 py-0.5 font-semibold tracking-wider'
    default:
      return 'bg-slate-500/25 text-slate-300 text-[10px]'
  }
}

const statusBadgeStyle = (status) => {
  switch (status) {
    case 'aktif':
      return 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] px-2 py-0.5'
    case 'trialing':
      return 'bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] px-2 py-0.5'
    case 'overdue':
      return 'bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] px-2 py-0.5'
    default:
      return 'bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[10px] px-2 py-0.5'
  }
}

const packageFeatures = computed(() => {
  switch (form.value.paket) {
    case 'enterprise':
      return {
        title: 'Enterprise Plan',
        desc: 'Untuk yayasan besar dengan banyak cabang unit sekolah.',
        benefits: [
          'Multi-unit & Yayasan Hub Terintegrasi',
          'Jumlah Siswa & Guru tidak terbatas',
          'Penyimpanan Awan 100 GB (Premium)',
          'Dukungan SLA Prioritas 24/7'
        ],
        icon: Zap,
        color: 'text-violet-500 bg-violet-500/10 border-violet-500/20'
      }
    case 'professional':
      return {
        title: 'Professional Plan',
        desc: 'Untuk unit sekolah tunggal dengan administrasi lengkap.',
        benefits: [
          'Seluruh fitur Akademik, Absensi, & Keuangan',
          'Maksimum 1.000 Siswa aktif',
          'Penyimpanan Awan 25 GB',
          'Dukungan Jam Kerja (Email & Live Chat)'
        ],
        icon: Sparkles,
        color: 'text-blue-500 bg-blue-500/10 border-blue-500/20'
      }
    case 'basic':
      return {
        title: 'Basic Plan',
        desc: 'Fitur standar untuk sekolah berkembang.',
        benefits: [
          'Fitur Akademik & Kasir Pembayaran Dasar',
          'Maksimum 300 Siswa aktif',
          'Penyimpanan Awan 5 GB',
          'Dukungan melalui Pusat Bantuan Online'
        ],
        icon: ShieldCheck,
        color: 'text-slate-500 bg-slate-500/10 border-slate-500/20'
      }
    case 'trial':
      return {
        title: 'Free Trial',
        desc: 'Uji coba gratis seluruh fitur professional.',
        benefits: [
          'Akses fitur Professional lengkap',
          'Berlaku 14 hari sejak aktivasi',
          'Batas input data s/d 50 siswa',
          'Konversi otomatis ke berbayar'
        ],
        icon: Info,
        color: 'text-amber-500 bg-amber-500/10 border-amber-500/20'
      }
    default:
      return null
  }
})

const isSubmitting = ref(false)

const handleSubmit = () => {
  if (!form.value.namaInstitusi) {
    toast.error('Mohon isi nama institusi!')
    return
  }
  if (!form.value.emailAdmin) {
    toast.error('Mohon isi email administrator!')
    return
  }
  
  isSubmitting.value = true
  
  setTimeout(() => {
    toast.success('Pendaftaran Langganan Baru Berhasil!')
    isSubmitting.value = false
    router.push('/keuangan/subscription')
  }, 1000)
}
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="h-full space-y-6 text-foreground pb-10 max-w-6xl mx-auto w-full text-left"
  >
    <!-- Header Component (Consistent with overall project theme) -->
    <PageHeader
      title="Tambah Langganan Baru"
      description="Lengkapi formulir di bawah ini untuk mendaftarkan institusi/tenant baru ke dalam lisensi CerdasBangsa."
      back
      @back="router.push('/keuangan/subscription')"
    />

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-6 items-start">
      <!-- Left Column: Form -->
      <Card class="shadow-sm border-border bg-card/60 backdrop-blur-md overflow-hidden rounded-xl">
        <div class="px-6 py-5 border-b border-border/80 flex justify-between items-center bg-muted/20">
          <div class="flex items-center gap-2.5">
            <div class="p-1.5 bg-primary/10 rounded-lg text-primary">
              <Layers class="w-4.5 h-4.5" />
            </div>
            <div>
              <h2 class="font-bold text-sm tracking-wide text-foreground uppercase">FORMULIR AKTIVASI</h2>
              <p class="text-[10px] text-muted-foreground mt-0.5">Menggunakan komponen standar proyek</p>
            </div>
          </div>
          <Badge variant="outline" class="font-mono text-xs px-2.5 py-0.5 border-primary/20 bg-primary/5 text-primary" v-if="form.tenantId">
            {{ form.tenantId }}
          </Badge>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 sm:p-8 space-y-6">
          <!-- Nama & ID Tenant -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Institusi -->
            <Field>
              <FieldLabel>Nama Institusi / Sekolah <span class="text-destructive">*</span></FieldLabel>
              <FieldContent>
                <InputGroup>
                  <InputGroupText>
                    <Building2 class="w-4 h-4 text-muted-foreground" />
                  </InputGroupText>
                  <InputGroupInput
                    v-model="form.namaInstitusi"
                    placeholder="Contoh: SMA Terpadu Harapan"
                    required
                  />
                </InputGroup>
              </FieldContent>
            </Field>

            <!-- ID Tenant -->
            <Field>
              <FieldLabel>ID Tenant (Otomatis)</FieldLabel>
              <FieldContent>
                <InputGroup>
                  <InputGroupText>
                    <Key class="w-4 h-4 text-muted-foreground" />
                  </InputGroupText>
                  <InputGroupInput
                    v-model="form.tenantId"
                    placeholder="Terisi otomatis..."
                    disabled
                    class="bg-muted/60 text-muted-foreground font-mono cursor-not-allowed"
                  />
                </InputGroup>
              </FieldContent>
            </Field>
          </div>

          <!-- Paket & Status -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Paket Langganan -->
            <Field>
              <FieldLabel>Paket Langganan</FieldLabel>
              <FieldContent>
                <Select :model-value="form.paket" @update:modelValue="updatePackageDetails">
                  <SelectTrigger class="h-11 bg-background/50 border-border">
                    <SelectValue placeholder="Pilih Paket" />
                  </SelectTrigger>
                  <SelectContent class="bg-card border-border">
                    <SelectItem value="trial">Free Trial (14 Hari)</SelectItem>
                    <SelectItem value="basic">Basic (Rp 15.000.000 / Thn)</SelectItem>
                    <SelectItem value="professional">Professional (Rp 45.000.000 / Thn)</SelectItem>
                    <SelectItem value="enterprise">Enterprise (Rp 120.000.000 / Thn)</SelectItem>
                  </SelectContent>
                </Select>
              </FieldContent>
            </Field>
            
            <!-- Status Awal -->
            <Field>
              <FieldLabel>Status Awal</FieldLabel>
              <FieldContent>
                <Select v-model="form.status">
                  <SelectTrigger class="h-11 bg-background/50 border-border">
                    <SelectValue placeholder="Pilih Status" />
                  </SelectTrigger>
                  <SelectContent class="bg-card border-border">
                    <SelectItem value="aktif">Aktif</SelectItem>
                    <SelectItem value="trialing">Trialing</SelectItem>
                    <SelectItem value="overdue">Overdue / Telat</SelectItem>
                  </SelectContent>
                </Select>
              </FieldContent>
            </Field>
          </div>

          <!-- Nilai Kontrak -->
          <Field>
            <FieldLabel>Nilai Kontrak Pertahun (Rp)</FieldLabel>
            <FieldContent>
              <InputGroup>
                <InputGroupText class="font-bold text-xs">Rp</InputGroupText>
                <InputGroupInput
                  v-model.number="form.nilaiKontrak"
                  type="number"
                  placeholder="0"
                  min="0"
                />
              </InputGroup>
            </FieldContent>
            <FieldDescription class="flex items-center gap-1">
              <HelpCircle class="w-3 h-3 shrink-0" />
              Sesuaikan nilai jika terdapat penyesuaian khusus (diskon, subsidi yayasan, dll).
            </FieldDescription>
          </Field>

          <!-- Tanggal Aktivasi & Jatuh Tempo (Menggunakan DatePicker standar) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
            <DatePicker
              v-model="activeDate"
              label="Tanggal Aktivasi"
              placeholder="Pilih Tanggal Aktivasi"
            />
            
            <DatePicker
              v-model="dueDate"
              label="Tanggal Jatuh Tempo"
              placeholder="Pilih Tanggal Jatuh Tempo"
            />
          </div>

          <!-- Admin Contact Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Email Admin -->
            <Field>
              <FieldLabel>Email Administrator <span class="text-destructive">*</span></FieldLabel>
              <FieldContent>
                <InputGroup>
                  <InputGroupText>
                    <Mail class="w-4 h-4 text-muted-foreground" />
                  </InputGroupText>
                  <InputGroupInput
                    v-model="form.emailAdmin"
                    type="email"
                    placeholder="admin.sekolah@email.com"
                    required
                  />
                </InputGroup>
              </FieldContent>
            </Field>
            
            <!-- Telepon -->
            <Field>
              <FieldLabel>No. Telepon / WhatsApp</FieldLabel>
              <FieldContent>
                <InputGroup>
                  <InputGroupText>
                    <Phone class="w-4 h-4 text-muted-foreground" />
                  </InputGroupText>
                  <InputGroupInput
                    v-model="form.telepon"
                    type="tel"
                    placeholder="Contoh: 081234567890"
                  />
                </InputGroup>
              </FieldContent>
            </Field>
          </div>

          <!-- Catatan / Memo -->
          <Field>
            <FieldLabel>Catatan Tambahan (Memo Internal)</FieldLabel>
            <FieldContent>
              <Textarea
                v-model="form.catatan"
                placeholder="Tambahkan informasi khusus seperti metode termin pembayaran, penanggung jawab yayasan, dll..."
                class="min-h-[100px] resize-none bg-background/50 border-border focus-visible:ring-primary/20 text-sm"
              />
            </FieldContent>
          </Field>

          <!-- Form Buttons -->
          <div class="border-t border-border/80 pt-6 flex items-center justify-end gap-3.5">
            <Button
              type="button"
              variant="outline"
              class="px-5 h-11 rounded-lg font-semibold border-2 hover:bg-muted/50"
              @click="router.push('/keuangan/subscription')"
            >
              Batal
            </Button>
            
            <Button
              type="submit"
              class="px-6 h-11 rounded-lg font-bold shadow-md bg-primary text-primary-foreground hover:bg-primary/90 transition-all hover:-translate-y-0.5 flex items-center gap-2"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting" class="flex items-center gap-1.5">
                <span class="w-4 h-4 border-2 border-primary-foreground border-t-transparent animate-spin rounded-full"></span>
                Menyimpan...
              </span>
              <span v-else class="flex items-center gap-2">
                Simpan Langganan
                <CheckCircle2 class="w-4.5 h-4.5" />
              </span>
            </Button>
          </div>
        </form>
      </Card>

      <!-- Right Column: Interactive Premium Preview & Plan Feature Details -->
      <div class="space-y-6">
        <!-- Live Subscription membership-like Card (styled with the theme's active primary color) -->
        <div 
          class="relative overflow-hidden rounded-2xl shadow-xl border border-primary/20 bg-gradient-to-br from-primary via-primary/90 to-primary/75 p-6 text-primary-foreground transition-all duration-300 hover:scale-[1.02]"
        >
          <!-- Ambient lighting/glow inside card -->
          <div class="absolute -right-16 -top-16 w-40 h-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
          <div class="absolute -left-12 -bottom-12 w-36 h-36 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

          <!-- Card Content -->
          <div class="relative z-10 space-y-6 flex flex-col justify-between min-h-[260px]">
            <!-- Top section -->
            <div class="flex justify-between items-start gap-4">
              <div class="space-y-1.5 flex-1 min-w-0 pr-2">
                <span class="text-[9px] tracking-widest font-black uppercase text-primary-foreground/70">KARTU ANGGOTA LISENSI</span>
                <h3 class="text-xl font-extrabold tracking-tight leading-tight truncate">
                  {{ form.namaInstitusi || 'Nama Institusi / Sekolah' }}
                </h3>
                <div class="inline-flex items-center gap-1 bg-white/10 px-2 py-0.5 rounded text-[10px] font-mono border border-white/15">
                  ID: {{ form.tenantId || 'T-PROSES-DAFTAR' }}
                </div>
              </div>
              
              <!-- Glass-like Building Icon Box -->
              <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                <Building2 class="w-5.5 h-5.5 text-primary-foreground" />
              </div>
            </div>

            <!-- Middle section: Price & Package -->
            <div class="py-4 border-y border-white/10 flex justify-between items-center">
              <div>
                <p class="text-[9px] uppercase tracking-wider text-primary-foreground/75 font-semibold">Tipe Paket</p>
                <div class="mt-1 flex items-center">
                  <Badge :class="packageBadgeStyle(form.paket)">
                    {{ form.paket.toUpperCase() }}
                  </Badge>
                </div>
              </div>
              <div class="text-right">
                <p class="text-[9px] uppercase tracking-wider text-primary-foreground/75 font-semibold">Nilai Kontrak</p>
                <p class="text-xl font-black mt-0.5 tracking-tight">
                  {{ formatCurrency(form.nilaiKontrak || 0) }}
                </p>
              </div>
            </div>

            <!-- Bottom section: Dates & Status -->
            <div class="flex justify-between items-end gap-4">
              <div class="space-y-3">
                <!-- Date range -->
                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                  <div>
                    <span class="text-[8px] uppercase text-primary-foreground/60 block">Aktivasi</span>
                    <span class="text-[11px] font-bold text-white/95 flex items-center gap-1 mt-0.5">
                      <Calendar class="w-3 h-3 text-white/50" />
                      {{ formatDate(activeDate) }}
                    </span>
                  </div>
                  <div>
                    <span class="text-[8px] uppercase text-primary-foreground/60 block">Jatuh Tempo</span>
                    <span class="text-[11px] font-bold text-white/95 flex items-center gap-1 mt-0.5">
                      <Calendar class="w-3 h-3 text-white/50" />
                      {{ formatDate(dueDate) }}
                    </span>
                  </div>
                </div>
                <!-- Admin contact -->
                <div class="border-t border-white/10 pt-2 min-w-0">
                  <span class="text-[8px] uppercase text-primary-foreground/60 block">Admin Sekolah</span>
                  <span class="text-[10px] font-semibold truncate block mt-0.5 text-white/90">
                    {{ form.emailAdmin || 'admin.sekolah@cerdasbangsa.id' }}
                  </span>
                </div>
              </div>
              
              <!-- Status Badge -->
              <Badge :class="statusBadgeStyle(form.status)" class="shrink-0 mb-0.5 shadow-sm">
                ● {{ form.status.toUpperCase() }}
              </Badge>
            </div>
          </div>
        </div>

        <!-- Dynamic Package Feature Information (Styled to match form card theme) -->
        <div v-if="packageFeatures" class="bg-card/40 border border-border/60 p-5 rounded-xl shadow-sm space-y-4">
          <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-lg border" :class="packageFeatures.color">
              <component :is="packageFeatures.icon" class="w-5 h-5" />
            </div>
            <div>
              <h4 class="font-bold text-sm text-foreground">{{ packageFeatures.title }}</h4>
              <p class="text-xs text-muted-foreground leading-relaxed">{{ packageFeatures.desc }}</p>
            </div>
          </div>
          
          <ul class="space-y-2 border-t border-border/80 pt-4">
            <li v-for="(benefit, index) in packageFeatures.benefits" :key="index" class="text-xs font-semibold flex items-center gap-2.5 text-muted-foreground/90">
              <div class="w-4 h-4 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-[10px] shrink-0 font-bold">
                ✓
              </div>
              {{ benefit }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Force dynamic over-ride on the child DatePicker trigger to keep layout uniform and responsive */
:deep([data-slot=select-trigger]) {
  width: 100% !important;
  height: 2.75rem !important; /* matches h-11 */
  background-color: var(--background);
  border-color: var(--border);
  padding-left: 0.875rem !important;
  border-radius: var(--radius) !important;
}
</style>