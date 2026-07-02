<script setup>
import { ChevronRight } from 'lucide-vue-next'
import { useRoute } from 'vue-router'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { navItemSlide } from '@/config/motion'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import {
  HoverCard,
  HoverCardTrigger,
  HoverCardContent
} from '@/components/ui/hover-card'
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem
} from '@/components/ui/sidebar'

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const route = useRoute()
const auth = useAuthStore()
const getNavDelay = (index) => (auth.isJustLoggedIn ? 1200 : 200) + (index * 80)

// Gunakan computed agar reaktifitasnya terjamin
const currentPath = computed(() => route.path)

const isPageActive = url => {
  if (!url || url === '#' || url === '') return false

  // Bersihkan slash di awal dan akhir untuk perbandingan yang konsisten
  const cleanCurrent = currentPath.value.replace(/^\/|\/$/g, '')
  const cleanTarget = url.replace(/^\/|\/$/g, '')

  // Menu dianggap aktif jika:
  // 1. Path-nya sama persis
  // 2. Path saat ini adalah anak dari target (misal: /siswa/tambah adalah anak dari /siswa)
  return cleanCurrent === cleanTarget || cleanCurrent.startsWith(cleanTarget + '/')
}

const isParentActive = items => {
  if (!items) return false
  return items.some(sub => isPageActive(sub.url))
}

// ── Accordion Logic ─────────────────────────────────────────────────────────
// Menyimpan index parent yang sedang terbuka. null = semua tertutup.
// Inisialisasi: buka parent yang sedang aktif (jika ada).
const openIndex = ref(
  props.items.findIndex(item => item.items?.length && isParentActive(item.items))
)

const toggleItem = (index) => {
  openIndex.value = openIndex.value === index ? null : index
}
// ────────────────────────────────────────────────────────────────────────────
</script>

