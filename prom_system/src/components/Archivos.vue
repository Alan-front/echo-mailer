<template>

<div class="container mt-4 div-selector">
	<div class="selector">
		<label for="menu">Selecciona un lanzamiento:</label>
		<select id="menu" v-model="idSeleccionado" class="form-select form-select-sm" aria-label=".form-select-lg example">
  <option selected disabled>Open this select menu</option>
 <option v-for="c in lasCampanas.filter(campana => campana.activa === '1' || campana.activa === '0')" :key="c.id" :value="c.id">
    {{ c.nombre_lanzamiento }} - {{ c.artista }}
</option>
  
</select>
	</div>

<div class="info-box">
  <span>{{ nombreLanzamiento }}</span> -
  <span>{{ nombreArtista }}</span> -
  <span>{{ nombreTipoLanzamiento }}</span> -
  <span>{{ nombreGenero }}</span> -
  <span>{{ nombreFirma }}</span>
</div>



</div>

<div class="container mt-4 cont-button">

  <button :disabled= "!nombreLanzamiento" class="btn btn-success btn-agregar" @click="agregarArchivo"><i class="bi bi-file-earmark-plus"></i> Agregar archivo</button>
</div>

<div class="container mt-4 div-selector">
  Archivos

    <table class="table  align-middle text-center">
      <thead>
          <tr>
            <td class="w-25">Tipo</td>
            <td class="w-25">Idioma</td>
            <td class="w-25">Link</td>
            <td class="w-25">Acciones</td>

          </tr>

      </thead>
      <tbody v-if="idSeleccionado">
        <tr v-for="a in archivosFiltrados" :key="a.id">
          <td>
  <i v-if="a.tipo === 'audio'" class="bi bi-music-note-beamed"></i>
  <i v-else-if="a.tipo === 'video'" class="bi bi-camera-reels-fill"></i>
  <i v-else-if="a.tipo === 'ficha'" class="bi bi-file-earmark-text"></i>
  {{ a.tipo }}
</td>

          <td>
  <div class="idioma-box">{{ a.idioma || '-' }}</div>
</td>


          <td><a :href="a.url" target="_blank"><i class="bi bi-link"></i></a> </td>
          <td>
            <div>
              <button type="button" class="btn btn-success" @click="editarArchivo(a)"><i class="bi bi-pencil-square"></i> UPDATE</button>
              <button type="button" class="btn btn-danger" @click="abrirModalEliminar(a.id)" ><i class="bi bi-trash3"></i> DELETE</button>
            </div>
          </td>
        </tr>
      </tbody>

      <tbody v-else>
    <tr>
      <td colspan="4">Selecciona una campaña para ver archivos</td>
    </tr>
  </tbody>


    </table>


</div>


    <!-- modal archivos -->

    <div class="modal fade" id="modalArchivo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Archivo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body modal-files">
        
        <form @submit.prevent="guardarArchivo">



          <div id="fileForm" class="form-group">
                    <label class="form-label">Tipo de Archivo</label>
                    <select class="form-control" v-model="tipoArchivo" id="fileType" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="audio">Audio</option>
                        <option value="video">Video</option>
                        <option value="ficha">Ficha Técnica</option>
                    </select>
                </div>
                
              <!-- style="display: none;" -->
                    <div class="form-group" v-if="tipoArchivo === 'ficha'"  >
                    <label class="form-label">Idioma</label>
                    <select class="form-control" v-model="idioma" id="language">
                        <option value="">Seleccionar idioma...</option>
                        <option value="esp">Español</option>
                        <option value="en">Inglés</option>
                        <option value="fr">Francés</option>
                        <option value="jp">Japonés</option>
                        <option value="uk">Ucraniano</option>
                        <option value="ru">Ruso</option>
                        <option value="co">Coreano</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Enlace del Archivo</label>
                    <input type="url" class="form-control" v-model="enlaceArchivo" id="fileUrl" placeholder="https://drive.google.com/... o https://mediafire.com/..." >
                </div>
          
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn" onclick="closeModal()">Cancelar</button>
                    <button 
                    type="button"
                    :disabled="!objetoCompleto"
                    @click="guardarArchivo"
                    class="btn btn-primary" 
                    >Guardar
                  </button>
                </div>


        </form>


      </div>
    </div>
  </div>
