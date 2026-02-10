<template>
  <div class="debug-toggle" @click="toggleDebugMenu">
    <i class="fas fa-cog"></i>
  </div>

  <div class="menu-debug" :class="{ show: showDebugMenu }">
    <button class="btn" @click="cargarEmails" :disabled="!campaniaActiva">
      <i class="fas fa-download"></i> Cargar emails
    </button>

    <div v-if="campaniaActiva && ultimaActualizacion" class="ultima-act">
      <i class="fas fa-clock"></i>
      <span>{{ ultimaActualizacionFormato }}</span>
    </div>

    <button class="btn" @click="iaUpdate()">
      <i class="fas fa-wand-magic-sparkles"></i> Análisis IA
    </button>
    <button class="btn" @click="responder()">
      <i class="fas fa-share"></i> Responder todos
    </button>
    <button class="btn" @click="resetEstado()">
      <i class="fas fa-trash-alt"></i> Limpiar Estado
    </button>
  </div>

  <div class="container-fluid mt-4">
    <div class="row">
      <!-- menu lateral -->
      <div class="col-md-3">
        <div class="d-flex flex-column gap-2 columCards">
          <template v-for="c in campañasEnviadas" :key="c.id">
            <div
              class="card w-100 card-btn"
              :class="{ 'card-active': campaniaActiva === c.id }"
              v-if="c.activa !== '2'"
              @click="abrirBandeja(c.id)"
            >
              <div class="card-body ca-ctb">
                <h5 class="card-title">{{ c.nombre_lanzamiento }}</h5>
                <p class="card-text">
                  <small class="text-body-secondary name-type"
                    >{{ c.artista }} - {{ c.tipo_de_lanzamiento }}</small
                  >
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
          <div class="d-flex gap-1 align-items-center">
            <button class="btn btn-echo btn-bande">Todos</button>
            <button class="btn btn-echo">Revisión</button>
            <button class="btn btn-echo">Programados</button>
            <button class="btn btn-echo">Respondidos</button>

            <div class="ms-auto">
              <input
                type="text"
                class="form-control form-control-sm searcher-echo"
                placeholder="Buscar..."
                style="width: 250px"
              />
            </div>
          </div>
        </div>

        <!-- bandeja -->
        <div class="card div-bandeja">
          <div class="card-body p-0">
            <!-- MOSTRAR ANIMACIÓN CUANDO ESTÁ CARGANDO -->
            <LoadingEmails v-if="isLoadingEmails" />

            <!-- mostrar emails -->
            <div v-else class="list-group list-group-flush scroll-echo">
              <div
                v-for="mensaje in mensajesRecibidos"
                :key="mensaje.id_contacto"
                class="list-group-item d-flex justify-content-between align-items-start py-3 card-message"
              >
                <div
                  class="d-flex w-100 div-inbox div_msg"
                  @click="abrirMensaje(mensaje)"
                >
                  <div class="flex-grow-1">
                    <div
                      class="d-flex justify-content-between align-items-start mb-1"
                    >
                      <h6 class="mb-0">{{ mensaje.nombre }}</h6>
                      <small class="text-muted text-fecha">{{
                        tiempoRelativo(mensaje.fecha)
                      }}</small>
                    </div>
                    <p class="mb-1 fw-bold custom-dark">{{ mensaje.asunto }}</p>
                    <p
                      class="mb-0 text-muted custom-muted mensaje-bandeja"
                      v-html="mensaje.mensaje"
                    ></p>
                    <div
                      class="d-flex justify-content-between align-items-center mt-2"
                    >
                      <small class="text custom-dark">{{
                        mensaje.idioma
                      }}</small>

                      <span
                        v-if="mensaje.estado === null"
                        class="badge estado pendiente"
                      >
                        <i class="fas fa-hourglass-half me-1"></i> PENDIENTE
                      </span>

                      <span
                        v-else-if="mensaje.estado === 0"
                        class="badge estado programado"
                      >
                        <i class="fas fa-clock me-1"></i> PROGRAMADO
                      </span>

                      <span
                        v-else-if="mensaje.estado === 1"
                        class="badge estado respondido"
                      >
                        <i class="fas fa-reply me-1"></i> RESPONDIDO
                      </span>

                      <span
                        v-else-if="mensaje.estado === 2"
                        class="badge estado requiere-revision"
                      >
                        <i class="fas fa-search me-1"></i> REQUIERE REVISIÓN
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- mensaje cuando no hay email y esta cargando -->
              <div
                v-if="mensajesRecibidos.length === 0"
                class="text-center p-5 text-muted bandeja-vacia"
              >
                <i class="fas fa-inbox fa-3x mb-3 inbox-icon"></i>
                <h5>Selecciona una campaña</h5>
                <p>
                  Elige una campaña del menú lateral para ver los emails
                  recibidos
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal para detalle del mensaje -->
  <div
    class="modal fade inbox"
    id="mensajeProg"
    tabindex="-1"
    aria-labelledby="mensajeProgLabel"
    aria-hidden="true"
  >
    <div
      class="modal-dialog modal-dialog-centered modal-lg"
      style="max-height: 90vh; display: flex; flex-direction: column"
    >
      <div
        class="modal-content modal-content-echo"
        style="display: flex; flex-direction: column; max-height: 100%"
      >
        <!-- header buttons -->
        <div class="modal-header" style="flex-shrink: 0">
          <div>
            <h5 class="modal-title" id="mensajeProgLabel">
              Asunto: {{ mensajeSeleccionado?.asunto || "" }}
            </h5>
            <div class="">De: {{ mensajeSeleccionado?.nombre || "" }}</div>
          </div>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>

        <!-- contenido -->
        <div class="scroll-echo" style="flex: 1; overflow-y: auto">
          <div class="modal-body">
            <div v-if="mensajeSeleccionado">
              <hr />
              <div class="mb-3">
                <strong>Mensaje:</strong>
                <div
                  class="mt-2 p-3 bg-light rounded"
                  v-html="mensajeSeleccionado.mensaje"
                ></div>
              </div>
            </div>
            <div v-else>
              <p class="text-center text-muted">
                Cargando información del mensaje...
              </p>
            </div>
          </div>

          <!-- Vista previa del mensaje completo -->
          <div v-if="programando" class="respuesta p-3 border-top">
            <div class="mb-3">
              <label class="form-label fw-bold"
                >Vista previa del mensaje:</label
              >
              <div class="mensaje-preview p-3 bg-light rounded border">
                <!-- Asunto -->
                <div class="mb-3">
                  <strong>Asunto:</strong> {{ asuntoRespuesta || "Sin asunto" }}
                </div>
                <hr />
                <!-- Mensaje con enlaces integrados -->
                <div
                  style="white-space: pre-wrap"
                  v-html="mensajeCompleto"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- FOOTER FIJO -->
        <div class="modal-footer" style="flex-shrink: 0">
          <div class="d-flex me-auto div-toggle">
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                type="checkbox"
                v-model="programando"
                id="checkNativeSwitch"
                switch
              />
              <label class="form-check-label" for="checkNativeSwitch">
                Programar
              </label>
            </div>

            <div class="btn-link">
              <button
                class="btn btn-eclose"
                :disabled="!programando"
                @click="insertarArchivo('audio')"
              >
                Audio
              </button>
              <button
                class="btn btn-eclose"
                :disabled="!programando"
                @click="insertarArchivo('ficha')"
              >
                Rider
              </button>
              <button
                class="btn btn-eclose"
                :disabled="!programando"
                @click="insertarArchivo('video')"
              >
                Vídeo
              </button>
            </div>
          </div>

          <button type="button" class="btn btn-eclose" data-bs-dismiss="modal">
            Cerrar
          </button>
          <button
            type="button"
            class="btn btn-econf"
            v-if="mensajeSeleccionado"
            @click="enviarRespuesta"
          >
            Programar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- modal confirmacion analizar con ia -->
  <div
    class="modal fade"
    id="iaModal"
    tabindex="-1"
    aria-labelledby="iaModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="iaModalLabel">Confirmar análisis IA</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            ¿Estás seguro que deseas analizar los emails con IA? Este proceso
            puede tomar varios minutos.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-eclose" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button
            type="button"
            class="btn btn-econf"
            @click="ejecutarAnalisisIA"
          >
            Analizar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- modal confirmacion responder a toda la campaña -->
  <div
    class="modal fade"
    id="responderTodos"
    tabindex="-1"
    aria-labelledby="responderTodosLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="responderTodosLabel">
            Confirmar respuestas
          </h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            ¿Estás seguro que deseas responder todos los emails de esta campaña?
            Este proceso puede tomar varios minutos.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-eclose" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button
            type="button"
            class="btn btn-econf"
            @click="ejecutarRespuestas"
          >
            Responder
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- modal confirmacion resetear analisis -->
  <div
    class="modal fade"
    id="resetear"
    tabindex="-1"
    aria-labelledby="resetearLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resetearLabel">Resetear análisis</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>
            ¿Estás seguro que deseas resetear todos los análisis de esta
            campaña?
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-eclose" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="button" class="btn btn-econf" @click="ejecutarReset">
            Resetear
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- modal aviso, no hay emails analizadoss -->
  <div
    class="modal fade"
    id="noEmails"
    tabindex="-1"
    aria-labelledby="noEmailsLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="noEmailsLabel">No hay emails</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p>No hay emails analizados para enviar respuestas</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-eclose" data-bs-dismiss="modal">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- overlay de carga para respuestas -->

  <transition name="overlay">
    <div
      v-if="mostrandoOverlay"
      class="overlay-loading d-flex justify-content-center align-items-center"
    >
      <div
        class="card text-center p-4 shadow-lg text-light rounded-4 container-overlay"
      >
        <!-- spinner grande -->
        <div class="mb-3">
          <div
            class="spinner-grow text-light"
            style="width: 4rem; height: 4rem"
            role="status"
          >
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>

        <!-- texto principal -->
        <h5 class="mb-2">{{ textoOverlay }}</h5>

        <!-- subtítulo -->
        <small class="text-secondary subtitulo"
          >Esto puede tardar varios minutos...</small
        >
      </div>
    </div>
  </transition>

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
</template>

