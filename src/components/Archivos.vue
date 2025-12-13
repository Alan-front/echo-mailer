<template>
  <div class="miContenedor container-archivos">
    <div class="container mt-4 div-selector tit-select">
      <div class="selector">
        <label for="menu">Selecciona un lanzamiento:</label>
        <select
          id="menu"
          v-model="idSeleccionado"
          class="form-select form-select-sm select-files w-50 w-sm-100"
          aria-label=".form-select-lg example"
        >
          <option selected disabled>Open this select menu</option>
          <option
            v-for="c in lasCampanas.filter(
              (campana) => campana.activa === '1' || campana.activa === '0'
            )"
            :key="c.id"
            :value="c.id"
          >
            {{ c.tipo_de_lanzamiento }} - {{ c.nombre_lanzamiento }} by
            {{ c.artista }}
          </option>
        </select>
      </div>

      <div class="info-box w-50 w-sm-100">
        <!-- <span>{{ nombreLanzamiento }} - </span> 
        <span>{{ nombreArtista }} - </span> -->
        <!-- <span>{{ nombreTipoLanzamiento }} - </span> -->
        <span>{{ nombreGenero }} - </span>
        <span v-if="nombreFirma">Campaign by {{ nombreFirma }}</span>
      </div>
    </div>

    <div class="container mt-4 cont-button">
      <button
        :disabled="!nombreLanzamiento"
        class="btn-edark"
        @click="agregarArchivo"
      >
        <i class="bi bi-file-earmark-plus"></i> Agregar archivo
      </button>
    </div>

    <div class="container mt-4 div-selector los-archivos">
      <label for="table">Archivos</label>

      <table class="table align-middle table-hover text-center table-files">
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
              <i
                v-else-if="a.tipo === 'video'"
                class="bi bi-camera-reels-fill"
              ></i>
              <i
                v-else-if="a.tipo === 'ficha'"
                class="bi bi-file-earmark-text"
              ></i>
              {{ a.tipo }}
            </td>

            <td>
              <div class="idioma-box">{{ a.idioma || "-" }}</div>
            </td>

            <td>
              <a :href="a.url" target="_blank"><i class="bi bi-link"></i></a>
            </td>
            <td>
              <div>
                <button
                  type="button"
                  class="btn btn-econf"
                  @click="editarArchivo(a)"
                >
                  <i class="bi bi-pencil-square"></i> UPDATE
                </button>
                <button
                  type="button"
                  class="btn btn-edelete"
                  @click="abrirModalEliminar(a.id)"
                >
                  <i class="bi bi-trash3"></i> DELETE
                </button>
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
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body modal-files">
            <form @submit.prevent="guardarArchivo">
              <div id="fileForm" class="form-group">
                <label class="form-label">Tipo de Archivo</label>
                <select
                  class="form-control"
                  v-model="tipoArchivo"
                  id="fileType"
                  required
                >
                  <option value="">Seleccionar tipo...</option>
                  <option value="audio">Audio</option>
                  <option value="video">Video</option>
                  <option value="ficha">Ficha Técnica</option>
                </select>
              </div>

              <!-- style="display: none;" -->
              <div class="form-group" v-if="tipoArchivo === 'ficha'">
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
                <input
                  type="url"
                  class="form-control"
                  v-model="enlaceArchivo"
                  id="fileUrl"
                  placeholder="https://drive.google.com/... o https://mediafire.com/..."
                />
              </div>

              <div class="form-actions d-flex justify-content-end">
                <button type="button" class="btn-edark" data-bs-dismiss="modal">
                  Cancelar
                </button>

                <button
                  type="button"
                  :disabled="!objetoCompleto"
                  @click="guardarArchivo"
                  class="btn-econf"
                >
                  Guardar
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
            <h5 class="modal-title">Actualizar Archivo</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body modal-files">
            <form @submit.prevent="guardarEdicion">
              <div id="fileForm" class="form-group">
                <label class="form-label">Tipo de Archivo</label>
                <select
                  class="form-control"
                  v-model="tipoEditar"
                  id="fileType"
                  required
                >
                  <option value="">Seleccionar tipo...</option>
                  <option value="audio">Audio</option>
                  <option value="video">Video</option>
                  <option value="ficha">Ficha Técnica</option>
                </select>
              </div>

              <!-- style="display: none;" -->
              <div class="form-group" v-if="tipoEditar === 'ficha'">
                <label class="form-label">Idioma</label>
                <select
                  class="form-control"
                  v-model="idiomaEditar"
                  id="language"
                >
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
                <input
                  type="url"
                  class="form-control"
                  v-model="urlEditar"
                  id="fileUrl"
                  placeholder="https://drive.google.com/... o https://mediafire.com/..."
                />
              </div>

              <div class="form-actions d-flex justify-content-end">
                <button type="button" class="btn-edark" data-bs-dismiss="modal">
                  Cancelar
                </button>

                <button
                  type="button"
                  :disabled="!objetoEditarCompleto"
                  @click="guardarEdicion"
                  class="btn-econf"
                >
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- modal confirmar eliminar -->
    <div
      class="modal fade"
      id="modalConfirmarEliminar"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar eliminación</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body mod-delete">
            ¿Estás seguro de que deseas eliminar este archivo?
          </div>
          <div class="form-actions d-flex justify-content-end">
            <button class="btn-edark" data-bs-dismiss="modal">Cancelar</button>

            <button class="btn-edelete" @click="confirmarEliminacion">
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- container toasts -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div
        id="successToast"
        class="toast"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="toast-header">
          <i class="bi bi-check-circle-fill text-success me-2"></i>
          <strong class="me-auto">Éxito</strong>
          <small class="text-body-secondary">Ahora</small>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="toast"
            aria-label="Close"
          ></button>
        </div>
        <div class="toast-body" id="toastMessage">
          <!-- mensaje -->
        </div>
      </div>
    </div>

    <!-- toasts de errores -->
    <div
      class="toast-container position-fixed top-0 end-0 p-3"
      style="z-index: 1060"
    >
      <div
        id="errorToast"
        class="toast"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="toast-header">
          <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
          <strong class="me-auto">Error</strong>
          <small class="text-body-secondary">Ahora</small>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="toast"
            aria-label="Close"
          ></button>
        </div>
        <div class="toast-body" id="errorToastMessage">
          <!-- mensaje de error -->
        </div>
      </div>
    </div>

    <!-- fin de toast -->
  </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from "vue";

