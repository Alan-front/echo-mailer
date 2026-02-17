<template>
  <div class="login-overlay">
    <div class="login-box">
      <h2>Iniciar sesión</h2>
      <input v-model="username" type="text" placeholder="Usuario" />
      <input v-model="password" type="password" placeholder="Contraseña" />
      <button @click="login">Entrar</button>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoginModal",
  data() {
    return {
      username: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async login() {
      try {
        const res = await fetch(
          "http://localhost/prom_system/api/auth/login.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              username: this.username,
              password: this.password,
            }),
          },
        );
        const data = await res.json();
        if (data.success) {
          localStorage.setItem("token", data.token);
          this.$emit("login-success");
        } else {
          this.error = data.message;
        }
      } catch (e) {
        this.error = "Error de conexión";
      }
    },
  },
};
</script>

<style scoped>
.login-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.login-box {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  min-width: 300px;
}
</style>
