<script setup>
import {
  BadgeCheck,
  Bell,
  ChevronsUpDown,
  LogOut,
  Sun,
  Moon,
  Palette,
  Sparkles,
  Square
} from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar
} from '@/components/ui/sidebar'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const { isMobile } = useSidebar()
const auth = useAuthStore()
const router = useRouter()
const showLogoutDialog = ref(false)

const confirmLogout = () => {
  auth.logout()
  router.push('/login')
}

// --- Theme Logic ---
const isDark = ref(false)
// Nama tema: 'blue' | 'emerald' | 'indigo' | 'bronze' | 'navy' | 'zinc'
const activeThemeStyle = ref('blue')
// Finish: 'glossy' | 'solid' (berlaku untuk semua tema)
const themeFinish = ref('glossy')

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

/** Hapus semua class theme-* dari body */
const clearThemeClasses = () => {
  document.body.classList.forEach(cls => {
    if (cls.startsWith('theme-')) {
      document.body.classList.remove(cls)
    }
  })
}

/**
 * Terapkan class tema ke body:
 * - semua tema glossy → theme-{name}
 * - semua tema solid  → theme-{name} finish-solid
 */
const applyThemeClass = (styleName, finish) => {
  clearThemeClasses()
  document.body.classList.remove('finish-solid')
  document.body.classList.add(`theme-${styleName}`)
  if (finish === 'solid') {
    document.body.classList.add('finish-solid')
  }
}

const setThemeStyle = (styleName) => {
  activeThemeStyle.value = styleName
  // Pertahankan finish saat pindah tema agar konsisten
  applyThemeClass(styleName, themeFinish.value)
  localStorage.setItem('themeStyle', styleName)
}

const toggleThemeFinish = () => {
  themeFinish.value = themeFinish.value === 'glossy' ? 'solid' : 'glossy'
  applyThemeClass(activeThemeStyle.value, themeFinish.value)
  localStorage.setItem('themeFinish', themeFinish.value)
}

const cycleThemeStyle = () => {
  const themes = ['blue', 'emerald', 'indigo', 'bronze', 'navy', 'zinc']
  const currentIndex = themes.indexOf(activeThemeStyle.value)
  const newStyle = themes[(currentIndex + 1) % themes.length]
  setThemeStyle(newStyle)
}

const themeNames = {
  'blue':    'Blue',
  'emerald': 'Emerald',
  'indigo':  'Indigo',
  'bronze':  'Bronze',
  'navy':    'Navy',
  'zinc':    'Zinc'
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (
    savedTheme === 'dark' ||
    (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }

  // Restore theme style (support legacy 'tahoe' → map ke 'blue')
  const savedThemeStyle = localStorage.getItem('themeStyle') || 'blue'
  const mappedStyle = savedThemeStyle === 'tahoe' ? 'blue' : savedThemeStyle
  activeThemeStyle.value = mappedStyle

  // Restore finish (berlaku untuk semua tema)
  const savedFinish = localStorage.getItem('themeFinish') || 'glossy'
  themeFinish.value = savedFinish

  applyThemeClass(mappedStyle, savedFinish)
  // Simpan ulang jika ada konversi dari 'tahoe'
  if (savedThemeStyle === 'tahoe') localStorage.setItem('themeStyle', 'blue')
})
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton
            size="lg"
            class="bg-sidebar-accent text-sidebar-accent-foreground hover:bg-sidebar-accent/80 data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
          >
            <Avatar class="h-8 w-8 rounded-lg">
              <AvatarImage :src="user.avatar" :alt="user.name" />
              <AvatarFallback class="rounded-lg"> CN </AvatarFallback>
            </Avatar>
            <div class="grid flex-1 text-left text-sm leading-tight">
              <span class="truncate font-medium">{{ user.name }}</span>
              <span class="truncate text-xs text-muted-foreground uppercase font-semibold">{{
                user.role
              }}</span>
            </div>
            <ChevronsUpDown class="ml-auto size-4" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
          :side="isMobile ? 'bottom' : 'right'"
          align="end"
          :side-offset="4"
        >
          <DropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
              <Avatar class="h-8 w-8 rounded-lg">
                <AvatarImage :src="user.avatar" :alt="user.name" />
                <AvatarFallback class="rounded-lg"> CN </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">{{ user.name }}</span>
                <span class="truncate text-xs">{{ user.email }}</span>
              </div>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuLabel class="text-xs text-muted-foreground">Tampilan &amp; Tema</DropdownMenuLabel>

            <DropdownMenuItem @select.prevent="toggleTheme" class="cursor-pointer">
              <Sun v-if="isDark" class="size-4" />
              <Moon v-else class="size-4" />
              {{ isDark ? 'Beralih Mode Terang' : 'Beralih Mode Gelap' }}
            </DropdownMenuItem>

            <DropdownMenuItem @select.prevent="cycleThemeStyle" class="cursor-pointer">
              <Palette class="size-4" />
              <div class="truncate">
                Ganti Tema: <span class="font-semibold text-primary ml-1">{{ themeNames[activeThemeStyle] || 'Blue' }}</span>
              </div>
            </DropdownMenuItem>

            <DropdownMenuItem
              @select.prevent="toggleThemeFinish"
              class="cursor-pointer"
            >
              <Sparkles v-if="themeFinish === 'glossy'" class="size-4" />
              <Square v-else class="size-4" />
              <div class="truncate">
                Gaya Tema:
                <span class="font-semibold text-primary ml-1">
                  {{ themeFinish === 'glossy' ? 'Glossy' : 'Solid' }}
                </span>
              </div>
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem>
              <BadgeCheck />
              Account
            </DropdownMenuItem>
            <DropdownMenuItem>
              <Bell />
              Notifications
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuItem
            class="text-destructive focus:bg-destructive/10 focus:text-destructive cursor-pointer"
            @click="showLogoutDialog = true"
          >
            <LogOut />
            Log out
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>

  <AlertDialog :open="showLogoutDialog" @update:open="showLogoutDialog = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Konfirmasi Keluar</AlertDialogTitle>
        <AlertDialogDescription>
          Apakah Anda yakin ingin keluar dari sistem? Sesi Anda akan diakhiri.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Batal</AlertDialogCancel>
        <AlertDialogAction
          class="bg-destructive text-white hover:bg-destructive/90"
          @click="confirmLogout"
        >
          Ya, Keluar
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>