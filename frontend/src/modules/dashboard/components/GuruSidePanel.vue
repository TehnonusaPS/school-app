<script setup lang="ts">
import { Users, MessageCircle } from 'lucide-vue-next'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import WidgetList from '@/components/dashboard-widget/WidgetList.vue'
import { kelasData as kelas, pesanData as pesan } from '../data/guruSidePanelData'
const props = defineProps({
  delay: { type: Number, default: 0 }
})

interface PesanItem {
  id: number
  nama: string
  isi: string
  waktu: string
  inisial: string
  warna: string
}

const asPesan = (item: unknown) => item as PesanItem
</script>

<template>
  <div class="lg:col-span-2 flex flex-col gap-4">
    <!-- Kelas Saya -->
    <WidgetCard
      title="Kelas Saya"
      :description="`${kelas.length} kelas yang Anda ampu`"
      :icon="Users"
      :delay="delay"
    >
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="k in kelas"
          :key="k.nama"
          class="rounded-xl border p-3 space-y-2 hover:bg-muted/40 transition-colors cursor-default"
        >
          <p class="text-lg font-bold">{{ k.nama }}</p>
          <div class="flex items-center justify-between text-xs text-muted-foreground">
            <span>{{ k.siswa }} Siswa</span>
            <span class="text-emerald-500 font-semibold">{{ k.aktif }} hadir</span>
          </div>
        </div>
      </div>
    </WidgetCard>

    <!-- Komunikasi Terakhir -->
    <WidgetList
      title="Komunikasi Terakhir"
      description="Pesan terbaru yang masuk"
      :icon="MessageCircle"
      cardClass="flex-1"
      listClass="h-[300px] px-4"
      :items="pesan"
      :delay="delay + 150"
    >
      <template #item="{ item }">
        <!-- Avatar inisial -->
        <div :class="['size-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0', asPesan(item).warna]">
          {{ asPesan(item).inisial }}
        </div>
        <div class="min-w-0 flex-1 ml-2">
          <p class="text-sm font-semibold leading-snug truncate">{{ asPesan(item).nama }}</p>
          <p class="text-xs text-muted-foreground truncate mt-0.5">{{ asPesan(item).isi }}</p>
          <p class="text-[10px] text-muted-foreground/70 mt-0.5">{{ asPesan(item).waktu }}</p>
        </div>
      </template>
    </WidgetList>
  </div>
</template>