<script setup>
import { ref, onMounted, watch, nextTick, computed } from "vue";
import LoadingEmails from "./LoadingEmails.vue";

const campañasEnviadas = ref([]);
const mensajesRecibidos = ref([]);
const isLoadingEmails = ref(false);
const campaniaActiva = ref(null);
const mensajeSeleccionado = ref(null);

// Estados para el overlay
const mostrandoOverlay = ref(false);
const textoOverlay = ref("");

// Funciones helper
const mostrarOverlay = (texto = "Procesando...") => {
  textoOverlay.value = texto;
  mostrandoOverlay.value = true;
};

const ocultarOverlay = () => {
  mostrandoOverlay.value = false;
};

const idiomaMap = {
  French: "fr",
  Spanish: "esp",
  English: "en",
  Korean: "ko",
  Russian: "ru",
  Ukrainian: "uk",
  Japanese: "jp",
};

const programando = ref(false);
const asuntoRespuesta = ref("");
const mensajeRespuesta = ref("");
const despedidaRespuesta = ref("");

const enlacesActivos = ref({
  audio: false,
  video: false,
  ficha: false,
});

const archivosSeleccionados = ref({
  id_respuesta: null,
  audio: 0,
  video: 0,
  ficha: 0,
});

const mensajeCompleto = computed(() => {
  let mensaje = mensajeRespuesta.value || "";

  const enlaces = [];

  if (enlacesActivos.value.audio && obtenerArchivos("audio")) {
    enlaces.push(
      `<a href="${obtenerArchivos("audio").url}" target="_blank">${
        obtenerArchivos("audio").url
      }</a>`
    );
  }

  if (enlacesActivos.value.video && obtenerArchivos("video")) {
    enlaces.push(
      `<a href="${obtenerArchivos("video").url}" target="_blank">${
        obtenerArchivos("video").url
      }</a>`
    );
  }

  if (enlacesActivos.value.ficha && obtenerArchivos("ficha")) {
    enlaces.push(
      `<a href="${obtenerArchivos("ficha").url}" target="_blank">${
        obtenerArchivos("ficha").url
      }</a>`
    );
  }

  if (enlaces.length > 0) {
    mensaje += "\n\n" + enlaces.join("\n");
  }

  return mensaje;
});

