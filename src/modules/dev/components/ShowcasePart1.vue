<script setup>
import {
  AlertCircle,
  Terminal,
  User,
  Settings,
  BellRing,
  ChevronsUpDown,
  School,
  LayoutDashboard,
  Database,
  GraduationCap
} from 'lucide-vue-next'

// 1. Accordion
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger
} from '@/components/ui/accordion'

// 2. Alert
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'

// 3. Alert Dialog
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button' // required to trigger the alert dialog

// 4. Aspect Ratio
import { AspectRatio } from '@/components/ui/aspect-ratio'

// 5. Avatar
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

// 6. Badge
import { Badge } from '@/components/ui/badge'

// 7. Breadcrumb
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator
} from '@/components/ui/breadcrumb'

// 8. Button (already imported above)
// import { Button } from '@/components/ui/button'

// 9. Button Group
import { ButtonGroup } from '@/components/ui/button-group'

// 10. Calendar
import { Calendar } from '@/components/ui/calendar'
</script>

<template>
  <div class="p-6 space-y-12 max-w-7xl mx-auto pb-24">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-foreground">UI Components Reference</h1>
      <p class="text-muted-foreground mt-2 max-w-3xl">
        Kumpulan dokumentasi lengkap seluruh komponen UI yang ada pada aplikasi ini. Daftar ini
        dibuat berurutan sesuai alfabet (Bagian 2: 10 Komponen Pertama).
      </p>
    </div>

    <!-- 1. ACCORDION -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">1. Accordion</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menampilkan daftar konten secara vertikal yang bisa dibuka-tutup. Berguna untuk FAQ atau
          menu bersarang.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card max-w-3xl">
        <Accordion type="single" collapsible class="w-full">
          <AccordionItem value="item-1">
            <AccordionTrigger>Apakah desain ini responsif?</AccordionTrigger>
            <AccordionContent>
              Ya. Semua komponen Shadcn dirancang menggunakan Tailwind CSS dan sepenuhnya responsif
              di semua perangkat (Mobile, Tablet, Desktop).
            </AccordionContent>
          </AccordionItem>
          <AccordionItem value="item-2">
            <AccordionTrigger>Bisa ganti tema warna sesuka hati?</AccordionTrigger>
            <AccordionContent>
              Tentu. Sistem kami menggunakan CSS Variabel (berbasis OKLCH) yang secara langsung
              mengubah mood aplikasi ketika tema diganti dari Navigation Bar.
            </AccordionContent>
          </AccordionItem>
          <AccordionItem value="item-3">
            <AccordionTrigger>Apakah mendukung Dark Mode?</AccordionTrigger>
            <AccordionContent>
              Sangat mendukung. Komponen ini sudah dilengkapi pemetaan warna otomatis untuk mode
              terang maupun mode gelap tanpa harus menambahkan class manual.
            </AccordionContent>
          </AccordionItem>
        </Accordion>
      </div>
    </section>

    <!-- 2. ALERT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">2. Alert</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Peringatan untuk menarik perhatian pengguna tanpa harus mengganggu aksi mereka
          (non-blocking).
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Default Alert -->
        <Alert>
          <Terminal class="h-4 w-4" />
          <AlertTitle>Informasi Sistem</AlertTitle>
          <AlertDescription>
            Pembaruan aplikasi versi 2.1.0 akan dilakukan malam ini.
          </AlertDescription>
        </Alert>

        <!-- Destructive Alert -->
        <Alert variant="destructive">
          <AlertCircle class="h-4 w-4" />
          <AlertTitle>Kesalahan Jaringan</AlertTitle>
          <AlertDescription>
            Gagal terhubung ke database. Silakan coba kembali beberapa saat lagi.
          </AlertDescription>
        </Alert>

        <!-- Custom Emerald Alert (Success) -->
        <Alert
          class="border-emerald-500 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400"
        >
          <Terminal class="h-4 w-4 stroke-emerald-500 dark:stroke-emerald-400" />
          <AlertTitle>Berhasil!</AlertTitle>
          <AlertDescription>
            Data rapor untuk 40 siswa telah berhasil disimpan ke server.
          </AlertDescription>
        </Alert>
      </div>
    </section>

    <!-- 3. ALERT DIALOG -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">3. Alert Dialog (Modal)</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Modal konfirmasi yang mengunci layar hingga pengguna memilih sebuah aksi (misalnya
          konfirmasi hapus data).
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex gap-4">
        <AlertDialog>
          <AlertDialogTrigger as-child>
            <Button variant="destructive">Hapus Akun Siswa</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Apakah Anda benar-benar yakin?</AlertDialogTitle>
              <AlertDialogDescription>
                Tindakan ini tidak bisa dibatalkan. Menghapus akun ini akan menghapus seluruh data
                permanen dari database kami termasuk nilai, absensi, dan tagihan.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Batal</AlertDialogCancel>
              <AlertDialogAction
                class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
              >
                Ya, Hapus Data
              </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>

        <AlertDialog>
          <AlertDialogTrigger as-child>
            <Button variant="outline">Simpan Perubahan Publik</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Publikasikan Perubahan?</AlertDialogTitle>
              <AlertDialogDescription>
                Pengumuman ini akan disebarkan ke lebih dari 500 pengguna aktif melalui email dan
                notifikasi push.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Periksa Kembali</AlertDialogCancel>
              <AlertDialogAction>Lanjutkan Publikasi</AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      </div>
    </section>

    <!-- 4. ASPECT RATIO -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">4. Aspect Ratio</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Mengunci dimensi elemen (biasanya gambar/video) sesuai dengan rasio tertentu secara
          responsif.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="p-4 border rounded-xl bg-card">
          <p class="mb-4 text-sm font-semibold">Rasio 16:9 (Video/Banner)</p>
          <AspectRatio :ratio="16 / 9" class="bg-muted rounded-md overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?w=800&dpr=2&q=80"
              alt="Photo by Drew Beamer"
              class="rounded-md object-cover w-full h-full"
            />
          </AspectRatio>
        </div>

        <div class="p-4 border rounded-xl bg-card">
          <p class="mb-4 text-sm font-semibold">Rasio 1:1 (Square Profile)</p>
          <div class="w-[200px] mx-auto">
            <AspectRatio :ratio="1 / 1" class="bg-muted rounded-md overflow-hidden">
              <img
                src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?w=400&dpr=2&q=80"
                alt="Photo by Drew Beamer"
                class="rounded-md object-cover w-full h-full"
              />
            </AspectRatio>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. AVATAR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">5. Avatar</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen profil pengguna yang otomatis me-render inisial jika foto pengguna gagal dimuat.
        </p>
      </div>

      <div class="flex flex-wrap gap-8 items-center p-6 border rounded-xl bg-card">
        <div class="flex flex-col items-center gap-2">
          <Avatar class="w-12 h-12">
            <AvatarImage src="https://i.pravatar.cc/150?u=44" alt="@shadcn" />
            <AvatarFallback>CN</AvatarFallback>
          </Avatar>
          <span class="text-xs text-muted-foreground">Normal (Image)</span>
        </div>

        <div class="flex flex-col items-center gap-2">
          <Avatar class="w-16 h-16">
            <AvatarImage src="https://github.com/nonexistent.png" alt="@broken" />
            <AvatarFallback class="bg-primary text-primary-foreground">BS</AvatarFallback>
          </Avatar>
          <span class="text-xs text-muted-foreground">Fallback (Inisial)</span>
        </div>

        <div class="flex flex-col items-center gap-2">
          <Avatar class="w-10 h-10 rounded-sm">
            <AvatarImage src="https://i.pravatar.cc/150?u=12" alt="@square" />
            <AvatarFallback>SQ</AvatarFallback>
          </Avatar>
          <span class="text-xs text-muted-foreground">Square Radius</span>
        </div>

        <div class="flex flex-col items-center gap-2">
          <Avatar class="w-24 h-24 border-4 border-primary">
            <AvatarImage src="https://i.pravatar.cc/150?u=77" alt="@large" />
            <AvatarFallback><User class="w-10 h-10" /></AvatarFallback>
          </Avatar>
          <span class="text-xs text-muted-foreground">X-Large & Border</span>
        </div>
      </div>
    </section>

    <!-- 6. BADGE -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">6. Badge</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Label kecil berbentuk pil untuk menampilkan status, kategori, atau penanda visual (Aktif,
          Lulus, dsb).
        </p>
      </div>

      <div class="flex flex-wrap gap-4 p-6 border rounded-xl bg-card">
        <Badge variant="default">Default / Baru</Badge>
        <Badge variant="secondary">Secondary / Draft</Badge>
        <Badge variant="outline">Outline</Badge>
        <Badge variant="destructive">Destructive / Ditolak</Badge>

        <!-- Custom Colors -->
        <Badge class="bg-emerald-500 hover:bg-emerald-600 text-white">Aktif / Success</Badge>
        <Badge class="bg-amber-500 hover:bg-amber-600 text-white">Pending / Warning</Badge>
        <Badge class="bg-blue-500 hover:bg-blue-600 text-white">Info / Process</Badge>
      </div>
    </section>

    <!-- 7. BREADCRUMB -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">7. Breadcrumb</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Navigasi hirarkis yang menunjukkan lokasi pengguna saat ini dalam struktur halaman.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card">
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink href="/">Beranda</BreadcrumbLink>
            </BreadcrumbItem>
            <BreadcrumbSeparator />
            <BreadcrumbItem>
              <BreadcrumbLink href="/components">UI Components</BreadcrumbLink>
            </BreadcrumbItem>
            <BreadcrumbSeparator />
            <BreadcrumbItem>
              <BreadcrumbPage>Komponen Navigasi</BreadcrumbPage>
            </BreadcrumbItem>
          </BreadcrumbList>
        </Breadcrumb>
      </div>
    </section>

    <!-- 8. BUTTON -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">8. Button</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Elemen tombol yang dapat diklik. Dilengkapi dengan berbagai ukuran dan varian gaya yang
          sudah tersinkronisasi dengan sistem warna.
        </p>
      </div>

      <div class="flex flex-wrap gap-4 items-center p-6 border rounded-xl bg-card">
        <Button variant="default">Primary</Button>
        <Button variant="secondary">Secondary</Button>
        <Button variant="outline">Outline</Button>
        <Button variant="ghost">Ghost</Button>
        <Button variant="destructive">Destructive</Button>
        <Button variant="link">Link Style</Button>
        <Button disabled>Disabled</Button>

        <Button>
          <User class="w-4 h-4 mr-2" />
          With Icon
        </Button>
        <Button size="icon" variant="outline">
          <Settings class="w-4 h-4" />
        </Button>
      </div>
    </section>

    <!-- 9. BUTTON GROUP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">9. Button Group</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Sekumpulan tombol yang digabungkan secara visual untuk opsi aksi berkelompok (misal:
          toggle view / pemfilteran list).
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex flex-col gap-4">
        <div class="flex">
          <ButtonGroup>
            <Button variant="outline">Harian</Button>
            <Button variant="outline">Mingguan</Button>
            <Button variant="outline">Bulanan</Button>
          </ButtonGroup>
        </div>
      </div>
    </section>

    <!-- 10. CALENDAR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">10. Calendar</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen pemilihan tanggal dengan sistem grid yang rapi.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card inline-block">
        <Calendar locale="id-ID" class="border rounded-md shadow" />
      </div>
    </section>
  </div>
</template>
