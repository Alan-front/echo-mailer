<template>
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- menu lateral -->
      <div class="col-md-3 ">
        <div class="d-flex flex-column gap-2 columCards">
          <!-- <button @click="cargarCampañas" class="btn btn-outline-info btn-camp">Bandeja</button> -->
          <template v-for="c in campañasEnviadas" :key="c.id">
            
            <div class="card w-100 card-btn"  v-if="c.activa !== '2'" @click="abrirBandeja(c.id)">
              <div class="card-body ca-ctb">
                <h5 class="card-title">{{ c.nombre_lanzamiento }}</h5>
                <p class="card-text">
                  <small class="text-body-secondary name-type">{{ c.artista }} - {{ c.tipo_de_lanzamiento }} </small>
                </p>
              </div>
            </div>
          
          </template>
          


          
        </div>
      </div>

     <!-- contenido principal -->
<div class="col-md-9 container-bandeja">
  
  <!-- filtros fijos -->
  <div class="div-filtros">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-primary btn-sm">Todos</button>
      <button class="btn btn-outline-secondary btn-sm">Necesitan Revisión</button>
      <button class="btn btn-outline-secondary btn-sm">Programados</button>
      <button class="btn btn-outline-secondary btn-sm">Respondidos</button>
      
      <div class="ms-auto">
        <input type="text" class="form-control form-control-sm" placeholder="Buscar..." style="width: 250px;" />
      </div>
    </div>
  </div>



        <!-- bandeja -->

        <div class="card div-bandeja">
  <div class="card-body p-0">
    <div class="list-group list-group-flush">
      <div v-for="mensaje in mensajesRecibidos" :key="mensaje.id_contacto" 
           class="list-group-item d-flex justify-content-between align-items-start py-3">
        
        <div class="d-flex w-100 div-inbox">
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h6 class="mb-0">{{ mensaje.nombre }}</h6>
              <small class="text-muted">{{ mensaje.fecha }}</small>
            </div>
            <p class="mb-1 fw-bold text-dark">{{ mensaje.asunto }}</p>
            <p class="mb-0 text-muted">{{ mensaje.mensaje }}</p>
            <div class="d-flex justify-content-between align-items-center mt-2">
      <small class="text-info">{{ mensaje.idioma }}</small>
      <span class="badge bg-warning text-dark">
        <i class="fas fa-clock me-1"></i> PROGRAMADO
      </span>
    </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

    <!-- fin Bandeja -->


      </div>
    </div>
  </div>
</template>

<script setup>


// import { C } from 'vitest/dist/chunks/reporters.d.DG9VKi4m';
import { ref, onMounted } from 'vue'

const campañasEnviadas = ref([]);
const emailsYaEnviados = ref([]);
const emailsImportados = ref([]);
const mensajesRecibidos = ref([]);




onMounted(() => {
  console.log("2do componente montado");

  fetch('http://localhost/conex-prom-system/api/conex-campanas.php')
    .then(res => res.json())
    .then(json => {
      campañasEnviadas.value = json;
      console.log('campEnviadas cargadas:', json);
    });

  fetch('http://localhost/conex-prom-system/api/emails-ya-enviados.php')
    .then(res => res.json())
    .then(json => {
      emailsYaEnviados.value = json;
      console.log(json);
    });

  // FETCH COMENTADO - te estorba por el momento
  /*
  fetch('http://localhost/probar_imap/probando-lectura.php')
    .then(res => res.json())
    .then(json => {
      emailsImportados.value = json;
      console.log(json);

      json.forEach(item => {
        console.log(
          'Asunto:', item.asunto + '\n' +
          'Remitente:', item.remitente + '\n' +
          'Fecha:', item.fecha + '\n' +
          'Mensaje:', item.mensaje + '\n' +
          '========================\n'
        );
      });
    });
  */
});


const abrirBandeja = (id) => {
  const campana = campañasEnviadas.value.find(c => c.id === id.toString());

  mensajesRecibidos.value = [];

  fetch('http://localhost/conex-prom-system/api/imap-test.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(campana)
  })
  .then(res => res.json())
  .then(res => {
    console.log ("Respuesta desde PHP:", res);
    mensajesRecibidos.value = res;
  })
  .catch(err => {
    console.error("Erros al obtener los emails:", err);
  });


};




function cargarCampañas() {
  const emailsFiltrados = emailsImportados.value
    .map(email => {
      const enviado = emailsYaEnviados.value.find(e => e.email === email.remitente);
      if (enviado) {
        return { ...email, campaña_id: enviado.campaña_id };
      }
    })
    .filter(email => email);
console.log('============================');
  console.log("Emails filtrados con campaña_id:");
  emailsFiltrados.forEach(e => {
    console.log(`Asunto: ${e.asunto}`);
    console.log(`Remitente: ${e.remitente}`);
    console.log(`Mensaje: ${e.mensaje}`);
    console.log(`Fecha: ${e.fecha}`);
    console.log(`Campaña ID: ${e.campaña_id}`);
    console.log('============================');
  });
}




</script>




<style scoped>
@import '../assets/echo-style.css';
</style>