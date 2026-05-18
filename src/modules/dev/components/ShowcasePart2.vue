<script setup>
import { defineProps, onMounted, ref } from 'vue'
import { AlertCircle, BellRing, ChevronsUpDown, Settings, User } from 'lucide-vue-next'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
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
import { Button } from '@/components/ui/button'
import { ButtonGroup } from '@/components/ui/button-group'
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle
} from '@/components/ui/card'
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious
} from '@/components/ui/carousel'
import { ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart'
import { Checkbox } from '@/components/ui/checkbox'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from '@/components/ui/dialog'
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
import { Input } from '@/components/ui/input'

const props = defineProps({
  searchQuery: {
    type: String,
    default: ''
  }
})

const match = keywords => {
  if (!props.searchQuery) return true
  return keywords.toLowerCase().includes(props.searchQuery.toLowerCase())
}

const isCollapsibleOpen = ref(false)
const contextMenuRadioValue = ref('pedro')
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

const drawerOpen = ref(false)
const dialogOpen = ref(false)
</script>

<template>
  <div class="space-y-12">
    <!-- 11. CARD -->
    <section v-show="match('card')" class="space-y-4">
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
    <section v-show="match('carousel')" class="space-y-4">
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
    <section v-show="match('checkbox')" class="space-y-4">
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
    <section v-show="match('collapsible')" class="space-y-4">
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
    <section v-show="match('context menu')" class="space-y-4">
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
    <section v-show="match('chart')" class="space-y-4">
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
    <section v-show="match('combobox')" class="space-y-4">
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
    <section v-show="match('command')" class="space-y-4">
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
    <section v-show="match('dialog')" class="space-y-4">
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
    <section v-show="match('drawer')" class="space-y-4">
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
  </div>
</template>