const obtenerArchivos = (tipo) => {
  if (!mensajeSeleccionado.value || !campaniaActiva.value) return null;

  let archivosFiltrados = archivos.value.filter(
    (archivo) =>
      archivo.campaña_id === campaniaActiva.value.toString() &&
      archivo.tipo === tipo
  );

  if (tipo === "ficha" && mensajeSeleccionado.value.idioma) {
    const idiomaCodigo =
      idiomaMap[mensajeSeleccionado.value.idioma] ||
      mensajeSeleccionado.value.idioma;
    archivosFiltrados = archivosFiltrados.filter(
      (archivo) => archivo.idioma === idiomaCodigo
    );
  }

  return archivosFiltrados.length > 0 ? archivosFiltrados[0] : null;
};

const insertarArchivo = (tipo) => {
  enlacesActivos.value[tipo] = !enlacesActivos.value[tipo];

  const archivo = obtenerArchivos(tipo);
  if (enlacesActivos.value[tipo] && !archivo) {
    console.warn(
      `No se encontró archivo de tipo ${tipo} para esta campaña/idioma`
    );
    enlacesActivos.value[tipo] = false;
    archivosSeleccionados.value[tipo] = 0;
    return;
  }

  archivosSeleccionados.value[tipo] = enlacesActivos.value[tipo] ? 1 : 0;

  console.log(
    `${tipo} ${enlacesActivos.value[tipo] ? "activado" : "desactivado"}`
  );
  console.log("Objeto para DB:", archivosSeleccionados.value);
};

