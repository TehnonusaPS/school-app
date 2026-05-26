<script setup>
import { defineProps, ref } from 'vue'
import {
  FolderOpen,
  Search,
  Settings,
  User,
  CalendarDays
} from 'lucide-vue-next'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
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
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle
} from '@/components/ui/empty'
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
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage
} from '@/components/ui/form'
import { HoverCard, HoverCardContent, HoverCardTrigger } from '@/components/ui/hover-card'
import { Input } from '@/components/ui/input'
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupInput,
  InputGroupText,
  InputGroupTextarea
} from '@/components/ui/input-group'
import { InputOTP, InputOTPGroup, InputOTPSeparator, InputOTPSlot } from '@/components/ui/input-otp'
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
import { Kbd } from '@/components/ui/kbd'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
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

const formSchema = toTypedSchema(
  z.object({
    username: z.string().min(2).max(50)
  })
)

function onSubmitForm(values) {
  console.log('Form submitted:', values)
}

const otpValue = ref('')
</script>

<template>
  <div class="space-y-12">
    <!-- 21. DROPDOWN MENU -->
    <section v-show="match('dropdown menu')" class="space-y-4">
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
    <section v-show="match('empty')" class="space-y-4">
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
    <section v-show="match('field')" class="space-y-4">
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
    <section v-show="match('form')" class="space-y-4">
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
    <section v-show="match('hover card')" class="space-y-4">
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
            <div class="flex items-start space-x-4">
              <Avatar>
                <AvatarImage src="https://github.com/vuejs.png" />
                <AvatarFallback>VC</AvatarFallback>
              </Avatar>
              <div class="space-y-1">
                <h4 class="text-sm font-semibold">@vuejs</h4>
                <p class="text-sm">The Progressive JavaScript Framework.</p>
                <div class="flex items-center pt-2">
                  <CalendarDays class="mr-2 h-4 w-4 opacity-70" />
                  <span class="text-xs text-muted-foreground"> Joined January 2014 </span>
                </div>
              </div>
            </div>
          </HoverCardContent>
        </HoverCard>
      </div>
    </section>

    <!-- 26. INPUT -->
    <section v-show="match('input')" class="space-y-4">
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
    <section v-show="match('input group')" class="space-y-4">
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
    <section v-show="match('input otp')" class="space-y-4">
      <div class="border-b pb-2">
        <h2 class="text-2xl font-semibold text-primary">28. Input OTP</h2>
        <p class="text-sm text-muted-foreground mt-1">
          Komponen khusus untuk memasukkan kode OTP (One Time Password) dengan pemisah antar blok
          angka.
        </p>
      </div>

      <div class="p-6 border rounded-xl bg-card flex justify-center">
        <InputOTP v-model="otpValue" maxlength="6">
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
    <section v-show="match('item')" class="space-y-4">
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
    <section v-show="match('kbd')" class="space-y-4">
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
  </div>
</template>