</div>



  <!-- modal editar -->

      <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Archivo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body modal-files">
        
        <form @submit.prevent="guardarEdicion">



          <div id="fileForm" class="form-group">
                    <label class="form-label">Tipo de Archivo</label>
                    <select class="form-control" v-model="tipoEditar" id="fileType" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="audio">Audio</option>
                        <option value="video">Video</option>
                        <option value="ficha">Ficha Técnica</option>
                    </select>
                </div>
                
              <!-- style="display: none;" -->
                    <div class="form-group" v-if="tipoEditar === 'ficha'"  >
                    <label class="form-label">Idioma</label>
                    <select class="form-control" v-model="idiomaEditar" id="language">
                        <option value="">Seleccionar idioma...</option>
                        <option value="esp">Español</option>
                        <option value="en">Inglés</option>
                        <option value="fr">Francés</option>
                        <option value="jp">Japonés</option>
                        <option value="uk">Ucraniano</option>
                        <option value="ru">Ruso</option>
                        <option value="co">Coreano</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Enlace del Archivo</label>
                    <input type="url" class="form-control" v-model="urlEditar" id="fileUrl" placeholder="https://drive.google.com/... o https://mediafire.com/..." >
                </div>
          
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn" onclick="closeModal()">Cancelar</button>
                    <button 
                    type="button"
                    :disabled="!objetoEditarCompleto"
                    @click="guardarEdicion"
                    class="btn btn-primary" 
                    >Guardar
                  </button>
                </div>


        </form>


      </div>
    </div>
  </div>
</div>

  <!-- modal confirmar eliminar -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar este archivo?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" @click="confirmarEliminacion">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast guardar-->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toastAgregado" class="toast text-bg-success" role="alert" data-bs-delay="2000">
    <div class="toast-body">
      Archivo agregado exitosamente ✅
    </div>
  </div>
</div>

<!-- Toast eliminar-->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toastEliminado" class="toast text-bg-success" role="alert" data-bs-delay="2000">
    <div class="toast-body">
      Archivo eliminado exitosamente ✅
    </div>
  </div>
</div>

<!-- Toast actuaizar-->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toastActualizado" class="toast text-bg-success" role="alert" data-bs-delay="2000">
    <div class="toast-body">
      Archivo actualizado exitosamente ✅
    </div>
  </div>
</div>



</template>

<script setup>

import { onMounted, ref, watch, computed } from 'vue';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';




const lasCampanas = ref ([]);

const idSeleccionado = ref("");


const nombreArtista = ref("");
const nombreLanzamiento = ref("");
const nombreTipoLanzamiento = ref("");
const nombreGenero = ref("");
const nombreFirma = ref("");


const tipoArchivo = ref("");
const idioma = ref("");
const enlaceArchivo = ref("");

const losArchivos = ref([]);

  // para editar

  const tipoEditar = ref("")
  const idiomaEditar = ref("")
  const urlEditar = ref("")
  const idEditar = ref("") 

// modal de carga
function agregarArchivo() {
  const modal = new bootstrap.Modal(document.getElementById('modalArchivo'));
  modal.show();
}
// modal de update
function editarArchivo (archivo) {
  tipoEditar.value = archivo.tipo;
  idiomaEditar.value = archivo.idioma;
  urlEditar.value = archivo.url;
  idEditar.value = archivo.id;

  const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
  modal.show();
}

function guardarEdicion() {
  const actualizado = {
    id: idEditar.value,
    tipo: tipoEditar.value,
    idioma: tipoEditar.value === 'ficha' ? idiomaEditar.value : null,
    url: urlEditar.value
  };

  fetch('http://localhost/conex-prom-system/api/update-archivo.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(actualizado)
  })
  .then(r => r.json())
  .then(data => {
    obtenerArchivos();
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
    modal.hide();

    const toast = new bootstrap.Toast(document.getElementById('toastActualizado'));
    toast.show();
  });

  
}
    //fin de modo editar

    //delete archivo

    const idPendienteEliminar = ref(null);


function abrirModalEliminar(id) {
  idPendienteEliminar.value = id; 
  const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
  modal.show(); 
}


function confirmarEliminacion() {
  eliminarArchivo(idPendienteEliminar.value); 
  const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
  modal.hide(); 

  const toast = new bootstrap.Toast(document.getElementById('toastEliminado'));
  toast.show();
}


    function eliminarArchivo(id) {

      

      fetch('http://localhost/conex-prom-system/api/delete-archivo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json'},
        body: JSON.stringify( { id })
      })
      .then(r => r.text())
.then(texto => {
  console.log('Respuesta cruda:', texto);
})

      .then(data => {
        console.log(data);
        obtenerArchivos();
      })
      .catch(err => console.error('Error al eliminar:', err));

    }

