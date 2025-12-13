<template>
  <div class="loading-container">
    <div class="message-counter" ref="counter">Cargando emails...</div>
    
    <div class="mailbox"></div>
    
    <div class="email"></div>
    <div class="email"></div>
    <div class="email"></div>
    <div class="email"></div>
    <div class="email"></div>
    
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    
    <div class="cloud"></div>
    <div class="cloud"></div>
    
    <div class="loading-text">¡Tus emails están llegando!</div>
    <div class="loading-subtext">Organizando mensajes de campaña...</div>
    
    <div class="progress-bar">
      <div class="progress-fill"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const counter = ref(null)
let sparkleInterval = null
let messageInterval = null

const messages = [
  'Conectando con el servidor...',
  'Descargando headers...',
  'Procesando emails...',
  'Organizando por fecha...',
  'Aplicando filtros...',
  '¡Casi listo!'
]

let currentMessage = 0

const updateCounter = () => {
  if (currentMessage < messages.length && counter.value) {
    counter.value.textContent = messages[currentMessage]
    currentMessage++
  }
}

const addSparkle = () => {
  const container = document.querySelector('.loading-container')
  if (!container) return
  
  const sparkle = document.createElement('div')
  sparkle.className = 'sparkle dynamic-sparkle'
  sparkle.style.left = Math.random() * 100 + '%'
  sparkle.style.top = Math.random() * 100 + '%'
  sparkle.style.animationDelay = Math.random() * 2 + 's'
  container.appendChild(sparkle)
  
  setTimeout(() => {
    if (sparkle.parentNode) {
      sparkle.remove()
    }
  }, 3000)
}

onMounted(() => {
  // Actualizar mensajes cada 2.5 segundos
  updateCounter()
  messageInterval = setInterval(updateCounter, 2500)
  
  // Agregar sparkles cada 2 segundos
  sparkleInterval = setInterval(addSparkle, 2000)
})

onUnmounted(() => {
  if (messageInterval) clearInterval(messageInterval)
  if (sparkleInterval) clearInterval(sparkleInterval)
})
</script>

<style scoped>
.loading-container {
  width: 100%;
  height: 400px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  position: relative;
  overflow: hidden;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-family: 'Arial', sans-serif;
}



.mailbox {
  width: 80px;
  height: 60px;
  background: var(--dark-echo);
  border-radius: 8px 8px 4px 4px;
  position: relative;
  margin-bottom: 30px;
  animation: mailboxBounce 2s ease-in-out infinite;
}

