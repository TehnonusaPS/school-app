<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator
} from '@/components/ui/breadcrumb'
import { Separator } from '@/components/ui/separator'
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'

const route = useRoute()
const router = useRouter()

// Fungsi untuk menelusuri hierarki breadcrumb secara rekursif
const breadcrumbs = computed(() => {
  const crumbs = []
  
  // Ambil rute saat ini
  let current = route
  
  // Masukkan halaman saat ini
  crumbs.unshift({
    title: current.meta.title || 'Halaman',
    active: true
  })

  // Telusuri parent jika ada
  let parentTitle = current.meta.parent
  while (parentTitle) {
    // Cari rute yang memiliki title yang sama dengan parentTitle ini
    const parentRoute = router.getRoutes().find(r => r.meta.title === parentTitle)
    
    if (parentRoute) {
      crumbs.unshift({
        title: parentRoute.meta.title,
        path: parentRoute.path,
        active: false
      })
      // Lanjut cari kakeknya (parent dari parent)
      parentTitle = parentRoute.meta.parent
    } else {
      // Jika tidak ketemu di rute, tapi parentTitle ada (misal top level menu), tetap tampilkan teksnya
      crumbs.unshift({
        title: parentTitle,
        active: false
      })
      parentTitle = null
    }
  }

  return crumbs
})
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header
        class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12"
      >
        <div class="flex items-center gap-2 px-4">
          <SidebarTrigger class="-ml-1" />
          <Separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
          <Breadcrumb>
            <BreadcrumbList>
              <template v-for="(crumb, index) in breadcrumbs" :key="index">
                <BreadcrumbItem>
                  <BreadcrumbLink v-if="!crumb.active" :href="crumb.path || '#'">
                    {{ crumb.title }}
                  </BreadcrumbLink>
                  <BreadcrumbPage v-else>
                    {{ crumb.title }}
                  </BreadcrumbPage>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
              </template>
            </BreadcrumbList>
          </Breadcrumb>
        </div>
      </header>
      <div class="flex flex-1 flex-col gap-4 p-4 pt-0 text-left">
        <router-view />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
