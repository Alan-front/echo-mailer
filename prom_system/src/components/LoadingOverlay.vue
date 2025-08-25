<template>
  <div class="loading-overlay" v-if="show">
    <div class="backdrop"></div>
    <div class="loading-content">
      <div class="spinner-container">
        <div class="spinner"></div>
        <div class="pulse-ring"></div>
      </div>
      <h4 class="loading-title">{{ title }}</h4>
      <p class="loading-subtitle">{{ subtitle }}</p>
      <div class="progress-dots">
        <span class="dot" :class="{ active: dots >= 1 }"></span>
        <span class="dot" :class="{ active: dots >= 2 }"></span>
        <span class="dot" :class="{ active: dots >= 3 }"></span>
      </div>
    </div>
  </div>

  <!-- animacion componente de espera -->

  <LoadingOverlay 
  :show="showOverlay" 
  title="Analizando con caca..." 
  subtitle="Esto puede tomar unos minutos"
/>


</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'

import LoadingOverlay from './LoadingOverlay.vue';
const showOverlay = ref(false);




const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Procesando...'
  },
  subtitle: {
    type: String,
    default: 'Por favor espera mientras completamos la operación'
  }
})

const dots = ref(0)
let interval = null

watch(() => props.show, (newVal) => {
  if (newVal) {
    startAnimation()
  } else {
    stopAnimation()
  }
})

const startAnimation = () => {
  dots.value = 0
  interval = setInterval(() => {
    dots.value = dots.value >= 3 ? 1 : dots.value + 1
  }, 800)
}

const stopAnimation = () => {
  if (interval) {
    clearInterval(interval)
    interval = null
  }
  dots.value = 0
}

onUnmounted(() => {
  stopAnimation()
})
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(55, 77, 95, 0.85);
  backdrop-filter: blur(4px);
}

.loading-content {
  position: relative;
  text-align: center;
  background: var(--bright-echo, rgba(242, 252, 250, 0.774));
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  border: 2px solid var(--light-echo, #cbd5df);
  min-width: 300px;
}

.spinner-container {
  position: relative;
  width: 80px;
  height: 80px;
  margin: 0 auto 2rem;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid var(--light-echo, #cbd5df);
  border-top: 4px solid var(--cta-echo, rgb(102, 29, 29));
  border-radius: 50%;
  position: absolute;
  top: 10px;
  left: 10px;
  animation: spin 1s linear infinite;
}

.pulse-ring {
  width: 80px;
  height: 80px;
  border: 2px solid var(--cta-echo, rgb(102, 29, 29));
  border-radius: 50%;
  opacity: 0.3;
  animation: pulse 2s ease-in-out infinite;
}

.loading-title {
  color: var(--dark-echo, #374d5f);
  font-weight: bold;
  margin-bottom: 0.5rem;
  font-size: 1.3rem;
}

.loading-subtitle {
  color: var(--medium-echo, #6f8ba2);
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

.progress-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--light-echo, #cbd5df);
  transition: all 0.3s ease;
}

.dot.active {
  background: var(--cta-echo, rgb(102, 29, 29));
  transform: scale(1.2);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulse {
  0% {
    transform: scale(0.8);
    opacity: 0.3;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.1;
  }
  100% {
    transform: scale(0.8);
    opacity: 0.3;
  }
}
</style>