import "bootstrap/dist/js/bootstrap.bundle.min.js";

const lasCampanas = ref([]);

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

const tipoEditar = ref("");
const idiomaEditar = ref("");
const urlEditar = ref("");
const idEditar = ref("");

// modal de carga
function agregarArchivo() {
  const modal = new bootstrap.Modal(document.getElementById("modalArchivo"));
  modal.show();
}
// modal de update
function editarArchivo(archivo) {
  tipoEditar.value = archivo.tipo;
  idiomaEditar.value = archivo.idioma;
  urlEditar.value = archivo.url;
  idEditar.value = archivo.id;

  const modal = new bootstrap.Modal(document.getElementById("modalEditar"));
  modal.show();
}

function guardarEdicion() {
  const actualizado = {
    id: idEditar.value,
    tipo: tipoEditar.value,
    idioma: tipoEditar.value === "ficha" ? idiomaEditar.value : null,
    url: urlEditar.value,
  };

  fetch("http://localhost/prom_system/api/update-archivo.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(actualizado),
  })
    .then((r) => r.json())
    .then((data) => {
      obtenerArchivos();
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("modalEditar")
      );
      modal.hide();

      mostrarToast("Archivo actualizado con éxito");
    })
    .catch((err) => {
      console.error("Error al actualizar", err);
      mostrarToastError("Error al actualizar el archivo");
    });
}
//fin de modo editar

//delete archivo

const idPendienteEliminar = ref(null);

function abrirModalEliminar(id) {
  idPendienteEliminar.value = id;
  const modal = new bootstrap.Modal(
    document.getElementById("modalConfirmarEliminar")
  );
  modal.show();
}

function confirmarEliminacion() {
  eliminarArchivo(idPendienteEliminar.value);
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("modalConfirmarEliminar")
  );
  modal.hide();

  mostrarToast("Archivo eliminado con éxito");
}

