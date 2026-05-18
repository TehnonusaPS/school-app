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

// 11. Card
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
  CardAction
} from '@/components/ui/card'

// 12. Carousel
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious
} from '@/components/ui/carousel'

// 13. Checkbox
import { Checkbox } from '@/components/ui/checkbox'

// 14. Collapsible
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'

// 15. Context Menu
import {
  ContextMenu,
  ContextMenuCheckboxItem,
  ContextMenuContent,
  ContextMenuGroup,
  ContextMenuItem,
  ContextMenuLabel,
  ContextMenuRadioGroup,
  ContextMenuRadioItem,
  ContextMenuSeparator,
  ContextMenuShortcut,
  ContextMenuSub,
  ContextMenuSubContent,
  ContextMenuSubTrigger,
  ContextMenuTrigger
} from '@/components/ui/context-menu'

// 16. Chart
import { ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart'
import { VisXYContainer, VisLine, VisAxis } from '@unovis/vue'

// 17. Combobox
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
  ComboboxSeparator,
  ComboboxTrigger,
  ComboboxViewport
} from '@/components/ui/combobox'

// 18. Command
import {
  Command,
  CommandDialog,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
  CommandShortcut
} from '@/components/ui/command'

import { ref, onMounted } from 'vue'

const isCollapsibleOpen = ref(false)
const contextMenuRadioValue = ref('pedro')
const commandOpen = ref(false)
const contextMenuCheckboxValue = ref(true)

const comboboxValue = ref('')
const comboboxFrameworks = [
  { value: 'next.js', label: 'Next.js' },
  { value: 'sveltekit', label: 'SvelteKit' },
  { value: 'nuxt.js', label: 'Nuxt.js' },
  { value: 'remix', label: 'Remix' },
  { value: 'astro', label: 'Astro' }
]

const chartData = [
  { name: 'Jan', total: Math.floor(Math.random() * 5000) + 1000 },
  { name: 'Feb', total: Math.floor(Math.random() * 5000) + 1000 },
  { name: 'Mar', total: Math.floor(Math.random() * 5000) + 1000 },
  { name: 'Apr', total: Math.floor(Math.random() * 5000) + 1000 },
  { name: 'May', total: Math.floor(Math.random() * 5000) + 1000 },
  { name: 'Jun', total: Math.floor(Math.random() * 5000) + 1000 }
]

const chartConfig = {
  total: {
    label: 'Total',
    color: 'hsl(var(--primary))'
  }
}

// 19. Dialog
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from '@/components/ui/dialog'

// 20. Drawer
import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger
} from '@/components/ui/drawer'

// 21. Dropdown Menu
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuPortal,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'

// 22. Empty
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle
} from '@/components/ui/empty'

// 23. Field
import {
  Field,
  FieldContent,
  FieldDescription,
  FieldError,
  FieldGroup,
  FieldLabel,
  FieldLegend,
  FieldSeparator,
  FieldSet,
  FieldTitle
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { FolderOpen } from 'lucide-vue-next'

const drawerOpen = ref(false)
const dialogOpen = ref(false)
const showCommand = ref(false)

onMounted(() => {
  // Paksa scroll ke atas setiap kali komponen dimuat (mengabaikan browser scroll restoration)
  window.scrollTo({ top: 0, left: 0, behavior: 'instant' })

  // Tunda rendering Command untuk menghindari auto-focus mencuri scroll
  setTimeout(() => {
    showCommand.value = true
  }, 100)
})

// 24. Form
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage
} from '@/components/ui/form'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

const formSchema = toTypedSchema(
  z.object({
    username: z.string().min(2).max(50)
  })
)

function onSubmitForm(values) {
  console.log('Form submitted:', values)
}

// 25. Hover Card
import { HoverCard, HoverCardContent, HoverCardTrigger } from '@/components/ui/hover-card'

// 26. Input (already imported)
// import { Input } from '@/components/ui/input'

// 27. Input Group
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupInput,
  InputGroupText,
  InputGroupTextarea
} from '@/components/ui/input-group'

// 28. Input OTP
import { InputOTP, InputOTPGroup, InputOTPSeparator, InputOTPSlot } from '@/components/ui/input-otp'

// 29. Item
import {
  Item,
  ItemActions,
  ItemContent,
  ItemDescription,
  ItemFooter,
  ItemGroup,
  ItemHeader,
  ItemMedia,
  ItemSeparator,
  ItemTitle
} from '@/components/ui/item'

// 30. Kbd
import { Kbd } from '@/components/ui/kbd'

// 31. Label
import { Label } from '@/components/ui/label'

// 32. Menubar
import {
  Menubar,
  MenubarCheckboxItem,
  MenubarContent,
  MenubarItem,
  MenubarMenu,
  MenubarRadioGroup,
  MenubarRadioItem,
  MenubarSeparator,
  MenubarShortcut,
  MenubarSub,
  MenubarSubContent,
  MenubarSubTrigger,
  MenubarTrigger
} from '@/components/ui/menubar'

// 33. Native Select
import { NativeSelect } from '@/components/ui/native-select'

// 34. Navigation Menu
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle
} from '@/components/ui/navigation-menu'

// 35. Number Field
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput
} from '@/components/ui/number-field'

// 36. Pagination
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious
} from '@/components/ui/pagination'

// 37. Pin Input
import { PinInput, PinInputGroup, PinInputSlot, PinInputSeparator } from '@/components/ui/pin-input'

// 38. Popover
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'

// 39. Progress
import { Progress } from '@/components/ui/progress'

// 40. Radio Group
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'

// 41. Range Calendar
import { RangeCalendar } from '@/components/ui/range-calendar'
import { getLocalTimeZone, today } from '@internationalized/date'

// 42. Resizable
import { ResizableHandle, ResizablePanel, ResizablePanelGroup } from '@/components/ui/resizable'

// 43. Scroll Area
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area'

// 44. Select
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

// 45. Separator
import { Separator } from '@/components/ui/separator'

// 46. Sheet
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
  SheetTrigger
} from '@/components/ui/sheet'

// 47. Sidebar
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuItem,
  SidebarMenuButton,
  SidebarGroupLabel,
  SidebarGroupContent
} from '@/components/ui/sidebar'

// 48. Skeleton
import { Skeleton } from '@/components/ui/skeleton'

// 49. Slider
import { Slider } from '@/components/ui/slider'

// 50. Sonner
import { toast } from 'vue-sonner'
import { Toaster } from '@/components/ui/sonner'

