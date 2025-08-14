<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Header -->
    <header class="border-b bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-primary">AND I QUOTE</h1>
        <nav class="flex gap-6">
          <a href="#features" class="text-gray-600 hover:text-primary transition">Features</a>
          <a href="#api" class="text-gray-600 hover:text-primary transition">API</a>
          <a href="#contact" class="text-gray-600 hover:text-primary transition">Contact</a>
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="flex-1 flex flex-col items-center justify-center text-center px-6 py-20">
      <h2 class="text-4xl font-extrabold mb-4">Create & Send Quotations in Minutes</h2>
      <p class="text-lg text-gray-600 max-w-2xl mb-8">
        Manage customers, generate product quotations, and send them via email with just a few clicks.
        Powered by Laravel, Vue, and Brevo API.
      </p>
      <div class="flex gap-4">
        <Button size="lg" @click="$router.push('/customers')">
          Get Started
        </Button>
        <Button size="lg" variant="outline" @click="$router.push('/about')">
          Learn More
        </Button>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="bg-gray-50 py-16">
      <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-2xl font-bold text-center mb-10">Why AND I QUOTE?</h3>
        <div class="grid md:grid-cols-3 gap-8">
          <Card>
            <CardHeader>
              <CardTitle>Customer Management</CardTitle>
              <CardDescription>Easily store and manage customer info.</CardDescription>
            </CardHeader>
            <CardContent>
              CRUD support, search capabilities, and easy navigation.
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Quotation Management</CardTitle>
              <CardDescription>Create multiple quotations per customer.</CardDescription>
            </CardHeader>
            <CardContent>
              Auto-calculate subtotals and grand totals with real-time updates.
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Email Integration</CardTitle>
              <CardDescription>Send quotations instantly via Brevo API.</CardDescription>
            </CardHeader>
            <CardContent>
              Attach files or include quotation details in the email body.
            </CardContent>
          </Card>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="border-t py-6 text-center text-sm text-gray-500">
      &copy; 2025 AND I QUOTE. All rights reserved.
    </footer>
  </div>
</template>

<script setup lang="ts">
import { Button } from "@/components/ui/button"
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card"
</script>