onMounted(()  =>{

  fetch('http://localhost/conex-prom-system/api/conex-campanas.php')
  .then(response => {
    if(!response.ok) {
      throw new Error ('Error al traer campañas: ' + response.status);
    }
    return response.json();
  })
  .then(json => {
    console.log('Aqui las campañas cargadas: ', json);
    lasCampanas.value = json;
    // idSeleccionado.value = "56";

  })
  .catch(err => {
    console.error('Error en la respuesta de campañas: ', err);
  });
});



watch(idSeleccionado, (nuevoId) => {
  const campana = lasCampanas.value.find(c => c.id == nuevoId);
  if (campana) {
    nombreArtista.value = campana.artista;
    nombreLanzamiento.value = campana.nombre_lanzamiento;
    nombreTipoLanzamiento.value = campana.tipo_de_lanzamiento;
    nombreGenero.value = campana.music_genre;
    nombreFirma.value = campana.sender_name || "Dr Yvan";
  }
});


function construirObjeto() {
  if (!idSeleccionado.value || !tipoArchivo.value || !enlaceArchivo.value) return;
  if (tipoArchivo.value === 'ficha' && !idioma.value) return;

  return {
    campana_id: idSeleccionado.value,
    tipo: tipoArchivo.value,
    idioma: tipoArchivo.value === 'ficha' ? idioma.value : null,
    url: enlaceArchivo.value
  };
}


/* ACTUALIZAR TABLA */
function obtenerArchivos() {
  fetch('http://localhost/conex-prom-system/api/conex-archivos.php')
    .then(r => r.json())
    .then(json => losArchivos.value = json);
}



  function guardarArchivo() {
  const archivo = construirObjeto();
  if (!archivo) return;

  fetch('http://localhost/conex-prom-system/api/enviar-archivos.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(archivo)
  })
  .then(res => res.ok ? res.json()  : Promise.reject('Error en l respuesta'))
  .then(data => {
    // console.log('Respuesta del backend:', data);
    obtenerArchivos();
    losArchivos.value.push({
  ...data,
  campaña_id: data.campana_id
});

  })
  .catch(err => console.error('Error al guardar', err));


  console.log("Objeto listo para enviar:", archivo);

  const modal = bootstrap.Modal.getInstance(document.getElementById('modalArchivo'));
modal.hide();

  tipoArchivo.value = "";
  idioma.value = "";
  enlaceArchivo.value = "";

  const toast = new bootstrap.Toast(document.getElementById('toastAgregado'));
  toast.show();

}

const objetoCompleto = computed(() => {
  return (
    idSeleccionado.value &&
    tipoArchivo.value &&
    enlaceArchivo.value &&
    (tipoArchivo.value !== 'ficha' || idioma.value)
  );
});

const objetoEditarCompleto = computed(() => {
  return (
    tipoEditar.value &&
    urlEditar.value &&
    (tipoEditar.value !== 'ficha' || idiomaEditar.value)
  );
});



    // TABLA DE ARCHIVOS

    
onMounted(()  =>{

  fetch('http://localhost/conex-prom-system/api/conex-archivos.php')
  .then(response => {
    if(!response.ok) {
      throw new Error ('Error al traer campañas: ' + response.status);
    }
    return response.json();
  })
  .then(json => {
    console.log('Aqui los archivos cargadas: ', json);
    losArchivos.value = json;
  })
  .catch(err => {
    console.error('Error en la respuesta de archivos: ', err);
  });
});

const archivosFiltrados = computed(() => {
  return losArchivos.value.filter(a => a["campaña_id"] == idSeleccionado.value);
});




</script>

<style scoped>

.div-selector{
	box-shadow: inset 1px 1px 2px #e4dfdf96, inset -1px -1px 2px #00000044;

	background-color: rgba(226, 229, 231, 0.192);
	min-height: 120px;
	padding: 20px;
	border-radius: 20px;
}

.div-selector .selector {
  width: 40%;
}

.cont-button{
  padding: 20px;
  border: 1px solid gray !important;

  display: flex;
  justify-content: flex-end;
}

.btn-agregar{
  right: 20px;
}


/* MODAL */

.modal-files{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    backdrop-filter: blur(5px);
    z-index: 1000;
}

.close-btn {
            background: rgba(255, 255, 255, 0.103);
            border: 1px solid grey;
            color: rgb(20, 20, 20);
            
            cursor: pointer;
            opacity: 0.7;
        }

        .idioma-box {
        width: 50px;
        text-align: center;
        justify-content: center;
        border: 3px solid #48adffec;
        box-shadow: 1px 2px 4px #48adffec;;
        padding: 1px;
        border-radius: 5px;
        display: inline-block;
        font-weight: bolder;
        color: #48adffec;
        text-shadow: #48adffec;
}

/* tabla */

td{
background-color: rgba(34, 36, 36, 0.096);
}

</style>