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
    <SidebarMenu class="gap-1.5 px-2 group-data-[collapsible=icon]:px-0">
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
              <SidebarMenuButton
                :tooltip="item.title"
                class="!h-11 px-3 text-sm transition-colors duration-200 group-data-[collapsible=icon]:!h-10 group-data-[collapsible=icon]:!px-0 group-data-[collapsible=icon]:justify-center"
                :class="[
                  isParentActive(item.items)
                    ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold'
                    : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                ]"
              >
                <component :is="item.icon" v-if="item.icon" class="!size-5 shrink-0" />
                <span class="group-data-[collapsible=icon]:hidden">{{ item.title }}</span>
                <ChevronRight
                  class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90 !size-4 group-data-[collapsible=icon]:hidden"
                />
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent class="mt-1 group-data-[collapsible=icon]:hidden">
              <SidebarMenuSub class="ml-4 border-l-2 border-primary/10 pl-2">
                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                  <SidebarMenuSubButton
                    as-child
                    :is-active="isPageActive(subItem.url)"
                    class="!h-9 px-3 text-xs transition-all duration-200"
                    :class="[
                      isPageActive(subItem.url)
                        ? 'glossy-active font-semibold'
                        : '!bg-transparent text-sidebar-foreground/70 hover:!bg-sidebar-accent hover:!text-sidebar-foreground font-medium'
                    ]"
                  >
                    <router-link :to="subItem.url">
                      <span>{{ subItem.title }}</span>
                    </router-link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </SidebarMenuItem>
        </Collapsible>

        <!-- CASE 2: MENU TUNGGAL (DASHBOARD) -->
        <SidebarMenuItem>
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
            <router-link :to="item.url">
              <component :is="item.icon" v-if="item.icon" class="!size-5 shrink-0" />
              <span class="group-data-[collapsible=icon]:hidden">{{ item.title }}</span>
            </router-link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>
