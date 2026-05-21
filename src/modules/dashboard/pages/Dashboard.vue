<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import SuperadminStatCards from '../components/SuperadminStatCards.vue'
import SuperadminGrowthChart from '../components/SuperadminGrowthChart.vue'
import SuperadminActivityLog from '../components/SuperadminActivityLog.vue'
import YayasanStatCards from '../components/YayasanStatCards.vue'
import YayasanSekolahSDM from '../components/YayasanSekolahSDM.vue'
import YayasanFinanceChart from '../components/YayasanFinanceChart.vue'
import KepsekStatCards from '../components/KepsekStatCards.vue'
import KepsekKehadiran from '../components/KepsekKehadiran.vue'
import KepsekAgenda from '../components/KepsekAgenda.vue'
import KepsekPerforma from '../components/KepsekPerforma.vue'
import AdminSekolahStatCards from '../components/AdminSekolahStatCards.vue'
import AdminSekolahAbsensi from '../components/AdminSekolahAbsensi.vue'
import AdminSekolahSPP from '../components/AdminSekolahSPP.vue'
import GuruTopSection from '../components/GuruTopSection.vue'
import GuruJadwal from '../components/GuruJadwal.vue'
import GuruSidePanel from '../components/GuruSidePanel.vue'
import GuruPenilaian from '../components/GuruPenilaian.vue'
import SiswaStatCards from '../components/SiswaStatCards.vue'
import SiswaAkademik from '../components/SiswaAkademik.vue'
import SiswaJadwal from '../components/SiswaJadwal.vue'
import SiswaTugas from '../components/SiswaTugas.vue'
import OrangTuaStudentCard from '../components/OrangTuaStudentCard.vue'
import OrangTuaSPP from '../components/OrangTuaSPP.vue'
import OrangTuaPerforma from '../components/OrangTuaPerforma.vue'
import OrangTuaKehadiran from '../components/OrangTuaKehadiran.vue'

const auth = useAuthStore()
</script>

<template>
  <!-- ── Superadmin Dashboard ── -->
  <div v-if="auth.user?.role === 'superadmin'" class="space-y-6">
    <DashboardStatCards />
    <div class="grid gap-4 lg:grid-cols-5">
      <DashboardGrowthChart />
      <DashboardActivityLog />
    </div>
  </div>

  <!-- ── Admin Yayasan Dashboard ── -->
  <div v-else-if="auth.user?.role === 'admin_yayasan'" class="space-y-6">
    <YayasanStatCards />
    <YayasanSekolahSDM />
    <YayasanFinanceChart />
  </div>

  <!-- ── Kepala Sekolah Dashboard ── -->
  <div v-else-if="auth.user?.role === 'kepala_sekolah'" class="space-y-6">
    <KepsekStatCards />
    <div class="grid gap-4 lg:grid-cols-5">
      <KepsekKehadiran />
      <KepsekAgenda />
    </div>
    <KepsekPerforma />
  </div>

  <!-- ── Admin Sekolah & TU Dashboard ── -->
  <div
    v-else-if="auth.user?.role === 'admin_sekolah' || auth.user?.role === 'tata_usaha'"
    class="space-y-6"
  >
    <AdminSekolahStatCards />
    <div class="grid gap-4 lg:grid-cols-5">
      <AdminSekolahAbsensi />
      <AdminSekolahSPP />
    </div>
  </div>

  <!-- ── Guru & Wali Kelas Dashboard ── -->
  <div
    v-else-if="auth.user?.role === 'guru' || auth.user?.role === 'wali_kelas'"
    class="space-y-6"
  >
    <GuruTopSection />
    <div class="grid gap-4 lg:grid-cols-5">
      <GuruJadwal />
      <GuruSidePanel />
    </div>
    <GuruPenilaian />
  </div>

  <!-- ── Siswa Dashboard ── -->
  <div v-else-if="auth.user?.role === 'siswa'" class="space-y-6">
    <SiswaStatCards />
    <div class="grid gap-4 lg:grid-cols-5">
      <SiswaAkademik />
      <SiswaJadwal />
    </div>
    <SiswaTugas />
  </div>

  <!-- ── Orang Tua Dashboard ── -->
  <div v-else-if="auth.user?.role === 'orang_tua'" class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-[3fr_2fr]">
      <OrangTuaStudentCard />
      <OrangTuaSPP />
    </div>
    <div class="grid gap-4 lg:grid-cols-2">
      <OrangTuaPerforma />
      <OrangTuaKehadiran />
    </div>
  </div>

</template>
