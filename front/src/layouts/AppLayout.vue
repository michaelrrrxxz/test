<template>
  <header class="border-b bg-white">
    <div class="container mx-auto flex items-center justify-between h-14 px-4">
      <!-- Logo / Brand -->
      <RouterLink to="#" class="text-lg font-bold">
        AND I QUOTE
      </RouterLink>

      <!-- Navigation links on the right -->
      <NavigationMenu>
        <NavigationMenuList>
          <NavigationMenuItem>
            <RouterLink
              to="/customers"
              class="px-3 py-2 hover:text-primary transition-colors"
            >
              Customers
            </RouterLink>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>
    </div>
  </header>

  <!-- Main page content -->
  <main class="container mx-auto px-4 py-6">
    <slot />
  </main>
</template>

<script setup lang="ts">
import {
  NavigationMenu,
  NavigationMenuList,
  NavigationMenuItem
} from '@/components/ui/navigation-menu'
</script>
