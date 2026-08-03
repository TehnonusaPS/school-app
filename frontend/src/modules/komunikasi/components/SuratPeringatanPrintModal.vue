<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Printer, X } from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  },
  data: {
    type: Object,
    default: () => null
  }
})

const emit = defineEmits(['update:open'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const A4_PX = 794
const paperZoom = ref(1)
const bodyRef = ref(null)

function recalcZoom() {
  if (!bodyRef.value) return
  const available = bodyRef.value.clientWidth - 32
  paperZoom.value = available < A4_PX ? +(available / A4_PX).toFixed(4) : 1
}

watch(
  () => props.open,
  async (val) => {
    if (val) {
      await nextTick()
      setTimeout(recalcZoom, 80)
    }
  }
)

function onResize() {
  if (props.open) recalcZoom()
}

onMounted(() => window.addEventListener('resize', onResize))
onBeforeUnmount(() => window.removeEventListener('resize', onResize))

function handlePrint() {
  const printableArea = document.getElementById('printable-area-peringatan')
  if (!printableArea) return

  const iframe = document.createElement('iframe')
  iframe.style.position = 'absolute'
  iframe.style.width = '0'
  iframe.style.height = '0'
  iframe.style.border = 'none'
  document.body.appendChild(iframe)

  const styles = Array.from(document.styleSheets)
    .flatMap(styleSheet => {
      try {
        return Array.from(styleSheet.cssRules).map(rule => rule.cssText)
      } catch (e) {
        return []
      }
    })
    .join('\n')

  const doc = iframe.contentWindow.document
  doc.open()
  doc.write(`
    <html>
      <head>
        <title>Print Surat</title>
        <style>
          ${styles}
          body { 
            background-color: white !important; 
            margin: 0; 
            padding: 0;
          }
          ::-webkit-scrollbar { display: none; }
        </style>
      </head>
      <body>
        <div style="padding: 1cm; max-width: 210mm; margin: auto;">
          ${printableArea.innerHTML}
        </div>
      </body>
    </html>
  `)
  doc.close()

  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    setTimeout(() => {
      document.body.removeChild(iframe)
    }, 1000)
  }, 500)
}

const letterNumber = computed(() => {
  if (!props.data) return ''
  const idStr = String(props.data.id).padStart(3, '0')
  const code = props.data.jenisSurat === 'Surat Pelanggaran' ? 'PEL' : 'TUNG'
  return `421.2/${idStr}/SD-TP1/${code}/V/2026`
})
</script>