function eliminarArchivo(id) {
  fetch("http://localhost/prom_system/api/delete-archivo.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((r) => r.json())
    .then((data) => {
      console.log("Respuesta:", data);

      // verificar si el backend indica éxito o error
      if (data.error || data.success === false) {
        throw new Error(data.message || "Error al eliminar");
      }

      obtenerArchivos();
      mostrarToast("Archivo eliminado con éxito");
    })
    .catch((err) => {
      console.error("Error al eliminar:", err);
      mostrarToastError("Error al eliminar el archivo");
    });
}

onMounted(() => {
  fetch("http://localhost/prom_system/api/conex-campanas.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error al traer campañas: " + response.status);
      }
      return response.json();
    })
    .then((json) => {
      console.log("Aqui las campañas cargadas: ", json);
      lasCampanas.value = json;
      // idSeleccionado.value = "56";
    })
    .catch((err) => {
      console.error("Error en la respuesta de campañas: ", err);
    });
});

watch(idSeleccionado, (nuevoId) => {
  const campana = lasCampanas.value.find((c) => c.id == nuevoId);
  if (campana) {
    nombreArtista.value = campana.artista;
    nombreLanzamiento.value = campana.nombre_lanzamiento;
    nombreTipoLanzamiento.value = campana.tipo_de_lanzamiento;
    nombreGenero.value = campana.music_genre;
    nombreFirma.value = campana.sender_name || "Dr Yvan";
  }
});

function construirObjeto() {
  if (!idSeleccionado.value || !tipoArchivo.value || !enlaceArchivo.value)
    return;
  if (tipoArchivo.value === "ficha" && !idioma.value) return;

  return {
    campana_id: idSeleccionado.value,
    tipo: tipoArchivo.value,
    idioma: tipoArchivo.value === "ficha" ? idioma.value : null,
    url: enlaceArchivo.value,
  };
}

/* ACTUALIZAR TABLA */
function obtenerArchivos() {
  fetch("http://localhost/prom_system/api/conex-archivos.php")
    .then((r) => r.json())
    .then((json) => (losArchivos.value = json));
}

function guardarArchivo() {
  const archivo = construirObjeto();
  if (!archivo) return;

  fetch("http://localhost/prom_system/api/enviar-archivos.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(archivo),
  })
    .then((res) =>
      res.ok ? res.json() : Promise.reject("Error en l respuesta")
    )
    .then((data) => {
      // console.log('Respuesta del backend:', data);
      obtenerArchivos();
      losArchivos.value.push({
        ...data,
        campaña_id: data.campana_id,
      });

      //  ejecutar esto si todo salió bien
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("modalArchivo")
      );
      modal.hide();

      tipoArchivo.value = "";
      idioma.value = "";
      enlaceArchivo.value = "";

      mostrarToast("Archivo agregado con éxito");
    })
    .catch((err) => {
      console.error("Error al guardar", err);
      mostrarToastError("Error al guardar el archivo");
    });

  console.log("Objeto listo para enviar:", archivo);
}

const objetoCompleto = computed(() => {
  return (
    idSeleccionado.value &&
    tipoArchivo.value &&
    enlaceArchivo.value &&
    (tipoArchivo.value !== "ficha" || idioma.value)
  );
});

const objetoEditarCompleto = computed(() => {
  return (
    tipoEditar.value &&
    urlEditar.value &&
    (tipoEditar.value !== "ficha" || idiomaEditar.value)
  );
});

// TABLA DE ARCHIVOS

onMounted(() => {
  fetch("http://localhost/prom_system/api/conex-archivos.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error al traer campañas: " + response.status);
      }
      return response.json();
    })
    .then((json) => {
      console.log("Aqui los archivos cargadas: ", json);
      losArchivos.value = json;
    })
    .catch((err) => {
      console.error("Error en la respuesta de archivos: ", err);
    });
});

const archivosFiltrados = computed(() => {
  return losArchivos.value.filter(
    (a) => a["campaña_id"] == idSeleccionado.value
  );
});

/*  toasts */

function mostrarToast(mensaje) {
  // actualizar mensaje
  document.getElementById("toastMessage").textContent = mensaje;

  const toastElement = document.getElementById("successToast");
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: 800,
  });

  toast.show();
}

function mostrarToastError(mensaje) {
  // actualizar mensaje
  document.getElementById("errorToastMessage").textContent = mensaje;

  const toastElement = document.getElementById("errorToast");
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: 5000,
  });

  toast.show();
}
</script>

<style scoped>
.container-archivos {
  max-width: 98% !important;
  /* padding: 50px 10px 10px; */
  border-radius: 25px;
  box-shadow: 2px -4px 12px var(--medium-echo);
  margin: auto !important;
  padding: auto !important;
  box-sizing: border-box;
}

.info-box {
  /* border: 1px solid red; */
  padding-left: 0.5rem;
}

