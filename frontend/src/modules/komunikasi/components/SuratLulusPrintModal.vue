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

// Mock values for school exam subjects based on item ID to keep it dynamic but predictable
const listNilai = computed(() => {
  if (!props.data) return []
  const seed = props.data.id || 1
  return [
    { no: 1, mapel: 'Pendidikan Agama & Budi Pekerti', nilai: 88 + (seed % 9) },
    { no: 2, mapel: 'Pendidikan Pancasila & Kewarganegaraan', nilai: 85 + (seed % 11) },
    { no: 3, mapel: 'Bahasa Indonesia', nilai: 87 + (seed % 10) },
    { no: 4, mapel: 'Matematika', nilai: 80 + (seed % 13) },
    { no: 5, mapel: 'Ilmu Pengetahuan Alam (IPA)', nilai: 84 + (seed % 12) },
    { no: 6, mapel: 'Ilmu Pengetahuan Sosial (IPS)', nilai: 82 + (seed % 14) },
    { no: 7, mapel: 'Seni Budaya & Prakarya', nilai: 86 + (seed % 8) },
    { no: 8, mapel: 'Pendidikan Jasmani, Olahraga, & Kesehatan', nilai: 85 + (seed % 9) }
  ]
})

const totalNilai = computed(() => {
  return listNilai.value.reduce((acc, curr) => acc + curr.nilai, 0)
})

const rataRataNilai = computed(() => {
  if (listNilai.value.length === 0) return 0
  const avg = totalNilai.value / listNilai.value.length
  return avg.toFixed(2)
})

function handlePrint() {
  const printableArea = document.getElementById('printable-area-skl')
  if (!printableArea) return

  // Create an iframe to hold the print content
  const iframe = document.createElement('iframe')
  iframe.style.position = 'absolute'
  iframe.style.width = '0'
  iframe.style.height = '0'
  iframe.style.border = 'none'
  document.body.appendChild(iframe)

  // Get styles from current document to ensure Tailwind works
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
        <title>Print Surat Keterangan Lulus</title>
        <style>
          ${styles}
          body { 
            background-color: white !important; 
            margin: 0; 
            padding: 0;
          }
          /* Hide scrollbars in print */
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

  // Wait for images/styles to load then print
  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    
    // Cleanup after print dialog closes
    setTimeout(() => {
      document.body.removeChild(iframe)
    }, 1000)
  }, 500)
}

// Generate a fake letter number based on ID
const letterNumber = computed(() => {
  if (!props.data) return ''
  const idStr = String(props.data.id).padStart(3, '0')
  return `421.3/${idStr}/SD-TP1/V/2026`
})
</script>

