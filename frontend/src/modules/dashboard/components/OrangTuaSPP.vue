<script setup lang="ts">
import { Wallet, History } from 'lucide-vue-next'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
</script>

<template>
  <Card class="flex flex-col justify-between">
    <CardHeader class="pb-2">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Wallet class="size-4 text-primary" />
          <CardTitle class="text-base font-semibold">Status SPP</CardTitle>
        </div>
        <Badge variant="destructive" class="text-xs">Belum Bayar</Badge>
      </div>
      <CardDescription class="text-xs">Tagihan Bulan Oktober 2026</CardDescription>
    </CardHeader>

    <CardContent class="flex flex-col gap-4 flex-1 justify-between">
      <!-- Nominal -->
      <div>
        <p class="text-3xl font-bold tracking-tight">Rp 1.250.000</p>
        <p class="text-xs text-muted-foreground mt-1">
          Jatuh tempo: <span class="text-rose-500 font-semibold">30 Oktober 2026</span>
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
    </CardContent>
  </Card>
</template>
