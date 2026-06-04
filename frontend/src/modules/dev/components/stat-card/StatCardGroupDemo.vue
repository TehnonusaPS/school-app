<script setup>
import StatCard from '@/components/stat-card/StatCard.vue'
import StatCardGrid from '@/components/stat-card/StatCardGrid.vue'
import { Users, UserCheck, Wallet } from 'lucide-vue-next'
</script>

<template>
  <div class="space-y-10 w-full">
    <!-- Group of 1 Card -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 1 Card (Full Width - Mobile, Tablet, & Desktop)
      </div>
      <StatCardGrid cols="1">
        <StatCard
          label="Total Siswa"
          value="402"
          sub="siswa aktif"
          :icon="Users"
          color="blue"
          variant="default"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 2 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 2 Cards (2 Columns - Mobile, Tablet, & Desktop)
      </div>
      <StatCardGrid cols="2">
        <StatCard
          label="Total Siswa"
          value="402"
          sub="siswa aktif"
          :icon="Users"
          color="blue"
          variant="default"
        />

        <StatCard
          label="Absensi Hari Ini"
          value="378"
          sub="94% hadir"
          trend="+2% dari kemarin"
          :icon="UserCheck"
          color="emerald"
          variant="up"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 3 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 3 Cards (2 Columns on Mobile & Tablet with bottom card full width, 3 Columns on Desktop - Kustom Stagger Delay 500ms)
      </div>
      <StatCardGrid
        cols="3"
        :delay="500"
      >
        <StatCard
          label="Total Siswa"
          value="402"
          sub="siswa aktif"
          :icon="Users"
          color="blue"
          variant="default"
        />

        <StatCard
          label="Absensi Hari Ini"
          value="378"
          sub="94% hadir"
          trend="+2% dari kemarin"
          :icon="UserCheck"
          color="emerald"
          variant="up"
        />

        <StatCard
          label="Total Anggaran"
          value="78.5"
          sub="Realisasi Anggaran"
          :progress="78.5"
          :icon="Wallet"
          color="violet"
          variant="progress"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 4 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 4 Cards (2 Columns on Mobile & Tablet, 4 Columns on Desktop - Kustom Stagger Delay 300ms)
      </div>
      <StatCardGrid
        cols="4"
        :delay="300"
      >
        <StatCard
          label="Total Siswa"
          value="402"
          sub="siswa aktif"
          :icon="Users"
          color="blue"
          variant="default"
        />

        <StatCard
          label="Absensi Hari Ini"
          value="378"
          sub="94% hadir"
          trend="+2% dari kemarin"
          :icon="UserCheck"
          color="emerald"
          variant="up"
        />

        <StatCard
          label="Total Anggaran"
          value="78.5"
          sub="Realisasi Anggaran"
          :progress="78.5"
          :icon="Wallet"
          color="violet"
          variant="progress"
        />

        <StatCard
          label="Tingkat Kelulusan"
          value="100%"
          sub="stabil dibanding tahun lalu"
          trend="0% perubahan"
          :icon="Users"
          color="primary"
          variant="neutral"
        />
      </StatCardGrid>
    </div>

    <!-- Group of 5 Cards -->
    <div class="space-y-3">
      <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Demo 5 Cards (2 Columns on Mobile & Tablet with bottom card full width, 5 Columns on
        Desktop)
      </div>
      <StatCardGrid cols="5">
        <StatCard
          label="Total Siswa"
          value="402"
          sub="siswa aktif"
          :icon="Users"
          color="blue"
          variant="default"
        />

        <StatCard
          label="Absensi Hari Ini"
          value="378"
          sub="94% hadir"
          trend="+2% dari kemarin"
          :icon="UserCheck"
          color="emerald"
          variant="up"
        />

        <StatCard
          label="Total Anggaran"
          value="78.5"
          sub="Realisasi Anggaran"
          :progress="78.5"
          :icon="Wallet"
          color="violet"
          variant="progress"
        />

        <StatCard
          label="Pendaftaran Baru"
          value="12"
          sub="siswa mendaftar"
          trend="-4% dari minggu lalu"
          :icon="Users"
          color="amber"
          variant="down"
        />

        <StatCard
          label="Tingkat Kelulusan"
          value="100%"
          sub="stabil dibanding tahun lalu"
          trend="0% perubahan"
          :icon="Users"
          color="primary"
          variant="neutral"
        />
      </StatCardGrid>
    </div>
  </div>
</template>