<template>
  <Dialog :open="isOpen" @update:open="isOpen = $event">
    <DialogContent class="sm:max-w-4xl p-0 overflow-hidden bg-slate-100 dark:bg-slate-900 border-none rounded-xl">
      <DialogHeader class="px-6 py-4 border-b border-border/40 bg-white dark:bg-slate-950 flex flex-row items-center justify-between sticky top-0 z-10 print:hidden">
        <DialogTitle class="text-xl font-bold">Preview Surat Keterangan Lulus</DialogTitle>
        <Button @click="handlePrint" class="gap-2 rounded-full px-6 shadow-sm bg-emerald-600 hover:bg-emerald-700 text-white">
          <Printer class="size-4" />
          Cetak Dokumen
        </Button>
      </DialogHeader>

      <ScrollArea class="h-[80vh] bg-slate-100/50 dark:bg-slate-900/50 p-6 sm:p-10 print:h-auto print:p-0 print:bg-transparent">
        <!-- Paper Sheet -->
        <div id="printable-area-skl" class="bg-white text-black max-w-[210mm] min-h-[297mm] mx-auto p-12 shadow-md print:shadow-none print:m-0 print:p-8 rounded-sm font-serif">
          
          <!-- KOP SURAT -->
          <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-1">
            <div class="w-[100px] h-[100px] flex items-center justify-center border border-dashed border-gray-400">
              <span class="text-gray-400 text-xs text-center">Logo<br>Sekolah</span>
            </div>
            <div class="flex-1 text-center">
              <h2 class="text-lg font-bold uppercase leading-tight">KEMENTERIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</h2>
              <h1 class="text-2xl font-bold uppercase mt-1">SDN TEHNONUSA PRIMA I</h1>
              <p class="text-xs mt-1 font-sans">Jl. Pendidikan No. 1, Kec. Ilmu, Kota Pengetahuan 12345</p>
              <p class="text-xs font-sans">Email: sdn_tp1@sekolah.sch.id | Telp: (021) 12345678</p>
            </div>
            <div class="w-[100px]"></div> <!-- Balancer for centering -->
          </div>
          <!-- Double border line -->
          <div class="border-b border-black mb-8"></div>

          <!-- TITLE -->
          <div class="text-center mb-8">
            <h3 class="text-lg font-bold underline underline-offset-4 uppercase">SURAT KETERANGAN LULUS</h3>
            <p class="mt-1 text-sm">Nomor : {{ letterNumber }}</p>
          </div>

          <!-- BODY -->
          <div class="leading-relaxed text-justify space-y-6 text-[14px]">
            <p>
              Yang bertanda tangan di bawah ini, Kepala Sekolah SD Tehnonusa Prima I dengan ini menerangkan bahwa :
            </p>

            <table class="w-full ml-6">
              <tbody>
                <tr>
                  <td class="w-48 py-1">Nama</td>
                  <td class="w-4 py-1">:</td>
                  <td class="py-1 font-bold">{{ data?.nama || '-' }}</td>
                </tr>
                <tr>
                  <td class="py-1">Tempat, Tanggal Lahir</td>
                  <td class="py-1">:</td>
                  <td class="py-1">
                    {{ data?.tempatLahir || '-' }}, {{ data?.tanggalLahir || '-' }}
                  </td>
                </tr>
                <tr>
                  <td class="py-1">Nama Orangtua/Wali</td>
                  <td class="py-1">:</td>
                  <td class="py-1">{{ data?.namaOrangTua || '-' }}</td>
                </tr>
                <tr>
                  <td class="py-1">NISN</td>
                  <td class="py-1">:</td>
                  <td class="py-1">{{ data?.nisn || '-' }}</td>
                </tr>
                <tr>
                  <td class="py-1">NIS</td>
                  <td class="py-1">:</td>
                  <td class="py-1">{{ data?.nis || '-' }}</td>
                </tr>
              </tbody>
            </table>

            <p>
              Yang bersangkutan dinyatakan <strong>LULUS</strong> berdasarkan hasil keputusan rapat Dewan Guru SD Tehnonusa Prima I dengan nilai sebagai berikut :
            </p>

            <!-- NILAI TABLE -->
            <div class="my-4">
              <table class="w-full border-collapse border border-black text-center text-xs">
                <thead>
                  <tr class="bg-slate-50">
                    <th class="border border-black py-2 px-3 w-[60px] font-bold">No</th>
                    <th class="border border-black py-2 px-3 text-left font-bold">Mata Pelajaran</th>
                    <th class="border border-black py-2 px-3 w-[200px] font-bold">Nilai Ujian Sekolah</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in listNilai" :key="item.no">
                    <td class="border border-black py-1.5 px-3 font-sans">{{ item.no }}</td>
                    <td class="border border-black py-1.5 px-3 text-left">{{ item.mapel }}</td>
                    <td class="border border-black py-1.5 px-3 font-bold font-sans">{{ item.nilai }}</td>
                  </tr>
                  <tr class="font-bold">
                    <td colspan="2" class="border border-black py-2 px-3 text-center uppercase">JUMLAH</td>
                    <td class="border border-black py-2 px-3 font-sans text-center">{{ totalNilai }}</td>
                  </tr>
                  <tr class="font-bold">
                    <td colspan="2" class="border border-black py-2 px-3 text-center uppercase">RATA - RATA</td>
                    <td class="border border-black py-2 px-3 font-sans text-center">{{ rataRataNilai }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <p>
              Surat Keterangan Lulus ini berlaku sementara sampai dengan diterbitkannya ijazah asli kepada yang bersangkutan.
            </p>
          </div>

          <!-- SIGNATURE -->
          <div class="mt-12 flex justify-end">
            <div class="text-center text-[14px]">
              <p>Jakarta, {{ data?.tanggalDibuat || new Date().toISOString().split('T')[0] }}</p>
              <p class="mb-20">Kepala Sekolah</p>
              
              <!-- CAP/TTD DIGITAL PLACEHOLDER -->
              <div class="my-2 h-14 flex items-center justify-center select-none text-slate-300 dark:text-slate-800 text-[10px] font-sans italic border border-dashed border-slate-200 w-36 mx-auto rounded">
                [ Cap & TTD Digital ]
              </div>
              
              <p class="font-bold underline underline-offset-2 mt-4">Dr. H. Ahmad Dahlan, M.Pd.</p>
              <p class="text-xs">NIP. 19700101 199512 1 001</p>
            </div>
          </div>
        </div>
      </ScrollArea>
    </DialogContent>
  </Dialog>
</template>
