<script setup>
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
</template>
