<template>
  <div class="echo-nav">
    <nav class="navbar navbar-expand-lg bg-secondary fixed-top">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/enviador">
          <div class="logo-container">
        <div class="text-echo">
            <span>E</span><span>C</span><span>H</span><span>O</span>
        </div>
    </div>
        </router-link>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- MENÚ -->
             
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item echoLi">
              <router-link
                class="nav-link"
                :class="{ active: $route.path === '/enviador-alterno' }"
                to="/enviador-alterno"
              ><i class="fa-solid fa-house"></i> Inicio</router-link>
            </li>

            <li class="nav-item echoLi">
              <router-link
                class="nav-link"
                :class="{ active: $route.path === '/bandeja' }"
                to="/bandeja"
              ><i class="fa-solid fa-inbox"></i> Bandeja</router-link>
            </li>

            <li class="nav-item echoLi">
              <router-link 
              class="nav-link"
                :class="{ active: $route.path === '/campanas' }"
                to="/campanas"
              >
              <i class="fa-solid fa-bullhorn"></i> Campañas</router-link>
            </li>


            <li class="nav-item echoLi">
              <router-link
              class="nav-link"
                :class="{ active: $route.path === '/archivos' }"
                to="/archivos" 
              >
              <i class="fa-solid fa-folder-closed"></i> Archivos</router-link>
            </li>
          </ul>

          <!-- LOGIN -->

          <div class="d-flex align-items-center gap-3 text-white">
            <!-- <a href="#" class="d-flex align-items-center text-white text-decoration-none">
              <i class="fas fa-user me-1"></i> <span>Login</span>
            </a> -->
            <a href="#" class="d-flex flex-column align-items-center text-white text-decoration-none">
            <i class="fa-regular fa-user"></i>
            <span>User</span>
          </a>

          </div>

        </div>
      </div>
    </nav>
  </div>
</template>


<script setup>
import { useRoute } from 'vue-router'
const $route = useRoute()
</script>

<style scoped>

nav.navbar {
  background-color: hsla(318, 18%, 86%, 0.342) !important;
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  padding: 1px 20px;
}

.echoLi .nav-link {
  color: rgb(235, 238, 231) !important;
  font-size: 1.1em;
  text-shadow: 0 1px 2px rgba(0,0,0,0.5) ;
  margin-inline: 1px;
  padding-inline: 20px;
  
}

.echoLi .nav-link:hover{
  background-color: rgba(201, 204, 204, 0.514);
  border-radius: 18px;
  color: rgba(59, 6, 59, 0.795) !important;
  
  text-shadow: none;
  box-shadow: 1px 1px 2px rgba(233, 229, 229, 0.562) !important;
}


/* logo */

.logo-container {
            background: transparent;
            /* border: 1px solid #22c55e; */
            border-radius: 0.5rem;
            /* padding: 0.5rem; */
            display: flex;
            justify-content: center;
            align-items: center;
            width: fit-content;
            height: 50px;
            /* background: linear-gradient(45deg, #2c3e50, #4a627d); */
        }

        .text-echo {
            /* font-family: 'Open Sans', sans-serif; */
            font-family: "Ranchers", sans-serif;
  font-weight: 400;
  font-style: normal;
            
            font-size: 2.2rem;
            color: #ecf0f1;
            letter-spacing: 0.1em;
            position: relative;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
            perspective: 1000px;
        }

        .text-echo span {
            display: inline-block;
            transform-style: preserve-3d;
            transform-origin: center center;
            animation: echo-wave 2s infinite ease-in-out;
        }

        .text-echo span:nth-child(1) { animation-delay: 0s; }
        .text-echo span:nth-child(2) { animation-delay: 0.1s; }
        .text-echo span:nth-child(3) { animation-delay: 0.2s; }
        .text-echo span:nth-child(4) { animation-delay: 0.3s; }

        @keyframes echo-wave {
            0%, 100% { transform: translateZ(0) rotateY(0deg); opacity: 1; }
            50% { transform: translateZ(-10px) rotateY(5deg); opacity: 0.8; }
        }
</style>