watch(mensajeSeleccionado, (nuevoMensaje) => {
  enlacesActivos.value = {
    audio: false,
    video: false,
    ficha: false,
  };

  archivosSeleccionados.value = {
    id_respuesta: nuevoMensaje.id,
    audio: 0,
    video: 0,
    ficha: 0,
  };

  if (nuevoMensaje) {
    if (nuevoMensaje.estado === 0) {
      programando.value = true;

      enlacesActivos.value.audio = nuevoMensaje.inc_audio == 1;
      enlacesActivos.value.video = nuevoMensaje.inc_video == 1;
      enlacesActivos.value.ficha = nuevoMensaje.inc_ficha == 1;

      archivosSeleccionados.value.audio = nuevoMensaje.inc_audio || 0;
      archivosSeleccionados.value.video = nuevoMensaje.inc_video || 0;
      archivosSeleccionados.value.ficha = nuevoMensaje.inc_ficha || 0;
    } else {
      programando.value = false;
    }

    if (nuevoMensaje.idioma) {
      const idiomaCodigo =
        idiomaMap[nuevoMensaje.idioma] || nuevoMensaje.idioma;

      const plantilla = respuestas_plantillas.value.find(
        (p) => p.idioma === idiomaCodigo
      );

      if (plantilla) {
        asuntoRespuesta.value = `${plantilla.prefijo} ${nuevoMensaje.asunto}`;
        mensajeRespuesta.value = `${plantilla.respuesta || ""}\n\n${
          plantilla.despedida || ""
        }`;
        despedidaRespuesta.value = plantilla.despedida || "";
      } else {
        asuntoRespuesta.value = nuevoMensaje.asunto || "";
      }
    }
  }
});

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/es";

dayjs.extend(relativeTime);
dayjs.locale("es");

function tiempoRelativo(fechaOriginal) {
  return dayjs(fechaOriginal).fromNow();
}

const respuestas_plantillas = ref([]);
const archivos = ref([]);

onMounted(() => {
  console.log("2do componente montado");

  fetch("http://localhost/prom_system/api/conex-campanas.php")
    .then((res) => res.json())
    .then((json) => {
      campañasEnviadas.value = json;
      console.log("campEnviadas cargadas:", json);
    });

  fetch("http://localhost/prom_system/api/respuestas_plantillas.php")
    .then((res) => res.json())
    .then((json) => {
      respuestas_plantillas.value = json;
      console.log("respuestas plantillas cargadas:", json);
    });

  fetch("http://localhost/prom_system/api/conex-archivos.php")
    .then((res) => res.json())
    .then((json) => {
      archivos.value = json;
      console.log("archivos cargados:", json);
    });
});

function resetEstado() {
  if (!campaniaActiva.value) {
    alert("No hay campaña seleccionada aún");
    return;
  }

  // Mostrar modal
  const modal = new bootstrap.Modal(document.getElementById("resetear"));
  modal.show();
}

function ejecutarReset() {
  const idCampaña = Number(campaniaActiva.value);
  console.log("ID campaña a enviar:", idCampaña);

  // Cerrar el modal primero
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("resetear")
  );
  modal.hide();

  fetch("http://localhost/prom_system/api/reset_analisis.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_campaña: idCampaña }),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("Respuesta del servidor:", data);
      if (data.ok) {
        mostrarToast(`Estado reiniciado exitosamente ✓ `);
        abrirBandeja(campaniaActiva.value);
      } else {
        mostrarToastError("Error: " + (data.error || "No se pudo reiniciar"));
      }
    })
    .catch((err) => {
      console.error("Error en fetch:", err);
      mostrarToastError("Error de conexión");
    });
}