<template>
  <Dialog :open="isOpen" @update:open="isOpen = $event">
    <DialogContent class="w-[95vw] max-w-[860px] p-0 overflow-hidden bg-slate-100 dark:bg-slate-900 border-none rounded-xl">
      <DialogHeader class="px-4 sm:px-6 py-3 sm:py-4 border-b border-border/40 bg-white dark:bg-slate-950 flex flex-row items-center justify-between gap-3 sticky top-0 z-10 print:hidden">
        <DialogTitle class="text-sm sm:text-xl font-bold leading-tight">Preview {{ data?.jenisSurat || 'Surat' }}</DialogTitle>
        <div class="flex items-center gap-2 shrink-0">
          <Button @click="handlePrint" size="sm" class="gap-1.5 rounded-full px-3 sm:px-5 shadow-sm text-xs sm:text-sm">
            <Printer class="size-3.5 sm:size-4" />
            Cetak
          </Button>
          <Button
            variant="ghost"
            size="icon"
            class="h-8 w-8 rounded-full hover:bg-muted/80"
            @click="isOpen = false"
          >
            <X class="size-4" />
          </Button>
        </div>
      </DialogHeader>

      <div ref="bodyRef" class="overflow-y-auto overflow-x-hidden bg-slate-100/80 dark:bg-slate-900/80 p-4 sm:p-6" style="max-height: 80vh;">
        <div class="w-full overflow-x-hidden">
          <div id="printable-area-peringatan" class="bg-white text-black mx-auto shadow-md rounded-sm" :style="{ width: '794px', minHeight: '1123px', padding: '48px', zoom: paperZoom }">
            
            <!-- KOP SURAT -->
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-1">
              <div class="w-[100px] h-[100px] shrink-0 flex items-center justify-center border border-dashed border-gray-400">
                <span class="text-gray-400 text-xs text-center">Logo<br>Sekolah</span>
              </div>
              <div class="flex-1 text-center font-serif">
                <h2 class="text-xl font-bold uppercase">KEMENTRIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</h2>
                <h1 class="text-2xl font-bold uppercase mt-1">SDN TEHNONUSA PRIMA I</h1>
                <p class="text-sm mt-1">Jl. Pendidikan No. 1, Kec. Ilmu, Kota Pengetahuan 12345</p>
                <p class="text-sm">Email: sdn_tp1@sekolah.sch.id | Telp: (021) 12345678</p>
              </div>
              <div class="w-[100px] shrink-0"></div>
            </div>
            <div class="border-b border-black mb-8"></div>

            <!-- TITLE -->
            <div class="text-center mb-10 font-serif">
              <h3 class="text-lg font-bold uppercase underline underline-offset-4">
                {{ data?.jenisSurat === 'Surat Pelanggaran' ? 'SURAT PELANGGARAN DISIPLIN' : 'SURAT TUNGGAKAN' }}
              </h3>
              <p class="mt-1">Nomor : {{ letterNumber }}</p>
            </div>

            <!-- BODY -->
            <div class="font-serif leading-relaxed text-justify space-y-6 text-[15px]">
              <p>Yang bertanda tangan dibawah ini, Kepala Sekolah SD Tehnonusa Prima I dengan ini menerangkan bahwa :</p>

              <table class="w-full ml-8">
                <tbody>
                  <tr>
                    <td class="w-48 py-1">Nama</td>
                    <td class="w-4 py-1">:</td>
                    <td class="py-1 font-bold">{{ data?.namaSiswa || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="py-1">NISN</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ data?.nisn || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="py-1">Kelas</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ data?.kelas || '-' }}</td>
                  </tr>
                </tbody>
              </table>

              <!-- CONDITIONAL RENDER -->
              <template v-if="data?.jenisSurat === 'Surat Pelanggaran'">
                <p class="mt-6">
                  Memberitahukan kepada Orang Tua/Wali telah terjadi suatu pelanggaran disiplin, Pelanggaran yang dilakukan oleh siswa/siswi tersebut, yaitu <strong>{{ data.perihalPelanggaran || '-' }}</strong>
                </p>
              </template>

              <template v-else-if="data?.jenisSurat === 'Surat Tunggakan'">
                <table class="w-full border-collapse border border-black text-center mt-6 mb-6">
                  <thead>
                    <tr class="bg-gray-100">
                      <th class="border border-black py-2 px-4 font-bold">Nama</th>
                      <th class="border border-black py-2 px-4 font-bold">NISN</th>
                      <th class="border border-black py-2 px-4 font-bold">Total Tunggakan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="border border-black py-2 px-4 text-left">{{ data?.namaSiswa || '-' }}</td>
                      <td class="border border-black py-2 px-4">{{ data?.nisn || '-' }}</td>
                      <td class="border border-black py-2 px-4 font-semibold text-rose-600 print:text-black">Rp {{ data?.jumlahTunggakan || '0' }}</td>
                    </tr>
                  </tbody>
                </table>

                <p class="mt-6">
                  Memberitahukan kepada Orang Tua/Wali telah terjadi suatu Tunggakan, kami berharap dapat menyelesaikan kewajiban tunggakan yang ada.
                </p>
              </template>

            </div>

            <!-- SIGNATURE -->
            <div class="mt-20 flex justify-end font-serif">
              <div class="text-center">
                <p>Jakarta, {{ data?.tanggalDibuat || new Date().toISOString().split('T')[0] }}</p>
                <p class="mb-24">Kepala Sekolah</p>
                <p class="font-bold underline underline-offset-2">Dr. H. Ahmad Dahlan, M.Pd.</p>
                <p>NIP. 19700101 199512 1 001</p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
@media print {
  #printable-area-peringatan {
    width: 210mm !important;
    min-height: 297mm !important;
    padding: 1cm !important;
    box-shadow: none !important;
    zoom: 1 !important;
  }
}
</style>
