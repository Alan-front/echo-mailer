<template>
  <HeaderVue />

  <div class="container-fluid mt-4">
    <router-view /> 

<!-- <Footer /> -->
  </div>


</template>

<script setup>
import HeaderVue from "./components/Header.vue"
import Footer from "./components/Footer.vue";


</script>

<style scoped>


</style>
