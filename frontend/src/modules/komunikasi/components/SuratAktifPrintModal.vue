<script setup>
import { computed, ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { Printer, X } from 'lucide-vue-next'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'

const props = defineProps({
  open: { type: Boolean, default: false },
  data: { type: Object, default: () => null },
})

const emit = defineEmits(['update:open'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val),
})

// ── CSS Zoom untuk tampilan mobile ──────────────────────
// Kita pakai CSS `zoom` (bukan transform:scale) karena zoom
// tetap berpartisipasi dalam layout flow — tidak perlu
// mengakali margin negatif.
const A4_PX = 794
const paperZoom = ref(1)
const bodyRef = ref(null)

function recalcZoom() {
  if (!bodyRef.value) return
  const available = bodyRef.value.clientWidth - 32 // kurangi padding p-4
  paperZoom.value = available < A4_PX ? +(available / A4_PX).toFixed(4) : 1
}

// Hitung ulang setelah dialog selesai animasi buka
watch(
  () => props.open,
  async (val) => {
    if (val) {
      await nextTick()
      setTimeout(recalcZoom, 80)
    }
  },
)

function onResize() {
  if (props.open) recalcZoom()
}

onMounted(() => window.addEventListener('resize', onResize))
onBeforeUnmount(() => window.removeEventListener('resize', onResize))

// ── Print ────────────────────────────────────────────────
function handlePrint() {
  const el = document.getElementById('printable-area')
  if (!el) return

  const iframe = document.createElement('iframe')
  Object.assign(iframe.style, { position: 'absolute', width: '0', height: '0', border: 'none' })
  document.body.appendChild(iframe)

  const styles = Array.from(document.styleSheets)
    .flatMap((ss) => {
      try { return Array.from(ss.cssRules).map((r) => r.cssText) }
      catch { return [] }
    })
    .join('\n')

  const doc = iframe.contentWindow.document
  doc.open()
  doc.write(`
    <html><head>
      <title>Surat Keterangan Aktif</title>
      <style>${styles} body{background:#fff!important;margin:0;padding:0} ::-webkit-scrollbar{display:none}</style>
    </head><body>
      <div style="padding:1cm;max-width:210mm;margin:auto">${el.innerHTML}</div>
    </body></html>
  `)
  doc.close()

  setTimeout(() => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    setTimeout(() => document.body.removeChild(iframe), 1000)
  }, 500)
}

// ── Letter number ────────────────────────────────────────
const letterNumber = computed(() => {
  if (!props.data) return ''
  return `421.2/${String(props.data.id).padStart(3, '0')}/SD-TP1/V/2026`
})
</script>

<template>
  <Dialog :open="isOpen" @update:open="isOpen = $event">
    <!--
      max-w-[860px] → cukup untuk kertas A4 + sedikit padding di desktop
      w-[95vw]      → hampir penuh di mobile
    -->
    <DialogContent
      :show-close-button="false"
      class="w-[95vw] max-w-[860px] p-0 overflow-hidden bg-slate-100 dark:bg-slate-900 border-none rounded-xl"
    >
      <!-- Header -->
      <DialogHeader
        class="px-4 sm:px-6 py-3 sm:py-4 border-b border-border/40 bg-white dark:bg-slate-950 flex flex-row items-center justify-between gap-3"
      >
        <DialogTitle class="text-sm sm:text-xl font-bold leading-tight">
          Preview Surat Keterangan Aktif
        </DialogTitle>
        <div class="flex items-center gap-2 shrink-0">
          <Button
            @click="handlePrint"
            size="sm"
            class="gap-1.5 rounded-full px-3 sm:px-5 shadow-sm text-xs sm:text-sm"
          >
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

      <!--
        Area scroll VERTIKAL saja. CSS zoom membuat kertas A4
        otomatis mengecil di layar sempit sehingga tidak perlu
        scroll horizontal sama sekali.
      -->
      <div
        ref="bodyRef"
        class="overflow-y-auto overflow-x-hidden bg-slate-100/80 dark:bg-slate-900/80 p-4 sm:p-6"
        style="max-height: 80vh;"
      >
        <!--
          Wrapper: overflow-x:hidden + lebar penuh.
          CSS zoom mengecilkan kertas secara layout, bukan hanya visual,
          sehingga wrapper tidak perlu lebih lebar dari layar.
        -->
        <div class="w-full overflow-x-hidden">
          <!-- A4 Paper -->
          <div
            id="printable-area"
            class="bg-white text-black mx-auto shadow-md rounded-sm"
            :style="{
              width: '794px',
              minHeight: '1123px',
              padding: '48px',
              zoom: paperZoom,
            }"
          >
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
              <h3 class="text-lg font-bold underline underline-offset-4">SURAT KETERANGAN SISWA AKTIF</h3>
              <p class="mt-1">Nomor : {{ letterNumber }}</p>
            </div>

            <!-- BODY -->
            <div class="font-serif leading-relaxed text-justify space-y-6 text-[15px]">
              <p>Kepala Sekolah SD Tehnonusa Prima I dengan ini menerangkan :</p>

              <table class="w-full ml-8">
                <tbody>
                  <tr>
                    <td class="w-48 py-1">Nama</td>
                    <td class="w-4 py-1">:</td>
                    <td class="py-1 font-bold">{{ data?.nama || '-' }}</td>
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
                  <tr>
                    <td class="py-1">Semester</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ data?.semester || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="py-1">Tempat, Tanggal Lahir</td>
                    <td class="py-1">:</td>
                    <td class="py-1">Jakarta, {{ data?.tanggalLahir || '-' }}</td>
                  </tr>
                  <tr>
                    <td class="py-1 align-top">Alamat</td>
                    <td class="py-1 align-top">:</td>
                    <td class="py-1">{{ data?.alamat || '-' }}</td>
                  </tr>
                </tbody>
              </table>

              <p>
                Adalah benar siswa kelas <strong>{{ data?.kelas || '-' }}</strong> SD Tehnonusa Prima I,
                yang bersangkutan aktif mengikuti persekolahan pada tahun akademik
                <strong>{{ data?.tahunAkademik || '-' }}</strong> duduk pada Semester
                <strong>{{ data?.semester || '-' }}</strong>.
              </p>

              <p>
                Demikian Surat Keterangan ini kami buat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.
              </p>
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
  #printable-area {
    width: 210mm !important;
    min-height: 297mm !important;
    padding: 1cm !important;
    box-shadow: none !important;
    zoom: 1 !important;
  }
}
</style>
