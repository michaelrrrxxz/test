import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index'
import { Toaster } from './components/ui/sonner'

const app = createApp(App)

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'Default Title'
  next()
})

app.use(router)
app.component('Toaster', Toaster)
app.mount('#app')