function iaUpdate() {
  if (!campaniaActiva.value) {
    alert("No hay campaña seleccionada aún");
    return;
  }

  // Mostrar modal
  const modal = new bootstrap.Modal(document.getElementById("iaModal"));
  modal.show();
}

function ejecutarAnalisisIA() {
  const idCampaña = Number(campaniaActiva.value);
  console.log("ID campaña a enviar:", idCampaña);

  const modal = bootstrap.Modal.getInstance(document.getElementById("iaModal"));
  modal.hide();

  // 👉 Mostrar overlay con texto específico
  mostrarOverlay("Procesando análisis IA...");

  fetch(
    `http://localhost/prom_system/api/ia_update.php?id_campana=${idCampaña}`
  )
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.text();
    })
    .then((data) => {
      console.log("Respuesta del servidor:", data);

      if (data.includes("PROCESO COMPLETADO") || data.includes("✅")) {
        mostrarToast("Análisis completado exitosamente ✓");
        abrirBandeja(campaniaActiva.value);
      } else if (data.includes("Error:") || data.includes("❌")) {
        mostrarToastError("Hubo errores durante el proceso");
      } else {
        mostrarToast("Proceso ejecutado");
      }
    })
    .catch((err) => {
      console.error("Error en fetch:", err);
      mostrarToastError("Error de conexión o en el servidor");
    })
    .finally(() => {
      // 👉 Ocultar overlay
      ocultarOverlay();
    });
}

function responder() {
  if (!campaniaActiva.value) {
    alert("No hay campaña seleccionada aún");
    return;
  }

  // Mostrar el modal de confirmación
  const modal = new bootstrap.Modal(document.getElementById("responderTodos"));
  modal.show();
}

function ejecutarRespuestas() {
  const idCampaña = Number(campaniaActiva.value);
  console.log("ID campaña a responder:", idCampaña);

  // Cerrar el modal primero
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("responderTodos")
  );
  modal.hide();

  // 👉 Mostrar overlay reactivo
  mostrarOverlay("Respondiendo emails...");

  fetch(
    `http://localhost/prom_system/api/responder_campana.php?id_campana=${idCampaña}`
  )
    .then((res) => {
      if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
      return res.json();
    })
    .then((data) => {
      console.log("Respuesta del servidor:", data);

      if (!data.error) {
        mostrarToast("Emails respondidos exitosamente ✓");
        abrirBandeja(campaniaActiva.value);
      } else {
        mostrarToastError(
          "Error: " + (data.message || "No se pudo responder la campaña")
        );
      }
    })
    .catch((err) => {
      console.error("Error en fetch:", err);
      mostrarToastError("Error de conexión o en el servidor");
    })
    .finally(() => {
      // 👉 Ocultar overlay reactivo
      ocultarOverlay();
    });
}

const abrirBandeja = (id) => {
  campaniaActiva.value = id;

  console.log("Iniciando carga de emails, isLoadingEmails:", true);
  isLoadingEmails.value = true;

  const campana = campañasEnviadas.value.find((c) => c.id === id.toString());

  mensajesRecibidos.value = [];

  fetch("http://localhost/prom_system/api/tabla_bandeja.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(campana),
  })
    .then((res) => res.json())
    .then((res) => {
      console.log("Respuesta desde PHP:", res);
      console.log("Finalizando carga, isLoadingEmails:", false);

      mensajesRecibidos.value = Array.isArray(res) ? res : [];
      isLoadingEmails.value = false;
    })
    .catch((err) => {
      console.error("Error al obtener los emails:", err);
      console.log("Error - ocultando loading, isLoadingEmails:", false);

      mensajesRecibidos.value = [];
      isLoadingEmails.value = false;
    });
};

const abrirMensaje = (mensaje) => {
  console.log("Abrir mensaje:", mensaje);

  mensajeSeleccionado.value = mensaje;

  const modalElement = document.getElementById("mensajeProg");
  if (modalElement) {
    const modalMsg = new bootstrap.Modal(modalElement);
    modalMsg.show();
  }
};

