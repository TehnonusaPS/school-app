<script setup>
import { ChevronRight } from 'lucide-vue-next'
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
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
</script>

<template>
  <SidebarGroup>
    <SidebarGroupLabel>Menu Utama</SidebarGroupLabel>
    <SidebarMenu>
      <template v-for="item in items" :key="item.title">
        <!-- CASE 1: MENU DENGAN SUB-ITEMS -->
        <Collapsible
          v-if="item.items && item.items.length > 0"
          as-child
          :default-open="isParentActive(item.items)"
          class="group/collapsible"
        >
          <SidebarMenuItem>
            <CollapsibleTrigger as-child>
              <!-- Paksa background putih jika tidak aktif, dan abu-abu jika aktif -->
              <SidebarMenuButton
                :tooltip="item.title"
                :class="[
                  isParentActive(item.items)
                    ? '!bg-sidebar-accent !text-sidebar-accent-foreground'
                    : '!bg-transparent'
                ]"
              >
                <component :is="item.icon" v-if="item.icon" />
                <span :class="{ 'font-bold': isParentActive(item.items) }">
                  {{ item.title }}
                </span>
                <ChevronRight
                  class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                />
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                  <SidebarMenuSubButton as-child :is-active="isPageActive(subItem.url)">
                    <router-link :to="subItem.url">
                      <span :class="{ 'font-bold': isPageActive(subItem.url) }">{{
                        subItem.title
                      }}</span>
                    </router-link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </SidebarMenuItem>
        </Collapsible>

        <!-- CASE 2: MENU TUNGGAL (DASHBOARD) -->
        <SidebarMenuItem v-else>
          <SidebarMenuButton
            as-child
            :tooltip="item.title"
            :class="[
              isPageActive(item.url)
                ? '!bg-sidebar-accent !text-sidebar-accent-foreground'
                : '!bg-transparent'
            ]"
          >
            <router-link :to="item.url">
              <component :is="item.icon" v-if="item.icon" />
              <span :class="{ 'font-bold': isPageActive(item.url) }">
                {{ item.title }}
              </span>
            </router-link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>
