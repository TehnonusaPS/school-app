<script setup>
import { ref } from 'vue'
import { FolderOpen } from 'lucide-vue-next'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import AppCard from '@/components/app-card/AppCard.vue'
import StatsCard from '@/components/stats-card/StatsCard.vue'
import ActivityCard from '@/components/activity-card/ActivityCard.vue'
import { mockStats } from '../data/mockSiswaStatCards'
import { mockAktivitas } from '../data/mockSiswaAktivitas'
import { mockMateriList } from '../data/mockSiswaMateri'

const selectedMapel = ref('matematika')
const selectedSemester = ref('semester-1')
const selectedMateri = ref('')
</script>

<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <StatsCard :stats="mockStats" />

    <!-- Header: Mata Pelajaran + Filters -->
    <div class="flex flex-wrap items-center gap-4">
      <h2 class="text-lg font-semibold">Mata Pelajaran</h2>
      <div class="flex gap-3">
        <Select v-model="selectedMapel">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Pilih Mata Pelajaran" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="matematika">Matematika</SelectItem>
            <SelectItem value="bahasa-indonesia">Bahasa Indonesia</SelectItem>
            <SelectItem value="ipa">IPA</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedSemester">
          <SelectTrigger class="w-[160px]">
            <SelectValue placeholder="Pilih Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="semester-1">Semester 1</SelectItem>
            <SelectItem value="semester-2">Semester 2</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid gap-6 lg:grid-cols-2">
      <!-- Left: Materi Pelajaran -->
      <AppCard header-class="pb-3" content-class="space-y-3">
        <template #header>
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="rounded-lg bg-muted p-2">
                <FolderOpen class="size-5 text-muted-foreground" />
              </div>
              <div>
                <div class="font-semibold">Matematika</div>
                <div class="text-xs text-muted-foreground">Lihat Detail Pelajaran</div>
              </div>
            </div>
            <Select v-model="selectedMateri">
              <SelectTrigger class="w-[200px]">
                <SelectValue placeholder="Pilih Materi Pelajaran" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="bab-1">Bab 1 - Aljabar</SelectItem>
                <SelectItem value="bab-2">Bab 2 - Geometri</SelectItem>
                <SelectItem value="bab-3">Bab 3 - Statistika</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </template>

        <ActivityCard
          v-for="(materi, i) in mockMateriList"
          :key="i"
          :leading-icon="materi.leadingIcon"
          :title="materi.title"
          :description="materi.description"
          :trailing-icon="materi.trailingIcon"
          :variant="materi.variant"
        />
      </AppCard>

      <!-- Right: Aktivitas Mata Pelajaran -->
      <AppCard
        title="Aktivitas Mata Pelajaran Matematika"
        description="Detail aktivitas atau kegiatan mata pelajaran dalam satu semester"
        header-class="pb-3"
        content-class="space-y-3"
      >
        <ActivityCard
          v-for="(item, i) in mockAktivitas"
          :key="i"
          :date="item.date"
          :month="item.month"
          :title="item.title"
          :description="item.description"
          :trailing-icon="item.icon"
          :variant="item.variant"
        />
      </AppCard>
    </div>
  </div>
</template>