const enviarRespuesta = () => {
  console.log("Enviando respuesta:", archivosSeleccionados.value);

  fetch("http://localhost/prom_system/api/update_bandeja.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(archivosSeleccionados.value),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("Respuesta del servidor:", data);
      if (data.ok) {
        mostrarToast("Respuesta programada correctamente ✓");
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("mensajeProg")
        );
        modal.hide();
        // Actualizar la bandeja para reflejar los cambios
        abrirBandeja(campaniaActiva.value);
      } else {
        mostrarToastError(
          "Error: " + (data.error || "No se pudo programar la respuesta")
        );
      }
    })
    .catch((err) => {
      console.error("Error al enviar:", err);
      mostrarToastError("Error de conexión");
    });
};

/* toggle menu debug */
const showDebugMenu = ref(false);

const toggleDebugMenu = () => {
  showDebugMenu.value = !showDebugMenu.value;
};

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

// actualiozar bandeja seleccionada
</script>

<style scoped>
@import "../assets/echo-style.css";

.card-active {
  background: var(--dark-echo) !important;
  border-left: 10px solid var(--medium-echo) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2), 0 0 10px var(--medium-echo),
    inset 0 0 20px rgba(255, 255, 255, 0.1) !important;
}

.card-active .card-title {
  color: var(--light-echo) !important;
  font-weight: 600;
}

.card-active .name-type {
  color: var(--bright-echo) !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.card-active:hover {
  background: var(--dark-echo) !important;
  transform: translateY(-2px);
  border-left: 10px solid var(--medium-echo) !important;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3), 0 0 15px var(--medium-echo),
    inset 0 0 25px rgba(255, 255, 255, 0.15) !important;
}

.card-btn {
  background: linear-gradient(120deg, var(--bright-echo), var(--light-echo));
  background-size: 200% 200%;
  border-left: 10px solid var(--dark-echo);
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
  margin-bottom: -4px;
  transition: all 0.4s ease;
  position: relative;
}

.card-btn:hover {
  background-position: right center;
  box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.06),
    0 2px 8px rgba(0, 0, 0, 0.15);
  border-left: 10px solid var(--medium-echo);
  transform: translateY(-1px);
}

/* ocultar ratio */
input[type="radio"] {
  display: none !important;
}

.card-active::after {
  content: "";
  position: absolute;
  top: 10px;
  right: 8px;
  bottom: 10px;
  width: 3px;
  background: linear-gradient(to bottom, var(--bright-echo), var(--light-echo));
  border-radius: 2px;
  box-shadow: 0 0 8px rgba(255, 255, 255, 0.6);
}

/* scroll */
.columCards {
  height: 74vh;
  overflow-y: auto;
  padding-inline: 4px;
  scrollbar-width: thin;
  scrollbar-color: var(--medium-echo) var(--dark-echo);
}

.columCards::-webkit-scrollbar {
  width: 10px;
}

.columCards::-webkit-scrollbar-track {
  background: var(--dark-echo);
  border-radius: 10px;
}

.columCards::-webkit-scrollbar-thumb {
  background: linear-gradient(var(--medium-echo), var(--light-echo));
  border-radius: 10px;
  border: 2px solid var(--dark-echo);
  box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.3);
}

.columCards::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(var(--light-echo), var(--medium-echo));
}

.ca-ctb {
  padding: 8px 16px;
}

.card-title {
  color: var(--dark-echo);
  margin: 0;
}

p.card-text small.name-type {
  color: var(--medium-echo) !important;
  font-weight: 500;
}

