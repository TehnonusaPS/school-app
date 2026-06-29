<script setup>
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { computed } from 'vue'

import PageHeader from '@/components/page-header/PageHeader.vue'
import { Button } from '@/components/ui/button'
import { Printer, Pencil, CheckCircle, XCircle, Clock, User, Users } from 'lucide-vue-next'

import FormSection from '@/components/forms/FormSection.vue'
import { Card, CardContent } from '@/components/ui/card'

const router = useRouter()
const route = useRoute()

const dataList = [
  { id: 1, nama: 'Andi Pratama',  nisn: '0123456789', nik: '3174012345678901', noKk: '3174098765432101', status: 'Diterima' },
  { id: 2, nama: 'Siti Aminah',   nisn: '0123456790', nik: '3174012345678902', noKk: '3174098765432102', status: 'Ditolak' },
  { id: 3, nama: 'Budi Santoso',  nisn: '0123456791', nik: '3174012345678903', noKk: '3174098765432103', status: 'Cadangan' },
]

const studentId = Number(route.query.id) || 1
const student = computed(() => dataList.find(s => s.id === studentId) || dataList[0])

const statusClass = computed(() => {
  switch (student.value.status) {
    case 'Diterima': return 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30'
    case 'Ditolak': return 'bg-red-500/20 text-red-500 border-red-500/30'
    case 'Cadangan': return 'bg-amber-500/20 text-amber-500 border-amber-500/30'
    default: return 'bg-slate-500/20 text-slate-500 border-slate-500/30'
  }
})

const badgeClass = computed(() => {
  switch (student.value.status) {
    case 'Diterima': return 'bg-emerald-500'
    case 'Ditolak': return 'bg-red-500'
    case 'Cadangan': return 'bg-amber-500'
    default: return 'bg-slate-500'
  }
})

const badgeText = computed(() => {
  switch (student.value.status) {
    case 'Diterima': return 'TERIMA'
    case 'Ditolak': return 'TOLAK'
    case 'Cadangan': return 'CADANGAN'
    default: return 'UNKNOWN'
  }
})

const BadgeIcon = computed(() => {
  switch (student.value.status) {
    case 'Diterima': return CheckCircle
    case 'Ditolak': return XCircle
    case 'Cadangan': return Clock
    default: return CheckCircle
  }
})

const cetakBukti = () => {
  toast.success('Mencetak bukti pendaftaran...')
}

const editData = () => {
  router.push(`/administrasi/edit-pendaftaran?id=${studentId}`)
}
</script>

<template>
  <div class="flex-1 space-y-8 relative z-10 w-full p-2 sm:p-4">
    <PageHeader
      title="Detail Pendaftaran"
      description="Informasi lengkap pendaftaran calon siswa."
      :back="true"
      @back="router.push('/administrasi/loket')"
      class="mb-8"
    />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-20 md:pb-0">
      
      <!-- Top Card: Profile -->
      <section class="lg:col-span-12">
        <Card class="shadow-sm">
          <CardContent class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center gap-6 z-10">
              <div class="relative">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-muted overflow-hidden border-4 border-background shadow-lg">
                  <img alt="Profile Photo" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida/AP1WRLsvU1cMyl-boCd7O9tCEp0h8QLxhj02U4pmVKGG6AFia7mCMkwD2zO56Mum1C0VBM9EQT1TkwOaGn0JwUyzLbnVMJ31B1X0gvxJHk6rc2LuwiLvAiBgwyYxV8bHSWyFsr31vxxn_jlN2Bxb2r4qSWb-qI5vuLGUoHsw9sMLOZtf0JkyWOhjnvwAOUrfs53_PA69WgopaCIFu1Oimoq0rdGQ4bJnplPcJXRAbpeBYYdV-NWKA7eAolUKmA">
                </div>
                <div class="absolute -bottom-2 -right-2 text-white text-[10px] font-bold px-2 py-1 rounded-full border-2 border-background flex items-center shadow-sm" :class="badgeClass">
                  <component :is="BadgeIcon" class="w-3 h-3 mr-0.5" />
                  {{ badgeText }}
                </div>
              </div>
              <div>
                <h2 class="font-headline-sm text-headline-sm font-bold mb-1">{{ student.nama }}</h2>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-muted-foreground mb-3">
                  <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">badge</span> NISN: {{ student.nisn }}</span>
                  <span class="hidden sm:inline">•</span>
                  <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">how_to_reg</span> No. Reg: PPDB-2024-0892</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border" :class="statusClass">
                  Status: {{ student.status.toUpperCase() }}
                </span>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto z-10">
              <Button @click="cetakBukti" variant="outline" class="h-11 px-5 rounded-lg flex items-center gap-2 font-bold">
                <Printer class="w-4 h-4" />
                Cetak Bukti
              </Button>
              <Button @click="editData" class="bg-primary hover:bg-primary/90 text-primary-foreground h-11 px-5 rounded-lg flex items-center gap-2 font-bold">
                <Pencil class="w-4 h-4" />
                Edit Data
              </Button>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Data Pribadi Siswa -->
      <section class="lg:col-span-6">
        <FormSection title="Data Pribadi Siswa" :icon="User" class="h-full">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Nama Lengkap Sesuai Akta</p>
              <p class="text-sm font-bold">{{ student.nama }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">NISN</p>
              <p class="text-sm font-bold">{{ student.nisn }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">NIK</p>
              <p class="text-sm font-bold">{{ student.nik }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Nomor KK</p>
              <p class="text-sm font-bold">{{ student.noKk }}</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Jenjang Pendidikan</p>
              <p class="text-sm font-bold">SMA / Sederajat</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Agama</p>
              <p class="text-sm font-bold">Islam</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Tempat, Tanggal Lahir</p>
              <p class="text-sm font-bold">Jakarta, 15 Agustus 2008</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Jenis Kelamin</p>
              <p class="text-sm font-bold">Laki-laki</p>
            </div>
          </div>
          
          <div class="pt-4 mt-4 border-t">
            <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Alamat Domisili Lengkap</p>
            <p class="text-sm font-bold leading-relaxed">
              Jl. Merdeka Raya No. 45, RT 03 / RW 05<br>
              Kec. Kebayoran Baru, Jakarta Selatan<br>
              DKI Jakarta, 12160
            </p>
          </div>
        </FormSection>
      </section>

      <!-- Data Orang Tua / Wali -->
      <section class="lg:col-span-6">
        <FormSection title="Data Orang Tua / Wali" :icon="Users" class="h-full">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Nama Ayah Kandung</p>
              <p class="text-sm font-bold">Budi Santoso</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Pekerjaan Ayah</p>
              <p class="text-sm font-bold">Karyawan Swasta</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Nama Ibu Kandung</p>
              <p class="text-sm font-bold">Siti Aminah</p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Pekerjaan Ibu</p>
              <p class="text-sm font-bold">Wiraswasta</p>
            </div>
            
            <div class="sm:col-span-2 pt-4 mt-4 border-t"></div>
            
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Nomor Telepon/WA</p>
              <p class="text-sm font-bold flex items-center gap-2">
                0812-3456-7890
              </p>
            </div>
            <div>
              <p class="text-xs text-muted-foreground mb-1 font-bold uppercase tracking-wider">Email Aktif</p>
              <p class="text-sm font-bold">budi.santoso@example.com</p>
            </div>
          </div>
        </FormSection>
      </section>

    </div>
  </div>
</template>
