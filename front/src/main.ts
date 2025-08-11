import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import { Toaster } from '@/components/ui/sonner'

const app = createApp(App)
app.use(router)
app.component('Toaster', Toaster)
app.mount('#app')