.info-box span {
  color: var(--light-echo);
}

#modalArchivo,
#modalEditar,
#modalConfirmarEliminar {
  backdrop-filter: blur(2px) saturate(130%);
  background-color: rgba(8, 47, 56, 0.322);
}

#modalConfirmarEliminar {
  padding-inline: 0;
}

#modalConfirmarEliminar .form-actions {
  padding-right: 0.5rem;
}

#modalConfirmarEliminar .text-dark {
  /* padding-inline: 1rem; */
  padding-bottom: 1rem;
}

#modalArchivo input,
select {
  background-color: var(--dark-echo);
  color: var(--bright-echo);
}

#modalArchivo input:focus,
#modalArchivo select:focus {
  outline: none;
  border: 1px solid var(--confirm-echo);
  background-color: var(--dark-echo);
  color: var(--bright-echo);
  box-shadow: 0 0 0 2px rgba(100, 200, 255, 0.2);
}

#modalEditar input,
select {
  background-color: var(--dark-echo);
  color: var(--bright-echo);
}

#modalEditar input:focus,
#modalEditar select:focus {
  outline: none;
  border: 1px solid var(--confirm-echo);
  background-color: var(--dark-echo);
  color: var(--bright-echo);
  box-shadow: 0 0 0 2px rgba(100, 200, 255, 0.2);
}

.div-selector {
  padding: 1rem 2rem;
  border-radius: 20px;
  box-shadow: 4px 4px 18px var(--dark-echo);
  transition: background 1s ease;
}

.tit-select {
  background: linear-gradient(
    135deg,
    var(--dark-echo) 65%,
    var(--medium-echo) 100%
  );
  box-shadow: 4px 4px 18px rgba(55, 77, 95, 0.4),
    inset 0 1px 0 rgba(203, 213, 223, 0.1);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  position: relative;
  overflow: hidden;
}

.tit-select::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(203, 213, 223, 0.1),
    transparent
  );
  transition: left 0.6s ease;
}

.tit-select:hover {
  background: linear-gradient(
    135deg,
    var(--dark-echo) 45%,
    var(--medium-echo) 85%,
    var(--light-echo) 100%
  );
  box-shadow: 6px 6px 25px rgba(55, 77, 95, 0.6),
    inset 0 1px 0 rgba(203, 213, 223, 0.2), 0 0 20px rgba(111, 139, 162, 0.3);
  /* transform: translateY(-2px); */
}

.tit-select:hover::before {
  left: 100%;
}

label {
  color: var(--light-echo);
  font-size: 1.1rem;
}

.div-selector select {
  background-color: var(--dark-echo);
  margin-block: 0.5rem;
  color: var(--light-echo);
  border: 1px solid var(--medium-echo);
  font-size: 1rem;
  border-radius: 8px;
}

.div-selector select:focus {
  background-color: var(--dark-echo);
  color: var(--bright-echo);
  box-shadow: 0px 0px 4px var(--light-echo);
  border: none;
}

.los-archivos {
  background: var(--light-echo);
  border: 1px solid var(--dark-echo);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.los-archivos:hover {
  box-shadow: 6px 6px 25px rgba(55, 77, 95, 0.6),
    inset 0 1px 0 rgba(203, 213, 223, 0.2), 0 0 20px rgba(111, 139, 162, 0.3);
  transform: translateY(-2px);
}

.los-archivos table {
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 2px -4px 12px var(--dark-echo);
  max-width: 100%;
}

.los-archivos label {
  color: var(--dark-echo);
  font-size: 1.2rem;
}

tr {
  border-bottom: 0.1rem solid var(--medium-echo);
  background-color: var(--dark-echo);
  color: var(--light-echo);
}

td {
  background: none;
  color: var(--light-echo);
}

a {
  color: var(--bright-echo);
  text-decoration: none;
}

/* modal body */
.text-dark {
  background-color: var(--dark-echo);
}

.cont-button {
  box-sizing: border-box !important;
  margin-top: 0;
  position: relative !important;
}

body.modal-open {
  overflow-x: hidden !important;
}

.form-actions,
.modal-footer {
  margin-top: 1rem;
}

.modal-footer {
  padding-right: 1rem;
}

.mod-delete {
  background-color: var(--light-echo);
}

/* responsive */

@media (min-width: 576px) {
  .select-files {
    width: 50% !important;
  }

  .info-box {
    width: 50% !important;
  }
}
</style>