/* bandeja */
.mensaje-bandeja {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.div_msg {
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.inbox-icon {
  color: var(--dark-echo);
}

.card-message {
  background: var(--bright-echo) !important;
}

.card-message h6 {
  color: var(--medium-echo);
}

.text-fecha {
  color: var(--medium-echo);
}

.custom-dark {
  color: var(--dark-echo) !important;
}

.custom-muted {
  color: var(--medium-echo) !important;
}

.programado {
  background: var(--confirm-echo);
  color: var(--light-echo);
}

.requiere-revision {
  background: var(--cta-echo);
  color: var(--bright-echo);
}

.respondido {
  background: var(--medium-echo);
  color: var(--bright-echo);
}

.pendiente {
  background: var(--alert-echo);
  color: var(--dark-echo);
}

/* modal */

.inbox {
  backdrop-filter: blur(2px) saturate(130%);
  background-color: rgba(8, 47, 56, 0.322);
}

.div-toggle {
  background: none;
  border: none;
  border-radius: 12px;
  padding: 10px 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 25px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.div-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Switch delgado y extenso */
.form-check-input {
  width: 3.2rem !important;
  height: 1rem !important;
  background-color: #e9ecef !important;
  border: 1px solid #dee2e6 !important;
  transition: all 0.3s ease !important;
  margin: 0 !important;
}

.form-check-input:checked {
  background-color: var(--confirm-echo) !important;
  border-color: var(--confirm-echo) !important;
}

/* Switch perfectamente centrado ARRIBA del texto */
.form-switch {
  padding: 8px;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px;
  text-align: center;
}

.form-check-label {
  font-weight: 500;
  font-size: 0.9rem;
  margin: 0 !important;
  padding: 0 !important;
  text-align: center;
  line-height: 1;
  color: var(--bright-echo) !important;
  font-weight: 600 !important;
  font-size: 0.9rem !important;
  margin: 0 !important;
  text-align: center;
  line-height: 1;

  letter-spacing: 0.5px;
}

/* cntrar contenedor */
.form-check {
  margin: 0 !important;
  padding: 0 !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
}

.div-bandeja {
  /* border: 3px solid red; */
  max-height: 65vh;
}

/* scroll bandeja */
.scroll-echo {
  height: 65vh;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: var(--medium-echo) var(--dark-echo);
}

.scroll-echo::-webkit-scrollbar {
  width: 10px;
}

.scroll-echo::-webkit-scrollbar-track {
  background: var(--dark-echo);
  border-radius: 10px;
}

.scroll-echo::-webkit-scrollbar-thumb {
  background: linear-gradient(var(--medium-echo), var(--light-echo));
  border-radius: 10px;
  border: 2px solid var(--dark-echo);
  box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.3);
}

.scroll-echo::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(var(--light-echo), var(--medium-echo));
}

/* searcher */
.searcher-echo {
  background-color: var(--dark-echo);
  color: var(--light-echo);
  border: 1px solid var(--medium-echo);
  border-radius: 20px;
  padding: 10px 16px;
  transition: all 0.3s ease;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
}

.searcher-echo::placeholder {
  color: var(--medium-echo);
  opacity: 0.6;
}

.searcher-echo:focus {
  outline: none;
  background-color: var(--dark-echo);
  border-color: var(--light-echo);
  box-shadow: 0 0 0 2px var(--light-echo), 0 0 10px var(--medium-echo);
  color: var(--light-echo);
}

/* Estilos adicionales para el modal */
.modal-content-echo {
  background: var(--bright-echo);
  border: 2px solid var(--medium-echo);
}

.modal-header {
  background: var(--dark-echo);
  color: var(--light-echo);
  border-bottom: 2px solid var(--medium-echo);
}

.modal-title {
  color: var(--light-echo);
}

.btn-close {
  filter: invert(1);
}

.modal-body {
  background: var(--bright-echo);
  color: var(--dark-echo);
}

.modal-footer {
  background: var(--dark-echo);
  border-top: 1px solid var(--medium-echo);
}

.mensaje-preview {
  background: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
}

.mensaje-preview a {
  color: #0066cc;
  text-decoration: none;
}

.mensaje-preview a:hover {
  text-decoration: underline;
}

/* boton toggle */
.debug-toggle {
  position: fixed;
  top: 64px;
  right: 20px;
  width: 45px;
  height: 45px;
  background: var(--medium-echo);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1001;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.debug-toggle:hover {
  background: var(--dark-echo);
  transform: rotate(90deg);
}

/* Menú debug oculto */
.menu-debug {
  position: fixed;
  top: 64px;
  right: -420px;
  width: fit-content;
  background: var(--bright-echo);
  border-radius: 15px;
  padding: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* asomar menú */
.menu-debug.show {
  right: 80px;
}

.menu-debug .btn {
  background: var(--bright-echo);
  color: var(--dark-echo);
  border: 1px solid var(--medium-echo);
  padding: 4px 15px;
  margin-inline: 8px;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.menu-debug .btn:hover {
  background: var(--medium-echo);
  color: white;
}

/* bandeja */

.bandeja-vacia {
  position: relative;
  top: 50%;
  transform: translateY(-50%);
}

/* boton cargar */
.ultima-act {
  font-size: 0.7rem;
  color: var(--medium-echo);
  margin-top: 4px;
  margin-left: 8px;
  display: flex;
  align-items: center;
  gap: 4px;
  opacity: 0.8;
}

.ultima-act i {
  font-size: 0.65rem;
}
</style>
