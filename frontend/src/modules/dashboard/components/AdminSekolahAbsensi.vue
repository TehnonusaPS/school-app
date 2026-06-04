<script setup lang="ts">
import { ClipboardList } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { absensiData as absensi } from '../data/adminSekolahAbsensiData'

const props = defineProps({
  delay: { type: Number, default: 0 }
})

const statusVariant = (s: string) =>
  s === 'Hadir'     ? 'default'   :
  s === 'Terlambat' ? 'secondary' :
  s === 'Izin'      ? 'outline'   :
  'destructive'
</script>

<template>
  <WidgetCard
    title="Update Absensi Terbaru"
    description="Rekap absensi siswa hari ini secara real-time"
    :icon="ClipboardList"
    cardClass="lg:col-span-3"
    contentClass="p-0"
    :delay="delay"
  >
    <!-- Fixed header -->
    <table class="w-full text-sm border-b">
      <colgroup>
        <col style="width: 38%" />
        <col style="width: 22%" />
        <col style="width: 16%" />
        <col style="width: 24%" />
      </colgroup>
      <thead>
        <tr class="border-b bg-muted/50">
          <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Siswa</th>
          <th class="py-3 text-left text-xs font-medium text-muted-foreground">Kelas</th>
          <th class="py-3 text-center text-xs font-medium text-muted-foreground">Jam</th>
          <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Status</th>
        </tr>
      </thead>
    </table>

    <!-- Scrollable body — diperpanjang agar tidak ada space kosong -->
    <div class="h-[390px] overflow-y-auto">
      <table class="w-full text-sm">
        <colgroup>
          <col style="width: 38%" />
          <col style="width: 22%" />
          <col style="width: 16%" />
          <col style="width: 24%" />
        </colgroup>
        <tbody>
          <tr
            v-for="(a, i) in absensi"
            :key="i"
            class="border-b transition-colors hover:bg-muted/50 cursor-default"
          >
            <td class="pl-6 py-3 font-medium text-sm">{{ a.nama }}</td>
            <td class="py-3 text-muted-foreground text-sm">{{ a.kelas }}</td>
            <td class="py-3 text-center font-mono text-xs text-muted-foreground">{{ a.jam }}</td>
            <td class="pr-6 py-3 text-right">
              <Badge :variant="statusVariant(a.status)" class="text-xs">
                {{ a.status }}
              </Badge>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </WidgetCard>
</template>
