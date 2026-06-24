<script setup>
import { ref } from 'vue'
import DataSheet from '@/components/data-sheet/DataSheet.vue'
import { Button } from '@/components/ui/button'
import { User } from 'lucide-vue-next'
import { rawGuruItem } from './data/dataSheetDemos'
import { guruSheetSections } from './data/dataSheetSchema'

const isOpen = ref(false)
</script>

<template>
  <div class="flex items-center gap-3">
    <Button
      variant="outline"
      @click="isOpen = true"
    >
      <User class="mr-2 h-4 w-4" />
      Buka Detail Profil
    </Button>

    <DataSheet
      v-model:open="isOpen"
      :item="rawGuruItem"
      title-key="nama"
      description-key="nuptk"
      description-prefix="NUPTK: "
      avatar-key="foto"
      badge-key="status"
      :sections="guruSheetSections"
    />
  </div>
</template>
