<template>
  <div class="min-h-screen flex flex-col bg-white">
    <section class="flex-1 flex flex-col items-center justify-center text-center px-6 py-20">
      <h2 class="text-4xl font-extrabold mb-4">Create & Send Quotations in Minutes</h2>
      <p class="text-lg text-gray-600 max-w-2xl mb-8">
        Manage customers,create quotations, and send them via email.
      </p>
      <div class="flex gap-4">
        <Button size="lg" @click="$router.push('/customers')">
          Get Started
        </Button>
      </div>
    </section>
    <footer id="contact" class="border-t py-6 text-center text-sm text-gray-500">
      &copy; 2025 AND I QUOTE. All rights reserved.
    </footer>
  </div>
</template>

<script setup lang="ts">
import { Button } from "@/components/ui/button"
</script>
