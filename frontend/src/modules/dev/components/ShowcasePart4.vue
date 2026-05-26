<script setup>
import { defineProps, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
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
import { NativeSelect } from '@/components/ui/native-select'
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle
} from '@/components/ui/navigation-menu'
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput
} from '@/components/ui/number-field'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationLink,
  PaginationNext,
  PaginationPrevious
} from '@/components/ui/pagination'
import { PinInput, PinInputGroup, PinInputSeparator, PinInputSlot } from '@/components/ui/pin-input'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Progress } from '@/components/ui/progress'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'

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

const pinValue = ref([])
const progressValue = ref(33)
const radioValue = ref('comfortable')
</script>

<template>
  <div class="space-y-12">
    <!-- 31. LABEL -->
    <section v-show="match('label')" class="space-y-4">
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
    <section v-show="match('menubar')" class="space-y-4">
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
    <section v-show="match('native select')" class="space-y-4">
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
    <section v-show="match('navigation menu')" class="space-y-4">
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
    <section v-show="match('number field')" class="space-y-4">
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
    <section v-show="match('pagination')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">36. Pagination</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Kontrol navigasi penomoran halaman untuk dataset yang panjang.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <Pagination v-slot="{ page }" :total="100" :sibling-count="1" show-edges :default-page="2">
          <PaginationContent v-slot="{ items }">
            <PaginationFirst />
            <PaginationPrevious />
            <template v-for="(item, index) in items">
              <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" :is-active="item.value === page">
                {{ item.value }}
              </PaginationItem>
              <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>
            <PaginationNext />
            <PaginationLast />
          </PaginationContent>
        </Pagination>
      </div>
    </section>

    <!-- 37. PIN INPUT -->
    <section v-show="match('pin input')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">37. Pin Input</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Input yang dioptimalkan untuk memasukkan kode PIN atau kode keamanan rahasia.
        </p>
      </div>
      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <PinInput id="pin-input" v-model="pinValue" placeholder="○" mask>
          <PinInputGroup>
            <PinInputSlot v-for="(id, index) in 5" :key="id" :index="index" />
          </PinInputGroup>
        </PinInput>
      </div>
    </section>

    <!-- 38. POPOVER -->
    <section v-show="match('popover')" class="space-y-4">
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
    <section v-show="match('progress')" class="space-y-4">
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
    <section v-show="match('radio group')" class="space-y-4">
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
  </div>
</template>
