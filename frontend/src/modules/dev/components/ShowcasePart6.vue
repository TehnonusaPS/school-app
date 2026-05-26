<script setup>
import { defineProps, ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import {
  Stepper,
  StepperDescription,
  StepperIndicator,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger
} from '@/components/ui/stepper'
import { Switch } from '@/components/ui/switch'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText
} from '@/components/ui/tags-input'
import { Textarea } from '@/components/ui/textarea'
import { Toggle } from '@/components/ui/toggle'
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

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
const switchValue = ref(false)
const tagsInputValue = ref(['Apple', 'Banana'])
const textareaValue = ref('')
const toggleValue = ref(false)
const toggleGroupValue = ref('center')

const stepperSteps = [
  { step: 1, title: 'Akun', description: 'Buat detail akun Anda' },
  { step: 2, title: 'Profil', description: 'Atur profil publik' },
  { step: 3, title: 'Selesai', description: 'Konfirmasi dan simpan' }
]
const stepperValue = ref(2)
</script>

<template>
  <div class="space-y-12">
    <!-- 51. SPINNER -->
    <section v-show="match('spinner')" class="space-y-4">
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
    <section v-show="match('stepper')" class="space-y-4">
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
    <section v-show="match('switch')" class="space-y-4">
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
    <section v-show="match('table')" class="space-y-4">
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
    <section v-show="match('tabs')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">55. Tabs</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen navigasi untuk memisahkan konten ke dalam beberapa tampilan (*tab*) berbeda.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <div class="flex w-full max-w-sm flex-col gap-6">
          <Tabs default-value="account">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="account">
                Account
              </TabsTrigger>
              <TabsTrigger value="password">
                Password
              </TabsTrigger>
            </TabsList>
            <TabsContent value="account">
              <Card>
                <CardHeader>
                  <CardTitle>Account</CardTitle>
                  <CardDescription>
                    Make changes to your account here. Click save when you're
                    done.
                  </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-6">
                  <div class="grid gap-3">
                    <Label for="tabs-demo-name">Name</Label>
                    <Input id="tabs-demo-name" default-value="Pedro Duarte" />
                  </div>
                  <div class="grid gap-3">
                    <Label for="tabs-demo-username">Username</Label>
                    <Input id="tabs-demo-username" default-value="@peduarte" />
                  </div>
                </CardContent>
                <CardFooter>
                  <Button>Save changes</Button>
                </CardFooter>
              </Card>
            </TabsContent>
            <TabsContent value="password">
              <Card>
                <CardHeader>
                  <CardTitle>Password</CardTitle>
                  <CardDescription>
                    Change your password here. After saving, you'll be logged
                    out.
                  </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-6">
                  <div class="grid gap-3">
                    <Label for="tabs-demo-current">Current password</Label>
                    <Input id="tabs-demo-current" type="password" />
                  </div>
                  <div class="grid gap-3">
                    <Label for="tabs-demo-new">New password</Label>
                    <Input id="tabs-demo-new" type="password" />
                  </div>
                </CardContent>
                <CardFooter>
                  <Button>Save password</Button>
                </CardFooter>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </section>

    <!-- 56. TAGS INPUT -->
    <section v-show="match('tags input')" class="space-y-4">
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
    <section v-show="match('textarea')" class="space-y-4">
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
    <section v-show="match('toggle')" class="space-y-4">
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
    <section v-show="match('toggle group')" class="space-y-4">
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
    <section v-show="match('tooltip')" class="space-y-4">
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
  </div>
</template>
