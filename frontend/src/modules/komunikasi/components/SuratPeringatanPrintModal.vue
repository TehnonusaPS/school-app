<script setup>
import { computed } from 'vue'
import { Printer } from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { ScrollArea } from '@/components/ui/scroll-area'

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
    .map(styleSheet => {
      try {
        return Array.from(styleSheet.cssRules)
          .map(rule => rule.cssText)
          .join('')
      } catch (e) {
        return ''
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
    <DialogContent class="sm:max-w-4xl p-0 overflow-hidden bg-slate-100 dark:bg-slate-900 border-none rounded-xl">
      <DialogHeader class="px-6 py-4 border-b border-border/40 bg-white dark:bg-slate-950 flex flex-row items-center justify-between sticky top-0 z-10 print:hidden">
        <DialogTitle class="text-xl font-bold">Preview {{ data?.jenisSurat || 'Surat' }}</DialogTitle>
        <Button @click="handlePrint" class="gap-2 rounded-full px-6 shadow-sm">
          <Printer class="size-4" />
          Cetak Dokumen
        </Button>
      </DialogHeader>

      <ScrollArea class="h-[80vh] bg-slate-100/50 dark:bg-slate-900/50 p-6 sm:p-10 print:h-auto print:p-0 print:bg-transparent">
        <div id="printable-area-peringatan" class="bg-white text-black max-w-[210mm] min-h-[297mm] mx-auto p-12 shadow-md print:shadow-none print:m-0 print:p-8 rounded-sm">
          
          <!-- KOP SURAT -->
          <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-1">
            <div class="w-[100px] h-[100px] flex items-center justify-center border border-dashed border-gray-400">
              <span class="text-gray-400 text-xs text-center">Logo<br>Sekolah</span>
            </div>
            <div class="flex-1 text-center font-serif">
              <h2 class="text-xl font-bold uppercase">KEMENTRIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</h2>
              <h1 class="text-2xl font-bold uppercase mt-1">SDN TEHNONUSA PRIMA I</h1>
              <p class="text-sm mt-1">Jl. Pendidikan No. 1, Kec. Ilmu, Kota Pengetahuan 12345</p>
              <p class="text-sm">Email: sdn_tp1@sekolah.sch.id | Telp: (021) 12345678</p>
            </div>
            <div class="w-[100px]"></div>
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
      </ScrollArea>
    </DialogContent>
  </Dialog>
</template>
