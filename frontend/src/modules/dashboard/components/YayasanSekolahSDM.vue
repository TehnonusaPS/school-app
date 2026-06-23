<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import WidgetProgressList from '@/components/dashboard-widget/WidgetProgressList.vue'
import { sekolahData, sdmProgressData, sdmQuickStats } from '../data/yayasanSekolahSDMData'
import { tableRowFade } from '@/config/motion'

const props = defineProps({
  delay: { type: Number, default: 0 }
})
</script>

<template>
  <div class="grid gap-4 lg:grid-cols-5">
    <!-- Tabel Sekolah -->
    <WidgetCard
      title="Daftar Sekolah"
      description="Sekolah yang terdaftar di bawah yayasan ini"
      cardClass="lg:col-span-3"
      contentClass="p-0"
      :delay="delay"
      illustration="abc_board"
    >
      <!-- Header fixed — tidak ikut scroll -->
      <table class="w-full text-sm border-b">
        <colgroup>
          <col style="width: 28%" />
          <col style="width: 26%" />
          <col style="width: 12%" />
          <col style="width: 12%" />
          <col style="width: 10%" />
          <col style="width: 12%" />
        </colgroup>
        <thead>
          <tr class="border-b bg-muted/50">
            <th class="pl-6 py-3 text-left text-xs font-medium text-muted-foreground">Nama Sekolah</th>
            <th class="py-3 text-left text-xs font-medium text-muted-foreground">Kepala Sekolah</th>
            <th class="py-3 text-center text-xs font-medium text-muted-foreground">Jenjang</th>
            <th class="py-3 text-center text-xs font-medium text-muted-foreground">Akreditasi</th>
            <th class="py-3 text-right text-xs font-medium text-muted-foreground">Kelas</th>
            <th class="pr-6 py-3 text-right text-xs font-medium text-muted-foreground">Siswa</th>
          </tr>
        </thead>
      </table>

      <!-- Body scrollable -->
      <div class="h-[272px] overflow-y-auto">
        <table class="w-full text-sm">
          <colgroup>
            <col style="width: 28%" />
            <col style="width: 26%" />
            <col style="width: 12%" />
            <col style="width: 12%" />
            <col style="width: 10%" />
            <col style="width: 12%" />
          </colgroup>
          <tbody>
            <tr
              v-for="(s, i) in sekolahData"
              :key="i"
              v-motion
              :initial="tableRowFade.initial"
              :visible-once="{ ...tableRowFade.visible, transition: { ...tableRowFade.visible.transition, delay: props.delay + 100 + (i * 50) } }"
              class="border-b transition-colors hover:bg-muted/50 cursor-default"
            >
              <td class="pl-6 py-3 font-medium text-sm">{{ s.nama }}</td>
              <td class="py-3 text-muted-foreground text-sm">{{ s.kepala }}</td>
              <td class="py-3 text-center">
                <Badge variant="outline" class="text-xs font-semibold">{{ s.jenjang }}</Badge>
              </td>
              <td class="py-3 text-center">
                <Badge
                  :variant="s.akreditasi === 'A' ? 'default' : 'secondary'"
                  class="text-xs font-bold"
                >
                  {{ s.akreditasi }}
                </Badge>
              </td>
              <td class="py-3 text-right text-sm text-muted-foreground">{{ s.kelas }}</td>
              <td class="pr-6 py-3 text-right">
                <Badge variant="secondary" class="font-mono text-xs">
                  {{ s.siswa.toLocaleString('id-ID') }}
                </Badge>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </WidgetCard>

    <WidgetProgressList
      title="Distribusi SDM"
      description="Komposisi tenaga pendidik & staff"
      cardClass="lg:col-span-2"
      :items="sdmProgressData"
      :delay="delay + 100"
      illustration="graduation_cap"
    >
      <!-- Divider / Ringkasan -->
      <div class="border-t pt-4">
        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Ringkasan</p>
        <div class="grid grid-cols-2 gap-3">
          <div
            v-for="stat in sdmQuickStats"
            :key="stat.label"
            class="rounded-lg bg-muted/50 p-3 space-y-0.5"
          >
            <p class="text-[10px] text-muted-foreground leading-tight">{{ stat.label }}</p>
            <p class="text-sm font-bold">{{ stat.value }}</p>
          </div>
        </div>
      </div>
    </WidgetProgressList>
  </div>
</template>
