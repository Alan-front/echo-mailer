import { createRouter, createWebHistory } from 'vue-router'
 import Bandeja from '@/components/Bandeja.vue'
import Enviador_ from '@/components/Enviador_.vue'

const routes = [
  {
    path: '/bandeja',
    name: 'bandeja',
    component: Bandeja
  },
  {
    path: '/enviador-alterno',
    name: 'enviador_',
    component: Enviador_
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/enviador-alterno' // redirige para cargar por defecto
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
