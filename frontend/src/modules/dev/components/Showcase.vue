<script setup>
import ShowcaseSection, { ShowcaseItem } from './ShowcaseSection.vue'
import PageHeaderDefaultDemo from './page-header/PageHeaderDefaultDemo.vue'
import PageHeaderBackDemo from './page-header/PageHeaderBackDemo.vue'
import PageHeaderActionsDemo from './page-header/PageHeaderActionsDemo.vue'
import StatCardDefaultDemo from './stat-card/StatCardDefaultDemo.vue'
import StatCardTrendUpDemo from './stat-card/StatCardTrendUpDemo.vue'
import StatCardTrendDownDemo from './stat-card/StatCardTrendDownDemo.vue'
import StatCardTrendNeutralDemo from './stat-card/StatCardTrendNeutralDemo.vue'
import StatCardProgressDemo from './stat-card/StatCardProgressDemo.vue'
import StatCardGroupDemo from './stat-card/StatCardGroupDemo.vue'
import DataTableCardDemo from './data-table/DataTableCardDemo.vue'
import DataSheetDemo from './data-sheet/DataSheetDemo.vue'
import FormSheetDemo from './data-sheet/FormSheetDemo.vue'
import DatePickerDemo from './date-picker/DatePickerDemo.vue'
import BarChartCardDemo from './chart-card/BarChartCardDemo.vue'
import LineChartCardDemo from './chart-card/LineChartCardDemo.vue'
import DonutChartCardDemo from './chart-card/DonutChartCardDemo.vue'

const props = defineProps({
  searchQuery: { type: String, default: '' }
})

const match = keywords => {
  if (!props.searchQuery) return true
  return keywords.toLowerCase().includes(props.searchQuery.toLowerCase())
}
</script>

<template>
  <div class="space-y-12">
    <!-- PAGE HEADER -->
    <ShowcaseSection
      v-show="match('page header halaman judul')"
      title="Page Header"
      description="Header halaman standar yang mendukung animasi v-motion."
    >
      <ShowcaseItem label="Varian Default (Hanya Judul & Deskripsi)">
        <PageHeaderDefaultDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Back (Dengan Tombol Kembali)">
        <PageHeaderBackDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Actions (Dengan Tombol Kembali & Multi Tombol Aksi)">
        <PageHeaderActionsDemo />
      </ShowcaseItem>
    </ShowcaseSection>

    <!-- STAT CARD -->
    <ShowcaseSection
      v-show="match('stat card statistik dashboard progress trend')"
      title="Stat Card"
      description="Kartu statistik responsif dengan desain Glassmorphism. Mendukung animasi masuk staggered otomatis via <StatCardGrid> dengan prop delay kustom (default 100ms)."
    >
      <ShowcaseItem label="Varian Default (Stat Card Biasa)">
        <StatCardDefaultDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Trend Up (Kenaikan)">
        <StatCardTrendUpDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Trend Down (Penurunan)">
        <StatCardTrendDownDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Trend Neutral (Statis / Stabil)">
        <StatCardTrendNeutralDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Progress (Dengan Progress Bar)">
        <StatCardProgressDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Varian Group (Beberapa Card dalam Grid)">
        <StatCardGroupDemo />
      </ShowcaseItem>
    </ShowcaseSection>

    <!-- CHART CARD -->
    <ShowcaseSection
      v-show="match('chart card grafis diagram batang garis donat')"
      title="Chart Card"
      description="Kartu visualisasi data responsif berbasis Unovis dengan gaya Glassmorphism yang konsisten dengan Stat Card."
    >
      <ShowcaseItem label="Bar Chart Card (Grafik Batang Grouped)">
        <BarChartCardDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Line Chart Card (Grafik Garis Multi-series)">
        <LineChartCardDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Donut Chart Card (Diagram Donat dengan Label Pusat)">
        <DonutChartCardDemo />
      </ShowcaseItem>
    </ShowcaseSection>

    <!-- DATA TABLE -->
    <ShowcaseSection
      v-show="match('data table tabel kartu pagination filter')"
      title="Data Table"
      description="Komponen tabel data lengkap yang mendukung filtering, pagination, custom cell templating, dan animasi staggered untuk baris tabel."
    >
      <ShowcaseItem label="Data Table Card (Tabel Lengkap dengan Card, Toolbar & Pagination)">
        <DataTableCardDemo />
      </ShowcaseItem>
    </ShowcaseSection>

    <!-- DATA SHEET -->
    <ShowcaseSection
      v-show="match('data sheet panel drawer detail profil form edit create')"
      title="Data Sheet"
      description="Komponen panel laci samping (side drawer) untuk menampilkan rincian detail data lengkap secara terstruktur, atau melakukan entry/edit data menggunakan form-sheet."
    >
      <ShowcaseItem label="Data Sheet Demo (Detail Profil Read-Only)">
        <DataSheetDemo />
      </ShowcaseItem>

      <ShowcaseItem label="Form Sheet Demo (Form Input Create & Edit)">
        <FormSheetDemo />
      </ShowcaseItem>
    </ShowcaseSection>

    <!-- DATE PICKER -->
    <ShowcaseSection
      v-show="match('date picker kalender tanggal')"
      title="Date Picker"
      description="Komponen pemilih tanggal (date picker) menggunakan kombinasi Popover dan Calendar."
    >
      <ShowcaseItem label="DatePicker Default (Month and Year Selection)">
        <DatePickerDemo />
      </ShowcaseItem>
    </ShowcaseSection>
  </div>
</template>