// 51. Spinner
import { Spinner } from '@/components/ui/spinner'

// 52. Stepper
import {
  Stepper,
  StepperDescription,
  StepperIndicator,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger
} from '@/components/ui/stepper'

// 53. Switch
import { Switch } from '@/components/ui/switch'

// 54. Table
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'

// 55. Tabs
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

// 56. Tags Input
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText
} from '@/components/ui/tags-input'

// 57. Textarea
import { Textarea } from '@/components/ui/textarea'

// 58. Toggle
import { Toggle } from '@/components/ui/toggle'

// 59. Toggle Group
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'

// 60. Tooltip
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

const otpValue = ref('')
const pinValue = ref([])
const progressValue = ref(33)
const radioValue = ref('comfortable')
const sliderValue = ref([50])
const switchValue = ref(false)
const tagsInputValue = ref(['Apple', 'Banana'])
const textareaValue = ref('')
const toggleValue = ref(false)
const toggleGroupValue = ref('center')

const rangeCalendarValue = ref({
  start: today(getLocalTimeZone()),
  end: today(getLocalTimeZone()).add({ days: 7 })
})

const stepperSteps = [
  { step: 1, title: 'Akun', description: 'Buat detail akun Anda' },
  { step: 2, title: 'Profil', description: 'Atur profil publik' },
  { step: 3, title: 'Selesai', description: 'Konfirmasi dan simpan' }
]
const stepperValue = ref(2)
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

    <!-- 11. CARD -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">11. Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen kartu untuk menampilkan konten terstruktur dengan Header, Body, dan Footer.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-muted/30 grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Notifikasi Sistem</CardTitle>
            <CardDescription>Anda memiliki 3 pesan yang belum terbaca.</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center gap-4 border p-4 rounded-lg bg-background">
              <BellRing class="w-8 h-8 text-primary" />
              <div>
                <p class="font-medium text-sm">Pembaruan Jadwal</p>
                <p class="text-xs text-muted-foreground">Jadwal pelajaran hari ini diubah.</p>
              </div>
            </div>
          </CardContent>
          <CardFooter class="flex justify-end gap-2">
            <Button variant="outline">Abaikan</Button>
            <Button>Tandai Dibaca</Button>
          </CardFooter>
        </Card>
      </div>
    </section>

    <!-- 12. CAROUSEL -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">12. Carousel</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen geser (slider) untuk menampilkan daftar gambar atau konten yang bisa di-scroll
          secara horizontal.
        </p>
      </div>

      <div class="p-12 border rounded-xl bg-card flex justify-center overflow-hidden">
        <Carousel class="w-full max-w-xs" :opts="{ align: 'start' }">
          <CarouselContent>
            <CarouselItem v-for="index in 5" :key="index" class="md:basis-1/2 lg:basis-1/3">
              <div class="p-1">
                <Card>
                  <CardContent class="flex aspect-square items-center justify-center p-6">
                    <span class="text-4xl font-semibold">{{ index }}</span>
                  </CardContent>
                </Card>
              </div>
            </CarouselItem>
          </CarouselContent>
          <CarouselPrevious />
          <CarouselNext />
        </Carousel>
      </div>
    </section>

    <!-- 13. CHECKBOX -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">13. Checkbox</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen input centang untuk memilih satu atau beberapa opsi (multiple choices).
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex flex-col gap-4">
        <div class="flex items-center space-x-2">
          <Checkbox id="terms" />
          <label
            for="terms"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
          >
            Saya setuju dengan syarat dan ketentuan
          </label>
        </div>

        <div class="flex items-center space-x-2">
          <Checkbox id="marketing" disabled />
          <label
            for="marketing"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
          >
            Terima email promosi (Disabled)
          </label>
        </div>
      </div>
    </section>

    <!-- 14. COLLAPSIBLE -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">14. Collapsible</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen interaktif yang dapat diperluas atau disembunyikan isinya. Sangat berguna untuk
          menyembunyikan detail tambahan.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Collapsible v-model:open="isCollapsibleOpen" class="w-full max-w-sm space-y-2">
          <div class="flex items-center justify-between space-x-4 px-4">
            <h4 class="text-sm font-semibold">@SekolahHebat repositories</h4>
            <CollapsibleTrigger as-child>
              <Button variant="ghost" size="sm" class="w-9 p-0">
                <ChevronsUpDown class="h-4 w-4" />
                <span class="sr-only">Toggle</span>
              </Button>
            </CollapsibleTrigger>
          </div>
          <div class="rounded-md border px-4 py-3 font-mono text-sm shadow-sm bg-background">
            @sekolah-hebat/elearning
          </div>
          <CollapsibleContent class="space-y-2">
            <div class="rounded-md border px-4 py-3 font-mono text-sm shadow-sm bg-background">
              @sekolah-hebat/library
            </div>
            <div class="rounded-md border px-4 py-3 font-mono text-sm shadow-sm bg-background">
              @sekolah-hebat/finance
            </div>
          </CollapsibleContent>
        </Collapsible>
      </div>
    </section>

    <!-- 15. CONTEXT MENU -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">15. Context Menu</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menu khusus yang muncul saat pengguna mengklik kanan (right-click) pada area tertentu.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <ContextMenu>
          <ContextMenuTrigger
            class="flex h-[150px] w-full max-w-sm items-center justify-center rounded-md border-2 border-dashed border-primary text-sm font-semibold bg-muted/20 cursor-context-menu hover:bg-muted/40 transition-colors"
          >
            Klik Kanan di Area Ini
          </ContextMenuTrigger>
          <ContextMenuContent class="w-64">
            <ContextMenuItem inset>
              Back
              <ContextMenuShortcut>⌘[</ContextMenuShortcut>
            </ContextMenuItem>
            <ContextMenuItem inset disabled>
              Forward
              <ContextMenuShortcut>⌘]</ContextMenuShortcut>
            </ContextMenuItem>
            <ContextMenuItem inset>
              Reload
              <ContextMenuShortcut>⌘R</ContextMenuShortcut>
            </ContextMenuItem>
            <ContextMenuSub>
              <ContextMenuSubTrigger inset> More Tools </ContextMenuSubTrigger>
              <ContextMenuSubContent class="w-48">
                <ContextMenuItem>
                  Save Page As...
                  <ContextMenuShortcut>⇧⌘S</ContextMenuShortcut>
                </ContextMenuItem>
                <ContextMenuItem>Create Shortcut...</ContextMenuItem>
                <ContextMenuItem>Name Window...</ContextMenuItem>
                <ContextMenuSeparator />
                <ContextMenuItem>Developer Tools</ContextMenuItem>
              </ContextMenuSubContent>
            </ContextMenuSub>
            <ContextMenuSeparator />
            <ContextMenuCheckboxItem v-model:checked="contextMenuCheckboxValue">
              Show Bookmarks Bar
              <ContextMenuShortcut>⌘⇧B</ContextMenuShortcut>
            </ContextMenuCheckboxItem>
            <ContextMenuCheckboxItem>Show Full URLs</ContextMenuCheckboxItem>
            <ContextMenuSeparator />
            <ContextMenuRadioGroup v-model="contextMenuRadioValue">
              <ContextMenuLabel inset> People </ContextMenuLabel>
              <ContextMenuSeparator />
              <ContextMenuRadioItem value="pedro"> Pedro Duarte </ContextMenuRadioItem>
              <ContextMenuRadioItem value="colm"> Colm Tuite </ContextMenuRadioItem>
            </ContextMenuRadioGroup>
          </ContextMenuContent>
        </ContextMenu>
      </div>
    </section>
    <!-- 16. CHART -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">16. Chart</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen visualisasi data menggunakan Unovis. Di sini dicontohkan sebagai Line Chart
          sederhana.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card">
        <ChartContainer :config="chartConfig" class="h-[250px] w-full">
          <VisXYContainer :data="chartData">
            <VisLine :x="(d, i) => i" :y="d => d.total" color="var(--color-primary)" />
            <VisAxis type="x" :tickFormat="i => chartData[i]?.name" />
            <VisAxis type="y" />
            <ChartTooltip />
          </VisXYContainer>
        </ChartContainer>
      </div>
    </section>

    <!-- 17. COMBOBOX -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">17. Combobox</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen input pencarian yang dilengkapi dengan daftar pilihan *dropdown* bergaya command.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center h-[350px] items-start">
        <Combobox v-model="comboboxValue" :options="comboboxFrameworks" class="w-[200px]">
          <ComboboxAnchor class="w-[200px]">
            <ComboboxTrigger as-child>
              <Button variant="outline" role="combobox" class="w-[200px] justify-between">
                {{
                  comboboxValue
                    ? comboboxFrameworks.find(f => f.value === comboboxValue)?.label
                    : 'Select framework...'
                }}
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
              </Button>
            </ComboboxTrigger>
          </ComboboxAnchor>

          <ComboboxList>
            <div class="flex items-center px-3 pb-2 pt-3 border-b">
              <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
              <ComboboxInput
                :auto-focus="false"
                placeholder="Search framework..."
                class="flex h-9 w-full bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50 border-none shadow-none focus:ring-0"
              />
            </div>
            <ComboboxEmpty class="py-6 text-center text-sm">No framework found.</ComboboxEmpty>
            <ComboboxViewport class="p-1">
              <ComboboxGroup>
                <ComboboxItem
                  v-for="framework in comboboxFrameworks"
                  :key="framework.value"
                  :value="framework.value"
                >
                  <ComboboxItemIndicator>
                    <Check class="h-4 w-4" />
                  </ComboboxItemIndicator>
                  <span>{{ framework.label }}</span>
                </ComboboxItem>
              </ComboboxGroup>
            </ComboboxViewport>
          </ComboboxList>
        </Combobox>
      </div>
    </section>

    <!-- 18. COMMAND -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">18. Command</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen pencarian yang sangat cepat (mirip dengan Command Palette macOS).
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Button variant="outline" @click="commandOpen = true">
          Buka Command Palette
          <kbd
            class="pointer-events-none inline-flex h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium text-muted-foreground opacity-100 ml-2"
          >
            <span class="text-xs">⌘</span>K
          </kbd>
        </Button>
        <CommandDialog v-model:open="commandOpen">
          <CommandInput placeholder="Type a command or search..." />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <CommandGroup heading="Suggestions">
              <CommandItem value="search-emoji">
                <Search class="mr-2 h-4 w-4" />
                <span>Search Emoji</span>
              </CommandItem>
              <CommandItem value="settings">
                <Settings class="mr-2 h-4 w-4" />
                <span>Settings</span>
              </CommandItem>
            </CommandGroup>
            <CommandSeparator />
            <CommandGroup heading="Account">
              <CommandItem value="profile">
                <User class="mr-2 h-4 w-4" />
                <span>Profile</span>
                <CommandShortcut>⌘P</CommandShortcut>
              </CommandItem>
              <CommandItem value="billing">
                <AlertCircle class="mr-2 h-4 w-4" />
                <span>Billing</span>
                <CommandShortcut>⌘B</CommandShortcut>
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </CommandDialog>
      </div>
    </section>
    <!-- 19. DIALOG -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">19. Dialog</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Jendela modal popup untuk menampilkan pesan, konfirmasi, atau form input tanpa berpindah
          halaman.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Dialog v-model:open="dialogOpen">
          <DialogTrigger as-child>
            <Button variant="outline">Edit Profile</Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>Edit profile</DialogTitle>
              <DialogDescription>
                Make changes to your profile here. Click save when you're done.
              </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
              <div class="grid grid-cols-4 items-center gap-4">
                <label for="name" class="text-right text-sm">Name</label>
                <Input id="name" value="Pedro Duarte" class="col-span-3" />
              </div>
              <div class="grid grid-cols-4 items-center gap-4">
                <label for="username" class="text-right text-sm">Username</label>
                <Input id="username" value="@peduarte" class="col-span-3" />
              </div>
            </div>
            <DialogFooter>
              <Button type="button" @click="dialogOpen = false">Save changes</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </section>

    <!-- 20. DRAWER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">20. Drawer</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Panel yang ditarik dari tepi layar (biasanya dari bawah pada perangkat seluler) untuk
          menampilkan konten tambahan.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Drawer v-model:open="drawerOpen">
          <DrawerTrigger as-child>
            <Button variant="outline">Open Drawer</Button>
          </DrawerTrigger>
          <DrawerContent>
            <div class="mx-auto w-full max-w-sm">
              <DrawerHeader>
                <DrawerTitle>Move Goal</DrawerTitle>
                <DrawerDescription>Set your daily activity goal.</DrawerDescription>
              </DrawerHeader>
              <div class="p-4 pb-0 flex items-center justify-center space-x-2">
                <Button variant="outline" size="icon" class="h-8 w-8 shrink-0 rounded-full">
                  <span class="sr-only">Decrease</span>
                  -
                </Button>
                <div class="flex-1 text-center">
                  <div class="text-7xl font-bold tracking-tighter">350</div>
                  <div class="text-[0.70rem] uppercase text-muted-foreground">Calories/day</div>
                </div>
                <Button variant="outline" size="icon" class="h-8 w-8 shrink-0 rounded-full">
                  <span class="sr-only">Increase</span>
                  +
                </Button>
              </div>
              <DrawerFooter>
                <Button @click="drawerOpen = false">Submit</Button>
                <DrawerClose as-child>
                  <Button variant="outline">Cancel</Button>
                </DrawerClose>
              </DrawerFooter>
            </div>
          </DrawerContent>
        </Drawer>
      </div>
    </section>

    <!-- 21. DROPDOWN MENU -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">21. Dropdown Menu</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menu opsi berlapis yang terbuka ketika pengguna mengklik suatu elemen *trigger* seperti
          tombol atau avatar.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline">Open Menu</Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent class="w-56">
            <DropdownMenuLabel>My Account</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
              <DropdownMenuItem>
                <User class="mr-2 h-4 w-4" />
                <span>Profile</span>
                <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
              </DropdownMenuItem>
              <DropdownMenuItem>
                <Settings class="mr-2 h-4 w-4" />
                <span>Settings</span>
                <DropdownMenuShortcut>⌘S</DropdownMenuShortcut>
              </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
              <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                  <User class="mr-2 h-4 w-4" />
                  <span>Invite users</span>
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                  <DropdownMenuSubContent>
                    <DropdownMenuItem>
                      <User class="mr-2 h-4 w-4" />
                      <span>Email</span>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                      <User class="mr-2 h-4 w-4" />
                      <span>Message</span>
                    </DropdownMenuItem>
                  </DropdownMenuSubContent>
                </DropdownMenuPortal>
              </DropdownMenuSub>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem disabled>
              <User class="mr-2 h-4 w-4" />
              <span>Log out</span>
              <DropdownMenuShortcut>⇧⌘Q</DropdownMenuShortcut>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </section>

    <!-- 22. EMPTY -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">22. Empty State</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Tampilan khusus ketika tidak ada data untuk ditampilkan (kosong), membantu menjaga
          antarmuka tetap ramah.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card">
        <Empty class="border-dashed py-12 rounded-lg">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <FolderOpen class="w-12 h-12 text-muted-foreground/50" />
            </EmptyMedia>
            <EmptyTitle>Tidak Ada Berkas</EmptyTitle>
            <EmptyDescription>
              Silakan unggah dokumen untuk mulai melihat daftarnya di sini.
            </EmptyDescription>
          </EmptyHeader>
          <EmptyContent>
            <Button variant="default" size="sm"> Upload Berkas </Button>
          </EmptyContent>
        </Empty>
      </div>
    </section>

    <!-- 23. FIELD -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">23. Field / Form Control</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Pembungkus elemen *form* untuk memberikan label, deskripsi, dan umpan balik *error* yang
          konsisten.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <FieldSet class="max-w-md w-full border rounded-lg p-6 bg-background space-y-4">
          <FieldLegend>Detail Pengguna</FieldLegend>
          <FieldDescription
            >Masukkan informasi pengguna di bawah ini secara lengkap.</FieldDescription
          >
          <FieldSeparator />

          <FieldGroup>
            <Field>
              <FieldLabel for="fullname">Nama Lengkap</FieldLabel>
              <FieldContent>
                <Input id="fullname" placeholder="Cth: John Doe" />
              </FieldContent>
            </Field>

            <Field data-invalid="true">
              <FieldLabel for="email"
                >Alamat Email <span class="text-destructive">*</span></FieldLabel
              >
              <FieldContent>
                <Input
                  id="email"
                  type="email"
                  placeholder="Cth: john@example.com"
                  class="border-destructive"
                />
              </FieldContent>
              <FieldError>Email tidak valid atau sudah terdaftar.</FieldError>
            </Field>
          </FieldGroup>
        </FieldSet>
      </div>
    </section>
    <!-- 24. FORM -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">24. Form</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Pembungkus form yang terintegrasi dengan vee-validate dan Zod untuk validasi skema.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Form
          v-slot="{ handleSubmit }"
          as=""
          :validation-schema="formSchema"
          @submit="onSubmitForm"
        >
          <form
            class="w-full max-w-sm space-y-6 border p-6 rounded-lg bg-background"
            @submit="handleSubmit($event, onSubmitForm)"
          >
            <FormField v-slot="{ componentField }" name="username">
              <FormItem>
                <FormLabel>Username</FormLabel>
                <FormControl>
                  <Input type="text" placeholder="shadcn" v-bind="componentField" />
                </FormControl>
                <FormDescription> This is your public display name. </FormDescription>
                <FormMessage />
              </FormItem>
            </FormField>
            <Button type="submit"> Submit </Button>
          </form>
        </Form>
      </div>
    </section>

    <!-- 25. HOVER CARD -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">25. Hover Card</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menampilkan preview atau informasi tambahan ketika pengguna menyorot (hover) sebuah tautan
          atau elemen.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center h-[200px] items-center">
        <HoverCard>
          <HoverCardTrigger as-child>
            <Button variant="link">@vuejs</Button>
          </HoverCardTrigger>
          <HoverCardContent class="w-80">
            <div class="flex justify-between space-x-4">
              <Avatar>
                <AvatarImage src="https://github.com/vuejs.png" />
                <AvatarFallback>VC</AvatarFallback>
              </Avatar>
              <div class="space-y-1">
                <h4 class="text-sm font-semibold">@vuejs</h4>
                <p class="text-sm">The Progressive JavaScript Framework.</p>
                <div class="flex items-center pt-2">
                  <Calendar class="mr-2 h-4 w-4 opacity-70" />
                  <span class="text-xs text-muted-foreground"> Joined January 2014 </span>
                </div>
              </div>
            </div>
          </HoverCardContent>
        </HoverCard>
      </div>
    </section>

    <!-- 26. INPUT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">26. Input</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kolom input teks dasar dengan gaya yang konsisten, mendukung *disabled state* dan *file
          upload*.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card grid gap-4 max-w-sm">
        <Input type="email" placeholder="Email" />
        <Input disabled type="email" placeholder="Email (Disabled)" />
        <div class="grid w-full max-w-sm items-center gap-1.5">
          <label
            for="picture"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >Picture</label
          >
          <Input id="picture" type="file" />
        </div>
      </div>
    </section>

    <!-- 27. INPUT GROUP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">27. Input Group</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Grup input yang memungkinkan penambahan ikon, teks awalan/akhiran, atau tombol langsung di
          sebelah input.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex flex-col gap-4 max-w-sm">
        <InputGroup>
          <InputGroupText>
            <User class="w-4 h-4" />
          </InputGroupText>
          <InputGroupInput placeholder="Username" />
        </InputGroup>

        <InputGroup>
          <InputGroupText>https://</InputGroupText>
          <InputGroupInput placeholder="example.com" />
        </InputGroup>

        <InputGroup>
          <InputGroupInput placeholder="Search..." />
          <InputGroupButton variant="secondary">
            <Search class="w-4 h-4" />
          </InputGroupButton>
        </InputGroup>
      </div>
    </section>

    <!-- 28. INPUT OTP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">28. Input OTP</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen khusus untuk memasukkan kode OTP (One Time Password) dengan pemisah antar blok
          angka.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <InputOTP v-model="otpValue" :max-length="6">
          <InputOTPGroup>
            <InputOTPSlot :index="0" />
            <InputOTPSlot :index="1" />
            <InputOTPSlot :index="2" />
          </InputOTPGroup>
          <InputOTPSeparator />
          <InputOTPGroup>
            <InputOTPSlot :index="3" />
            <InputOTPSlot :index="4" />
            <InputOTPSlot :index="5" />
          </InputOTPGroup>
        </InputOTP>
      </div>
    </section>

    <!-- 29. ITEM -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">29. Item</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen daftar item yang kaya fitur, mendukung media, judul, deskripsi, dan aksi tambahan
          di dalamnya.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card">
        <ItemGroup class="max-w-md mx-auto space-y-2">
          <Item>
            <ItemMedia variant="image">
              <img src="https://ui.shadcn.com/avatars/01.png" alt="Avatar" />
            </ItemMedia>
            <ItemContent>
              <ItemHeader>
                <ItemTitle>Olivia Martin</ItemTitle>
              </ItemHeader>
              <ItemDescription>olivia.martin@email.com</ItemDescription>
            </ItemContent>
            <ItemActions>
              <Button variant="outline" size="sm">Invite</Button>
            </ItemActions>
          </Item>
          <Item>
            <ItemMedia variant="image">
              <img src="https://ui.shadcn.com/avatars/02.png" alt="Avatar" />
            </ItemMedia>
            <ItemContent>
              <ItemHeader>
                <ItemTitle>Jackson Lee</ItemTitle>
              </ItemHeader>
              <ItemDescription>jackson.lee@email.com</ItemDescription>
            </ItemContent>
            <ItemActions>
              <Button variant="outline" size="sm">Invite</Button>
            </ItemActions>
          </Item>
        </ItemGroup>
      </div>
    </section>

    <!-- 30. KBD -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">30. Kbd</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menampilkan pintasan keyboard (*keyboard shortcut*) dengan gaya khusus.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex items-center justify-center gap-4">
        <span>Tekan</span>
        <Kbd>⌘</Kbd>
        <span>+</span>
        <Kbd>K</Kbd>
        <span>untuk mencari</span>
      </div>
    </section>

    <!-- 31. LABEL -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">31. Label</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen label yang terhubung dengan elemen form, mendukung *state disabled*.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex flex-col gap-4 max-w-sm">
        <div class="flex items-center space-x-2">
          <Checkbox id="terms-label" />
          <Label for="terms-label">Accept terms and conditions</Label>
        </div>
      </div>
    </section>

    <!-- 32. MENUBAR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">32. Menubar</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Menu bar ala desktop yang membentang secara horizontal dengan sub-menu.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center h-[200px]">
        <Menubar>
          <MenubarMenu>
            <MenubarTrigger>File</MenubarTrigger>
            <MenubarContent>
              <MenubarItem> New Tab <MenubarShortcut>⌘T</MenubarShortcut> </MenubarItem>
              <MenubarItem> New Window <MenubarShortcut>⌘N</MenubarShortcut> </MenubarItem>
              <MenubarItem disabled>New Incognito Window</MenubarItem>
              <MenubarSeparator />
              <MenubarSub>
                <MenubarSubTrigger>Share</MenubarSubTrigger>
                <MenubarSubContent>
                  <MenubarItem>Email link</MenubarItem>
                  <MenubarItem>Messages</MenubarItem>
                  <MenubarItem>Notes</MenubarItem>
                </MenubarSubContent>
              </MenubarSub>
              <MenubarSeparator />
              <MenubarItem> Print... <MenubarShortcut>⌘P</MenubarShortcut> </MenubarItem>
            </MenubarContent>
          </MenubarMenu>
          <MenubarMenu>
            <MenubarTrigger>Edit</MenubarTrigger>
            <MenubarContent>
              <MenubarItem>Undo <MenubarShortcut>⌘Z</MenubarShortcut></MenubarItem>
              <MenubarItem>Redo <MenubarShortcut>⇧⌘Z</MenubarShortcut></MenubarItem>
            </MenubarContent>
          </MenubarMenu>
        </Menubar>
      </div>
    </section>

    <!-- 33. NATIVE SELECT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">33. Native Select</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Pilihan bawaan dari browser (*native*) dengan sentuhan gaya desain agar terlihat lebih
          rapi.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <NativeSelect class="w-[200px]">
          <option value="" disabled selected>Pilih kota...</option>
          <option value="jakarta">Jakarta</option>
          <option value="bandung">Bandung</option>
          <option value="surabaya">Surabaya</option>
        </NativeSelect>
      </div>
    </section>

    <!-- 34. NAVIGATION MENU -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">34. Navigation Menu</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Sistem navigasi kompleks dengan kemampuan menampilkan *mega menu* bergaya animasi.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center h-[250px]">
        <NavigationMenu>
          <NavigationMenuList>
            <NavigationMenuItem>
              <NavigationMenuTrigger>Getting started</NavigationMenuTrigger>
              <NavigationMenuContent>
                <ul class="grid gap-3 p-6 md:w-[400px] lg:w-[500px] lg:grid-cols-[.75fr_1fr]">
                  <li class="row-span-3">
                    <NavigationMenuLink as-child>
                      <a
                        class="flex h-full w-full select-none flex-col justify-end rounded-md bg-gradient-to-b from-muted/50 to-muted p-6 no-underline outline-hidden focus:shadow-md"
                        href="/"
                      >
                        <div class="mb-2 mt-4 text-lg font-medium">School App</div>
                        <p class="text-sm leading-tight text-muted-foreground">
                          Platform manajemen pendidikan masa depan.
                        </p>
                      </a>
                    </NavigationMenuLink>
                  </li>
                  <li>
                    <NavigationMenuLink as-child>
                      <a
                        href="#"
                        class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-hidden transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                      >
                        <div class="text-sm font-medium leading-none">Introduction</div>
                        <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                          Kenali berbagai fitur andalan.
                        </p>
                      </a>
                    </NavigationMenuLink>
                  </li>
                </ul>
              </NavigationMenuContent>
            </NavigationMenuItem>
            <NavigationMenuItem>
              <NavigationMenuLink href="/docs" :class="navigationMenuTriggerStyle()">
                Documentation
              </NavigationMenuLink>
            </NavigationMenuItem>
          </NavigationMenuList>
        </NavigationMenu>
      </div>
    </section>

    <!-- 35. NUMBER FIELD -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">35. Number Field</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Input angka interaktif dengan tombol penambah (+) dan pengurang (-).
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <NumberField id="age" :default-value="18" :min="0" :max="100" class="w-40">
          <Label for="age" class="sr-only">Age</Label>
          <NumberFieldContent>
            <NumberFieldDecrement />
            <NumberFieldInput />
            <NumberFieldIncrement />
          </NumberFieldContent>
        </NumberField>
      </div>
    </section>

    <!-- 36. PAGINATION -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">36. Pagination</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kontrol navigasi penomoran halaman untuk dataset yang panjang.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Pagination :total="100" :sibling-count="1" show-edges :default-page="2">
          <PaginationContent>
            <PaginationFirst />
            <PaginationPrevious />
            <PaginationItem>
              <PaginationLink :page="1">1</PaginationLink>
            </PaginationItem>
            <PaginationItem>
              <PaginationLink :page="2" is-active>2</PaginationLink>
            </PaginationItem>
            <PaginationItem>
              <PaginationLink :page="3">3</PaginationLink>
            </PaginationItem>
            <PaginationEllipsis />
            <PaginationNext />
            <PaginationLast />
          </PaginationContent>
        </Pagination>
      </div>
    </section>

    <!-- 37. PIN INPUT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">37. Pin Input</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Input yang dioptimalkan untuk memasukkan kode PIN atau kode keamanan rahasia.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <PinInput id="pin-input" v-model="pinValue" placeholder="○">
          <PinInputGroup>
            <PinInputSlot v-for="(id, index) in 5" :key="id" :index="index" />
          </PinInputGroup>
        </PinInput>
      </div>
    </section>

    <!-- 38. POPOVER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">38. Popover</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Balon informasi (*pop-up*) interaktif yang muncul saat sebuah elemen diklik, mendukung
          form di dalamnya.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center h-[200px] items-start pt-10">
        <Popover>
          <PopoverTrigger as-child>
            <Button variant="outline">Buka Popover</Button>
          </PopoverTrigger>
          <PopoverContent class="w-80">
            <div class="grid gap-4">
              <div class="space-y-2">
                <h4 class="font-medium leading-none">Dimensi</h4>
                <p class="text-sm text-muted-foreground">Atur dimensi objek.</p>
              </div>
              <div class="grid gap-2">
                <div class="grid grid-cols-3 items-center gap-4">
                  <Label for="width">Lebar</Label>
                  <Input id="width" default-value="100%" class="col-span-2 h-8" />
                </div>
                <div class="grid grid-cols-3 items-center gap-4">
                  <Label for="maxWidth">Maks. Lebar</Label>
                  <Input id="maxWidth" default-value="300px" class="col-span-2 h-8" />
                </div>
              </div>
            </div>
          </PopoverContent>
        </Popover>
      </div>
    </section>

    <!-- 39. PROGRESS -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">39. Progress</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Bilah kemajuan (*progress bar*) untuk mengindikasikan status dari proses yang sedang
          berjalan.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex flex-col items-center gap-4">
        <Progress v-model="progressValue" class="w-[60%]" />
        <Button
          variant="outline"
          size="sm"
          @click="progressValue = Math.min(100, progressValue + 10)"
        >
          Tambah 10%
        </Button>
      </div>
    </section>

    <!-- 40. RADIO GROUP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">40. Radio Group</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Grup pilihan tunggal (radio button) yang dirancang secara elegan.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <RadioGroup v-model="radioValue" class="space-y-2">
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r1" value="default" />
            <Label for="r1">Default (Bawaan)</Label>
          </div>
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r2" value="comfortable" />
            <Label for="r2">Comfortable (Nyaman)</Label>
          </div>
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r3" value="compact" />
            <Label for="r3">Compact (Padat)</Label>
          </div>
        </RadioGroup>
      </div>
    </section>

    <!-- 41. RANGE CALENDAR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">41. Range Calendar</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen kalender yang memungkinkan pengguna untuk memilih rentang tanggal (*start* dan
          *end*).
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <RangeCalendar v-model="rangeCalendarValue" class="rounded-md border shadow-sm" />
      </div>
    </section>

    <!-- 42. RESIZABLE -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">42. Resizable</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Panel antarmuka yang ukurannya dapat diubah-ubah dengan cara digeser batasnya (*drag and
          drop*).
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card">
        <ResizablePanelGroup
          direction="horizontal"
          class="min-h-[200px] max-w-md rounded-lg border mx-auto"
        >
          <ResizablePanel :default-size="50">
            <div class="flex h-full items-center justify-center p-6">
              <span class="font-semibold">Panel Kiri</span>
            </div>
          </ResizablePanel>
          <ResizableHandle />
          <ResizablePanel :default-size="50">
            <div class="flex h-full items-center justify-center p-6">
              <span class="font-semibold">Panel Kanan</span>
            </div>
          </ResizablePanel>
        </ResizablePanelGroup>
      </div>
    </section>

    <!-- 43. SCROLL AREA -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">43. Scroll Area</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Area dengan *scrollbar* kustom yang gayanya konsisten dan mulus di semua browser.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <ScrollArea class="h-[200px] w-[350px] rounded-md border p-4">
          <div class="pr-4">
            <h4 class="mb-4 text-sm font-medium leading-none">Tags</h4>
            <div v-for="tag in 50" :key="tag" class="text-sm">
              Tag {{ tag }}
              <Separator class="my-2" />
            </div>
          </div>
        </ScrollArea>
      </div>
    </section>

    <!-- 44. SELECT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">44. Select</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Pilihan (*dropdown*) kustom kaya fitur yang menggantikan elemen &lt;select&gt; bawaan
          browser.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Select>
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Pilih buah" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectLabel>Fruits</SelectLabel>
              <SelectItem value="apple">Apple</SelectItem>
              <SelectItem value="banana">Banana</SelectItem>
              <SelectItem value="blueberry">Blueberry</SelectItem>
              <SelectItem value="grapes">Grapes</SelectItem>
              <SelectItem value="pineapple">Pineapple</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
    </section>

    <!-- 45. SEPARATOR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">45. Separator</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Garis pemisah secara visual atau semantik antar bagian konten.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card">
        <div class="max-w-md mx-auto">
          <div class="space-y-1">
            <h4 class="text-sm font-medium leading-none">Radix Primitives</h4>
            <p class="text-sm text-muted-foreground">An open-source UI component library.</p>
          </div>
          <Separator class="my-4" />
          <div class="flex h-5 items-center space-x-4 text-sm">
            <div>Blog</div>
            <Separator orientation="vertical" />
            <div>Docs</div>
            <Separator orientation="vertical" />
            <div>Source</div>
          </div>
        </div>
      </div>
    </section>

    <!-- 46. SHEET -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">46. Sheet</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Panel yang meluncur (*slide*) dari tepi layar, sering digunakan untuk menu *mobile* atau
          form detail.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Sheet>
          <SheetTrigger as-child>
            <Button variant="outline">Buka Sheet Kanan</Button>
          </SheetTrigger>
          <SheetContent>
            <SheetHeader>
              <SheetTitle>Edit Profil</SheetTitle>
              <SheetDescription>
                Lakukan perubahan pada profil Anda di sini. Klik simpan saat Anda selesai.
              </SheetDescription>
            </SheetHeader>
            <div class="grid gap-4 py-4">
              <div class="grid grid-cols-4 items-center gap-4">
                <Label for="name" class="text-right">Nama</Label>
                <Input id="name" value="Pedro Duarte" class="col-span-3" />
              </div>
              <div class="grid grid-cols-4 items-center gap-4">
                <Label for="username" class="text-right">Username</Label>
                <Input id="username" value="@peduarte" class="col-span-3" />
              </div>
            </div>
            <SheetFooter>
              <SheetClose as-child>
                <Button type="submit">Simpan Perubahan</Button>
              </SheetClose>
            </SheetFooter>
          </SheetContent>
        </Sheet>
      </div>
    </section>

    <!-- 47. SIDEBAR -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">47. Sidebar</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Struktur navigasi samping bawaan aplikasi yang memiliki dukungan *state* buka/tutup.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex h-[400px]">
        <div class="flex h-full w-full overflow-hidden border rounded-md relative">
          <Sidebar collapsible="none" class="border-r w-64 bg-sidebar">
            <SidebarHeader class="sidebar-brand-header">
              <SidebarMenu>
                <SidebarMenuItem>
                  <SidebarMenuButton
                    size="lg"
                    as-child
                    class="sidebar-brand-btn hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                  >
                    <a href="#">
                      <div class="sidebar-brand-icon">
                        <School class="size-5" />
                      </div>
                      <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate font-extrabold text-sidebar-foreground tracking-tight"
                          >CerdasBangsa</span
                        >
                        <span
                          class="truncate text-[10px] capitalize text-sidebar-foreground/70 font-medium"
                        >
                          Admin
                        </span>
                      </div>
                    </a>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
              <SidebarGroup>
                <SidebarGroupLabel>Menu Utama</SidebarGroupLabel>
                <SidebarMenu class="gap-1.5 px-2">
                  <SidebarMenuItem>
                    <SidebarMenuButton
                      class="!h-11 px-3 text-sm transition-colors duration-200 glossy-active font-semibold"
                      tooltip="Dashboard"
                    >
                      <LayoutDashboard class="!size-5 shrink-0" />
                      <span>Dashboard</span>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                  <SidebarMenuItem>
                    <SidebarMenuButton
                      class="!h-11 px-3 text-sm transition-colors duration-200 !bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium"
                      tooltip="Manajemen Data"
                    >
                      <Database class="!size-5 shrink-0" />
                      <span>Manajemen Data</span>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                  <SidebarMenuItem>
                    <SidebarMenuButton
                      class="!h-11 px-3 text-sm transition-colors duration-200 !bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium"
                      tooltip="Akademik"
                    >
                      <GraduationCap class="!size-5 shrink-0" />
                      <span>Akademik</span>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                </SidebarMenu>
              </SidebarGroup>
            </SidebarContent>
            <SidebarFooter>
              <SidebarMenu>
                <SidebarMenuItem>
                  <SidebarMenuButton
                    size="lg"
                    class="hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                  >
                    <Avatar class="h-8 w-8 rounded-lg">
                      <AvatarFallback class="rounded-lg"> AD </AvatarFallback>
                    </Avatar>
                    <div class="grid flex-1 text-left text-sm leading-tight">
                      <span class="truncate font-medium">Admin Sekolah</span>
                      <span class="truncate text-xs text-muted-foreground uppercase font-semibold"
                        >admin</span
                      >
                    </div>
                    <ChevronsUpDown class="ml-auto size-4" />
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </SidebarMenu>
            </SidebarFooter>
          </Sidebar>
          <main class="flex-1 p-4 bg-background">
            <div class="text-sm">
              Ini adalah contoh konten utama yang berdampingan dengan sidebar di sebelah kiri.
            </div>
          </main>
        </div>
      </div>
    </section>

    <!-- 48. SKELETON -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">48. Skeleton</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Indikator visual berbentuk kerangka untuk menampilkan status *loading* dari sebuah
          komponen.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex items-center space-x-4">
        <Skeleton class="h-12 w-12 rounded-full" />
        <div class="space-y-2">
          <Skeleton class="h-4 w-[250px]" />
          <Skeleton class="h-4 w-[200px]" />
        </div>
      </div>
    </section>

    <!-- 49. SLIDER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">49. Slider</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Input interaktif (*slider*) tempat pengguna memilih rentang atau nilai tunggal di
          sepanjang batang horizontal/vertikal.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex flex-col gap-6 items-center">
        <Slider v-model="sliderValue" :max="100" :step="1" class="w-[60%]" />
        <div class="text-sm font-medium">Nilai Terpilih: {{ sliderValue[0] }}</div>
      </div>
    </section>

    <!-- 50. SONNER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">50. Sonner</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen notifikasi (*toast*) yang dirancang khusus untuk kenyamanan developer, dilengkapi
          *stacking* otomatis.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Button
          variant="outline"
          @click="toast('Event telah dibuat', { description: 'Senin, 18 Mei 2026 pukul 09:00' })"
        >
          Tampilkan Notifikasi (Toast)
        </Button>
      </div>
    </section>

    <!-- 51. SPINNER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">51. Spinner</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Indikator visual berbentuk animasi memutar untuk menunjukkan proses yang sedang berjalan.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center gap-6 items-center">
        <Spinner size="sm" />
        <Spinner size="default" />
        <Spinner size="lg" />
      </div>
    </section>

    <!-- 52. STEPPER -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">52. Stepper</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Navigasi yang memandu pengguna melalui serangkaian proses atau form yang dibagi menjadi
          beberapa langkah.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card">
        <Stepper v-model="stepperValue" class="flex w-full items-start gap-2">
          <StepperItem
            v-for="step in stepperSteps"
            :key="step.step"
            class="relative flex w-full flex-col items-center justify-center"
            :step="step.step"
          >
            <StepperSeparator
              v-if="step.step !== stepperSteps[stepperSteps.length - 1].step"
              class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
            />

            <StepperTrigger as-child>
              <Button
                :variant="
                  step.step === stepperValue
                    ? 'default'
                    : step.step < stepperValue
                      ? 'default'
                      : 'outline'
                "
                size="icon"
                class="z-10 rounded-full shrink-0"
                :class="[
                  step.step === stepperValue
                    ? 'ring-2 ring-ring ring-offset-2 ring-offset-background'
                    : ''
                ]"
              >
                {{ step.step }}
              </Button>
            </StepperTrigger>

            <div class="mt-4 flex flex-col items-center text-center">
              <StepperTitle
                :class="[
                  step.step === stepperValue
                    ? 'text-primary font-semibold'
                    : 'text-muted-foreground'
                ]"
              >
                {{ step.title }}
              </StepperTitle>
              <StepperDescription
                :class="[
                  step.step === stepperValue
                    ? 'text-primary font-semibold'
                    : 'text-muted-foreground'
                ]"
              >
                {{ step.description }}
              </StepperDescription>
            </div>
          </StepperItem>
        </Stepper>
      </div>
    </section>

    <!-- 53. SWITCH -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">53. Switch</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Tombol geser (toggle) untuk mengaktifkan atau menonaktifkan suatu pengaturan tunggal.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <div class="flex items-center space-x-2">
          <Switch id="airplane-mode" v-model="switchValue" />
          <Label for="airplane-mode">Airplane Mode</Label>
        </div>
      </div>
    </section>

    <!-- 54. TABLE -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">54. Table</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Tabel data responsif dengan desain yang rapi dan mudah disesuaikan.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card">
        <Table>
          <TableCaption>Daftar invoice terbaru Anda.</TableCaption>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[100px]">Invoice</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Metode</TableHead>
              <TableHead class="text-right">Jumlah</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow>
              <TableCell class="font-medium">INV001</TableCell>
              <TableCell>Paid</TableCell>
              <TableCell>Credit Card</TableCell>
              <TableCell class="text-right">$250.00</TableCell>
            </TableRow>
            <TableRow>
              <TableCell class="font-medium">INV002</TableCell>
              <TableCell>Pending</TableCell>
              <TableCell>PayPal</TableCell>
              <TableCell class="text-right">$150.00</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </section>

    <!-- 55. TABS -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">55. Tabs</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen navigasi untuk memisahkan konten ke dalam beberapa tampilan (*tab*) berbeda.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Tabs default-value="account" class="w-[400px]">
          <TabsList class="grid w-full grid-cols-2">
            <TabsTrigger value="account">Account</TabsTrigger>
            <TabsTrigger value="password">Password</TabsTrigger>
          </TabsList>
          <TabsContent value="account">
            <div class="p-4 bg-muted/50 rounded-lg text-sm">
              Buat perubahan pada akun Anda di sini. Klik simpan saat Anda selesai.
            </div>
          </TabsContent>
          <TabsContent value="password">
            <div class="p-4 bg-muted/50 rounded-lg text-sm">
              Ubah kata sandi Anda di sini. Setelah menyimpan, Anda akan keluar.
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </section>

    <!-- 56. TAGS INPUT -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">56. Tags Input</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Input yang mengubah kata yang dipisahkan koma atau Enter menjadi elemen *tag* interaktif.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <TagsInput v-model="tagsInputValue" class="w-80">
          <TagsInputItem v-for="item in tagsInputValue" :key="item" :value="item">
            <TagsInputItemText />
            <TagsInputItemDelete />
          </TagsInputItem>
          <TagsInputInput placeholder="Tambahkan tag..." />
        </TagsInput>
      </div>
    </section>

    <!-- 57. TEXTAREA -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">57. Textarea</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kolom teks multi-baris yang akan otomatis membesar (*auto-resize*) sesuai isi ketikan.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Textarea
          v-model="textareaValue"
          placeholder="Tulis pesan Anda di sini..."
          class="w-[400px] min-h-[100px]"
        />
      </div>
    </section>

    <!-- 58. TOGGLE -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">58. Toggle</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Tombol interaktif dengan status *on* dan *off* berbentuk *button toggle*.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Toggle v-model="toggleValue" aria-label="Toggle italic">
          <span class="italic font-serif">I</span>
        </Toggle>
      </div>
    </section>

    <!-- 59. TOGGLE GROUP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">59. Toggle Group</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kumpulan tombol *toggle* yang bisa membatasi pengguna untuk memilih satu (tunggal) atau
          beberapa (multi) opsi.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <ToggleGroup v-model="toggleGroupValue" type="single">
          <ToggleGroupItem value="left" aria-label="Toggle Left"> Kiri </ToggleGroupItem>
          <ToggleGroupItem value="center" aria-label="Toggle Center"> Tengah </ToggleGroupItem>
          <ToggleGroupItem value="right" aria-label="Toggle Right"> Kanan </ToggleGroupItem>
        </ToggleGroup>
      </div>
    </section>

    <!-- 60. TOOLTIP -->
    <section class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">60. Tooltip</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Label informasi melayang yang muncul saat pengguna mengarahkan kursor (*hover*) ke suatu
          elemen.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center h-[100px] items-center">
        <TooltipProvider>
          <Tooltip>
            <TooltipTrigger as-child>
              <Button variant="outline">Arahkan kursor ke sini</Button>
            </TooltipTrigger>
            <TooltipContent>
              <p>Informasi tambahan muncul di sini.</p>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </div>
    </section>

    <Toaster />
  </div>
</template>
