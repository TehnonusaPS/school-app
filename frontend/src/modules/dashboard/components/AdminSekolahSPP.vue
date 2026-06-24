<script setup lang="ts">
import { Wallet } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import DonutChartCard from '@/components/chart-card/DonutChartCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { sppData as chartData, sppSummary } from '../data/adminSekolahSPPData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const auth = useAuthStore()
const computedDelay = computed(() => (auth.isJustLoggedIn ? 1400 : 0) + props.delay)

const total = computed(() => chartData.reduce((a, c) => a + c.nilai, 0))

const activeData = ref<any[]>([
  { status: 'sudah', nilai: 0 },
  { status: 'belum', nilai: total.value }
])

const activePersen = computed(() => {
  const sudah = activeData.value.find(d => d.status === 'sudah')?.nilai || 0
  const tot = activeData.value.reduce((a, c) => a + c.nilai, 0)
  return tot > 0 ? Math.round((sudah / tot) * 100) : 0
})

onMounted(() => {
  setTimeout(() => {
    activeData.value = chartData
  }, computedDelay.value + 800)
})

const chartConfig = {
  sudah: { label: 'Sudah Bayar', color: 'var(--primary)' },
  belum: { label: 'Belum Bayar', color: 'var(--muted-foreground)' }
}
</script>

<template>
  <DonutChartCard
    title="Ringkasan SPP Bulan Ini"
    description="Status pembayaran SPP seluruh siswa"
    :icon="Wallet"
    cardClass="lg:col-span-2"
    contentClass="pb-0"
    footerClass="flex-col gap-0 pt-2 border-t border-white/10"
    :delay="delay"
    illustration="textbook"
    :data="activeData"
    category="nilai"
    index="status"
    :config="chartConfig"
    :showLegend="false"
    :showCenterLabel="true"
    :centerLabelValue="activePersen + '%'"
    centerLabelText="Terkumpul"
  >
    <template #footer>
      <div class="w-full space-y-2 py-3">
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-primary"></span>
            <span class="text-muted-foreground">Sudah Bayar</span>
          </div>
          <span class="font-semibold text-emerald-500">{{ sppSummary.sudah }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-block size-3 rounded-sm bg-muted-foreground"></span>
            <span class="text-muted-foreground">Belum Bayar</span>
          </div>
          <span class="font-semibold text-rose-500">{{ sppSummary.belum }}</span>
        </div>
        <div class="flex items-center justify-between text-sm border-t border-white/10 pt-2">
          <span class="font-medium">Total Target</span>
          <span class="font-bold">{{ sppSummary.total }}</span>
        </div>
      </div>
    </template>
  </DonutChartCard>
</template>
