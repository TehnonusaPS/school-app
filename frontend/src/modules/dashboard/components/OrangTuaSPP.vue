<script setup lang="ts">
import { Wallet, History } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import WidgetCard from '@/components/dashboard-widget/WidgetCard.vue'
import { sppData } from '../data/orangTuaSPPData'
</script>

<template>
  <WidgetCard
    title="Status SPP"
    :description="`Tagihan Bulan ${sppData.bulan}`"
    :icon="Wallet"
    cardClass="flex flex-col justify-between"
    contentClass="flex flex-col gap-4 flex-1 justify-between"
  >
    <template #header-action>
      <Badge variant="destructive" class="text-xs">{{ sppData.status }}</Badge>
    </template>

    <!-- Nominal -->
    <div>
      <p class="text-3xl font-bold tracking-tight">{{ sppData.nominal }}</p>
      <p class="text-xs text-muted-foreground mt-1">
        Jatuh tempo: <span class="text-rose-500 font-semibold">{{ sppData.jatuhTempo }}</span>
      </p>
    </div>

    <!-- Actions -->
    <div class="space-y-2">
      <Button class="w-full gap-2">
        <Wallet class="size-4" />
        Bayar Sekarang
      </Button>
      <Button variant="ghost" class="w-full gap-1.5 text-xs text-muted-foreground hover:text-foreground h-8">
        <History class="size-3.5" />
        Lihat Riwayat Pembayaran
      </Button>
    </div>
  </WidgetCard>
</template>