.mailbox::before {
  content: '';
  position: absolute;
  top: -8px;
  left: 10px;
  right: 10px;
  height: 8px;
  background: var(--medium-echo, #3a7a8a);
  border-radius: 4px 4px 0 0;
}

.mailbox::after {
  content: '';
  position: absolute;
  top: 20px;
  left: 20px;
  width: 40px;
  height: 20px;
  background: #ffffff;
  border-radius: 2px;
  opacity: 0.8;
}

@keyframes mailboxBounce {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.email {
  position: absolute;
  width: 40px;
  height: 30px;
  background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  opacity: 0;
}

.email::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 15px;
  background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
  border-radius: 4px 4px 0 0;
  border-bottom: 2px solid #2196f3;
}

.email::after {
  content: '';
  position: absolute;
  top: 6px;
  left: 6px;
  right: 6px;
  height: 2px;
  background: #666;
  border-radius: 1px;
  box-shadow: 0 4px 0 #999, 0 8px 0 #ccc;
}

.email:nth-child(2) { 
  animation: flyIn1 3s ease-out 0s forwards;
  background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
}
.email:nth-child(2)::before { 
  background: linear-gradient(135deg, #fff8e1 0%, #ffcc02 100%);
  border-bottom-color: #ff9800;
}

.email:nth-child(3) { 
  animation: flyIn2 3s ease-out 2s forwards;
  background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
}
.email:nth-child(3)::before { 
  background: linear-gradient(135deg, #f8bbd9 0%, #e91e63 100%);
  border-bottom-color: #9c27b0;
}

.email:nth-child(4) { 
  animation: flyIn3 3s ease-out 4s forwards;
  background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
}
.email:nth-child(4)::before { 
  background: linear-gradient(135deg, #a5d6a7 0%, #4caf50 100%);
  border-bottom-color: #2e7d32;
}

.email:nth-child(5) { 
  animation: flyIn4 3s ease-out 6s forwards;
  background: linear-gradient(135deg, #fff3e0 0%, #ffcdd2 100%);
}
.email:nth-child(5)::before { 
  background: linear-gradient(135deg, #ffab91 0%, #f44336 100%);
  border-bottom-color: #d32f2f;
}

.email:nth-child(6) { 
  animation: flyIn5 3s ease-out 8s forwards;
  background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
}
.email:nth-child(6)::before { 
  background: linear-gradient(135deg, #80cbc4 0%, #009688 100%);
  border-bottom-color: #00695c;
}

@keyframes flyIn1 {
  0% { 
    opacity: 1;
    transform: translate(-200px, -100px) rotate(-45deg) scale(0.5);
  }
  70% { 
    transform: translate(0px, 0px) rotate(0deg) scale(1.1);
  }
  100% { 
    opacity: 1;
    transform: translate(0px, 0px) rotate(0deg) scale(1);
  }
}

@keyframes flyIn2 {
  0% { 
    opacity: 1;
    transform: translate(250px, -150px) rotate(45deg) scale(0.3);
  }
  70% { 
    transform: translate(30px, 40px) rotate(0deg) scale(1.2);
  }
  100% { 
    opacity: 1;
    transform: translate(30px, 40px) rotate(0deg) scale(1);
  }
}

@keyframes flyIn3 {
  0% { 
    opacity: 1;
    transform: translate(-180px, 200px) rotate(-90deg) scale(0.4);
  }
  70% { 
    transform: translate(-30px, 40px) rotate(0deg) scale(1.1);
  }
  100% { 
    opacity: 1;
    transform: translate(-30px, 40px) rotate(0deg) scale(1);
  }
}

@keyframes flyIn4 {
  0% { 
    opacity: 1;
    transform: translate(300px, 50px) rotate(90deg) scale(0.6);
  }
  70% { 
    transform: translate(60px, 80px) rotate(0deg) scale(1.2);
  }
  100% { 
    opacity: 1;
    transform: translate(60px, 80px) rotate(0deg) scale(1);
  }
}

@keyframes flyIn5 {
  0% { 
    opacity: 1;
    transform: translate(-250px, 150px) rotate(-60deg) scale(0.5);
  }
  70% { 
    transform: translate(-60px, 80px) rotate(0deg) scale(1.1);
  }
  100% { 
    opacity: 1;
    transform: translate(-60px, 80px) rotate(0deg) scale(1);
  }
}

.loading-text {
  margin-top: 40px;
  font-size: 18px;
  color: var(--dark-echo, #4a90a4);
  font-weight: 600;
  animation: textPulse 2s ease-in-out infinite;
}

.loading-subtext {
  margin-top: 10px;
  font-size: 14px;
  color: var(--medium-echo, #666);
  animation: subtextFade 3s ease-in-out infinite;
}

@keyframes textPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}

@keyframes subtextFade {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

.progress-bar {
  width: 200px;
  height: 4px;
  background: #e0e0e0;
  border-radius: 2px;
  margin-top: 20px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--dark-echo, #4a90a4), var(--light-echo, #6ba3b8));
  border-radius: 2px;
  animation: progressFill 15s linear forwards;
  width: 0%;
}

@keyframes progressFill {
  0% { width: 0%; }
  100% { width: 100%; }
}

.sparkle, .dynamic-sparkle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: #ffd700;
  border-radius: 50%;
  animation: sparkleAnim 1.5s ease-in-out infinite;
}

.sparkle:nth-child(7) {
  top: 20%;
  left: 20%;
  animation-delay: 0s;
}

.sparkle:nth-child(8) {
  top: 30%;
  right: 25%;
  animation-delay: 0.5s;
}

.sparkle:nth-child(9) {
  bottom: 30%;
  left: 30%;
  animation-delay: 1s;
}

.sparkle:nth-child(10) {
  bottom: 20%;
  right: 20%;
  animation-delay: 1.5s;
}

@keyframes sparkleAnim {
  0%, 100% { 
    opacity: 0; 
    transform: scale(0);
  }
  50% { 
    opacity: 1; 
    transform: scale(1);
  }
}

.message-counter {
  position: absolute;
  top: 20px;
  right: 20px;
  background: var(--dark-echo, #4a90a4);
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  animation: counterUpdate 15s linear forwards;
}

@keyframes counterUpdate {
  0% { opacity: 0; }
  10% { opacity: 1; }
  20% { transform: scale(1.1); }
  25% { transform: scale(1); }
  100% { opacity: 1; }
}

.cloud {
  position: absolute;
  background: rgba(255,255,255,0.7);
  border-radius: 50px;
  opacity: 0.3;
}

.cloud:nth-child(11) {
  width: 60px;
  height: 20px;
  top: 10%;
  left: -60px;
  animation: floatCloud1 20s linear infinite;
}

.cloud:nth-child(12) {
  width: 80px;
  height: 25px;
  top: 80%;
  left: -80px;
  animation: floatCloud2 25s linear infinite;
}

@keyframes floatCloud1 {
  0% { left: -60px; }
  100% { left: 100%; }
}

@keyframes floatCloud2 {
  0% { left: -80px; }
  100% { left: 100%; }
}


</style>