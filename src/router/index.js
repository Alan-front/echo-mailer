import { createRouter, createWebHistory } from 'vue-router'
 import Bandeja from '@/components/Bandeja.vue'
import Enviador_ from '@/components/Enviador_.vue'
import Campanas from '@/components/Campanas.vue'
import Archivos from '@/components/Archivos.vue'

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
    redirect: '/enviador-alterno' 
  },
  {
    path: '/campanas',
    name: '/campanas',
    component: Campanas 
  },
  {
    path: '/archivos',
    name: '/archivos',
    component: Archivos
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