<template>
  <SidebarGroup>
    <SidebarGroupLabel>Menu Utama</SidebarGroupLabel>
    <SidebarMenu class="gap-1.5 px-2 group-data-[collapsible=icon]:px-0">
      <template v-for="(item, itemIndex) in items" :key="item.title">
        <!-- CASE 1: MENU DENGAN SUB-ITEMS -->
        <SidebarMenuItem 
          v-if="item.items && item.items.length > 0"
          v-motion
          :initial="navItemSlide.initial"
          :enter="{ ...navItemSlide.enter, transition: { ...navItemSlide.enter.transition, delay: getNavDelay(itemIndex) } }"
        >
          
          <!-- HOVERCARD UNTUK MODE COLLAPSED (ICON) -->
          <HoverCard :open-delay="100" :close-delay="100">
            <HoverCardTrigger as-child>
              <!-- Tombol ini HANYA tampil saat mode collapsed -->
              <SidebarMenuButton
                class="hidden group-data-[collapsible=icon]:flex !h-10 !px-0 justify-center text-sm transition-colors duration-200"
                :class="[
                  isParentActive(item.items)
                    ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold'
                    : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                ]"
              >
                <component :is="item.icon" v-if="item.icon" class="!size-5 shrink-0" />
              </SidebarMenuButton>
            </HoverCardTrigger>
            <HoverCardContent side="right" align="start" :side-offset="8" class="w-56 p-2 rounded-lg flex flex-col gap-1">
              <div class="px-2 py-1.5 font-bold text-sm text-foreground">{{ item.title }}</div>
              <div class="h-px bg-border my-1 -mx-2"></div>
              <router-link
                v-for="subItem in item.items" 
                :key="subItem.title" 
                :to="subItem.url"
                class="flex items-center px-2 py-1.5 text-sm rounded-md transition-colors hover:bg-muted hover:text-foreground outline-none focus:outline-none focus:ring-0"
                :class="[isPageActive(subItem.url) ? 'bg-primary/10 text-primary font-bold' : 'text-muted-foreground']"
              >
                {{ subItem.title }}
              </router-link>
            </HoverCardContent>
          </HoverCard>

          <!-- COLLAPSIBLE UNTUK MODE EXPANDED -->
          <Collapsible
            :open="openIndex === itemIndex"
            @update:open="toggleItem(itemIndex)"
            class="group/collapsible group-data-[collapsible=icon]:hidden w-full"
          >
            <CollapsibleTrigger as-child>
              <SidebarMenuButton
                :tooltip="item.title"
                class="!h-11 px-3 text-sm transition-colors duration-200 w-full"
                :class="[
                  isParentActive(item.items)
                    ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold'
                    : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                ]"
              >
                <component :is="item.icon" v-if="item.icon" class="!size-5 shrink-0" />
                <span>{{ item.title }}</span>
                <ChevronRight
                  class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90 !size-4"
                />
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent class="mt-1">
              <SidebarMenuSub class="ml-4 border-l-2 border-primary/10 pl-2">
                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                  <!-- Jika subItem punya anak lagi (Level 3) -->
                  <Collapsible
                    v-if="subItem.items && subItem.items.length > 0"
                    as-child
                    :default-open="isParentActive(subItem.items)"
                    class="group/subcollapsible"
                  >
                    <CollapsibleTrigger as-child>
                      <SidebarMenuSubButton
                        class="!h-9 px-3 text-xs transition-all duration-200 cursor-pointer w-full flex items-center justify-between"
                        :class="[
                          isParentActive(subItem.items)
                            ? 'font-semibold text-foreground'
                            : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                        ]"
                      >
                        <span>{{ subItem.title }}</span>
                        <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/subcollapsible:rotate-90 !size-3.5" />
                      </SidebarMenuSubButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent class="mt-1">
                      <SidebarMenuSub class="ml-2 border-l border-primary/20 pl-2">
                        <SidebarMenuSubItem v-for="child in subItem.items" :key="child.title">
                          <SidebarMenuSubButton
                            as-child
                            :is-active="isPageActive(child.url)"
                            class="!h-8 px-2 text-[11px] transition-all duration-200"
                            :class="[
                              isPageActive(child.url)
                                ? 'glossy-active font-semibold'
                                : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                            ]"
                          >
                            <router-link :to="child.url">
                              <span>{{ child.title }}</span>
                            </router-link>
                          </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                      </SidebarMenuSub>
                    </CollapsibleContent>
                  </Collapsible>

                  <!-- Jika subItem biasa (Level 2) -->
                  <SidebarMenuSubButton
                    v-else
                    as-child
                    :is-active="isPageActive(subItem.url)"
                    class="!h-9 px-3 text-xs transition-colors duration-200"
                    :class="[
                      isPageActive(subItem.url)
                        ? 'glossy-active font-semibold'
                        : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                    ]"
                  >
                    <router-link :to="subItem.url" class="outline-none focus:outline-none focus:ring-0">
                      <span>{{ subItem.title }}</span>
                    </router-link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </Collapsible>
        </SidebarMenuItem>

        <!-- CASE 2: MENU TUNGGAL (DASHBOARD) -->
        <SidebarMenuItem
          v-motion
          :initial="navItemSlide.initial"
          :enter="{ ...navItemSlide.enter, transition: { ...navItemSlide.enter.transition, delay: getNavDelay(itemIndex) } }"
        >
          <SidebarMenuButton
            v-if="!item.items || item.items.length === 0"
            as-child
            :tooltip="item.title"
            class="!h-11 px-3 text-sm transition-colors duration-200 group-data-[collapsible=icon]:!h-10 group-data-[collapsible=icon]:!px-0 group-data-[collapsible=icon]:justify-center"
            :class="[
              isPageActive(item.url)
                ? 'glossy-active font-semibold'
                : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
            ]"
          >
            <router-link :to="item.url" class="outline-none focus:outline-none focus:ring-0">
              <component :is="item.icon" v-if="item.icon" class="!size-5 shrink-0" />
              <span class="group-data-[collapsible=icon]:hidden">{{ item.title }}</span>
            </router-link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>
