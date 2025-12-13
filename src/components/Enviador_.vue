<template>
  <div class="container miContenedor">
    <h1 class="text-center principal">Gestor de Campañas</h1>
    <h5 class="text-center">
      Crea y administra tus campañas de email marketing.
    </h5>

    <br />

    <form action="">
      <div class="d-flex justify-content-center">
        <div class="row" data-toggle="buttons">
          <label
            class="col btn-campaña"
            :class="{ active: !showSelect }"
            @click="showSelect = false"
          >
            <input
              type="radio"
              name="options"
              id="option1"
              autocomplete="off"
            />
            <span class="icon-circle">
              <i
                class="fa fa-plus-circle ico-btn"
                aria-hidden="true"
              ></i> </span
            ><br />Crear campaña
            <p class="descripcion">Inicia una nueva campaña desde cero.</p>
          </label>

          <label
            class="col btn-campaña"
            :class="{ active: showSelect }"
            @click="showSelect = true"
          >
            <input
              type="radio"
              name="options"
              id="option2"
              autocomplete="off"
            />
            <i class="fa-solid fa-folder-open ico-btn"></i><br />Usar campaña
            existente
            <p class="descripcion">Continua con una campaña anterior.</p>
          </label>
        </div>
      </div>
      <!-- tabla para crear -->

      <div class="mb-3 datos-camp" v-if="!showSelect">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="campagneInput" class="form-label subti"
              >Nom de l'artiste</label
            >
            <input
              v-model="campagne"
              type="email"
              class="form-control"
              id="exampleFormControlInput1"
              placeholder="Nom de l'artiste"
            />
            <div class="text-danger small" v-if="nombreCampanaExiste">
              ⚠️ Ya existe una campaña con este nombre
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label subti">Type du lancement</label>
            <div class="input-group">
              <select
                v-model="lancement"
                class="form-select"
                style="max-width: 40%"
              >
                <option disabled value="">Type</option>
                <option value="Album">Album</option>
                <option value="Ep">Ep</option>
                <option value="Single">Single</option>
              </select>
              <input
                v-model="nomLancement"
                type="text"
                class="form-control"
                placeholder="Ou écris ton type"
              />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="lienInput" class="form-label subti">Lien</label>
            <input
              v-model="lien"
              type="email"
              class="form-control"
              id="exampleFormControlInput1"
              placeholder="lien"
            />
          </div>

          <div class="col-md-6 mb-3">
            <label for="genreSelect" class="form-label subti">Genre</label>
            <select
              v-model="genre"
              class="form-select"
              aria-label="Default select example"
            >
              <option disabled value="">Genre</option>
              <option value="Jazz">Jazz</option>
              <option value="Pop">Pop</option>
              <option value="Rock">Rock</option>
              <option value="World Music">World Music</option>
              <option value="Classical">Classical</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label subti">Utilisateur</label>

            <select class="form-select firma-select" v-model="signature">
              <option disabled value="" selected>Choisir un utilisateur</option>
              <option
                v-for="cuenta in enviadoresEmails"
                :key="cuenta.id"
                :value="cuenta.id"
              >
                {{ cuenta.sender_name }}
              </option>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label for="signatureInput" class="form-label subti">Pays</label>
            <input
              v-model="filtrarPorPais"
              type="text"
              class="form-control"
              id="paysInput"
              placeholder="Choisir un pays"
            />
          </div>
        </div>
      </div>

      <div class="mb-3 datos-camp" v-else>
        <div class="row camp-exist">
          <div class="col-md-6 mb-3 dato-exis">
            <label for="campagneSelect" class="form-label subti"
              >Elegir campaña existente</label
            >

            <select
              v-model="campagne"
              class="form-select"
              aria-label="Default select example"
            >
              <option value="" disabled selected>Selecciona una campaña</option>
              <option
                v-for="camp in campActivasEnviadas"
                :key="camp.id"
                :value="camp.id"
              >
                {{ camp.tipo_de_lanzamiento }} {{ camp.nombre_lanzamiento }}
              </option>
            </select>
          </div>

          <div class="mb-4 data-camp">
            <p>
              <span class="campo-nombre">Artista:</span><br />
              <span class="campo-valor">{{ selectedCamp?.artista || "" }}</span>
            </p>
          </div>

          <div class="mb-4 data-camp">
            <p>
              <span class="campo-nombre">Lien:</span><br />
              <span v-if="selectedCamp?.enlace" class="campo-valor">
                <a
                  class="link-camp"
                  :href="selectedCamp.enlace"
                  target="_blank"
                  rel="noopener"
                  >{{ selectedCamp.enlace }}</a
                >
              </span>
            </p>
          </div>

          <div class="mb-4 data-camp">
            <p>
              <span class="campo-nombre">Genre:</span><br />
              <span class="campo-valor">{{
                selectedCamp?.music_genre || ""
              }}</span>
            </p>
          </div>

          <div class="col-md-6 mb-3">
            <label for="signatureInput" class="form-label subti">Pays</label>
            <input
              v-model="filtrarPorPaisExistente"
              type="text"
              class="form-control"
              placeholder="Choisir un pays"
            />
          </div>
        </div>

        <br />

        <!-- div de boton de filtrar toggle -->

        <div v-if="lancement && mostrarTabla" class="text-end ms-auto">
          <div class="toggle-wrapper">
            <label class="toggle-switch">
              <span
                class="toggle-label"
                :class="{ 'active-green': !filtroYaEnviados }"
                >All</span
              >

              <input type="checkbox" v-model="filtroYaEnviados" />
              <span
                class="slider"
                :class="filtroYaEnviados ? 'red' : 'green'"
              ></span>

              <span
                class="toggle-label"
                :class="{ 'active-red': filtroYaEnviados }"
                >No enviados</span
              >
            </label>
          </div>
        </div>

        <!-- fin del toggle -->
      </div>

      <div class="btn-group" role="group" aria-label="...">
        <button
          @click="mostrarPrevisualizacion"
          class="btn btn-echo second"
          type="button"
          :disabled="!formularioCompleto && !selectedCamp"
        >
          <i class="fas fa-eye" aria-hidden="true"></i> Previsualizar Correos
        </button>

        <button
          id="btn-preview"
          class="btn btn-echo second"
          type="button"
          :disabled="!formularioCompleto && !selectedCamp"
          @click="mostrarTabla = true"
        >
          <i class="fas fa-table" aria-hidden="true"></i> Mostrar Contactos
        </button>

        <button
          class="btn btn-echo action"
          type="button"
          :disabled="!showPreview || deshabilitarChecks"
          @click="enviarCorreos"
        >
          <i class="fa fa-paper-plane"></i> Enviar Seleccionados
        </button>
      </div>
    </form>

    <!-- <div class="debug-form small text-muted mt-2">
  campagne: {{ campagne }} |
  lancement: {{ lancement }} |
  nomLancement: {{ nomLancement }} |
  signature: {{ signature }} |
  lien: {{ lien }} |
  genre: {{ genre }} |
  form completo: {{ formularioCompleto }} 
  
</div> -->

    <div class="la-tabla" v-if="mostrarTabla">
      <div class="table-responsive cont-tabla">
        <table class="table mi-tabla">
          <thead>
            <tr>
              <th scope="col">
                <div class="form-check">
                  <input
                    class="form-check-input mi-checkbox"
                    type="checkbox"
                    id="flexCheckIndeterminate"
                    :disabled="deshabilitarChecks"
                    @change="selectAllChecks"
                  />
                  <label
                    class="form-check-label"
                    for="flexCheckIndeterminate"
                  ></label>
                </div>
              </th>
              <!-- <th scope="col">Email</th> -->
              <th scope="col">Nom</th>
              <th scope="col">Pay</th>
              <th scope="col">Type de média</th>
              <th scope="col">Genre</th>
              <th scope="col">Langue</th>
              <th scope="col" v-if="showSelect">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in datosFiltrados" :key="index" class="">
              <td>
                <div class="form-check">
                  <input
                    class="form-check-input mi-checkbox"
                    type="checkbox"
                    :value="item.id"
                    v-model="checkeds"
                    :id="'checkbox-' + item.id"
                    :disabled="deshabilitarChecks"
                  />
                  <label
                    class="form-check-label"
                    :for="'checkbox-' + item.id"
                  ></label>
                </div>
              </td>
              <!-- <td>{{ item.test_email }}</td> -->

              <td>{{ item.name }}</td>
              <td>{{ item.country }}</td>
              <td>{{ item.media_type }}</td>
              <td>{{ item.music_genre }}</td>
              <td>{{ item.secondary_language }}</td>
              <td v-if="showSelect">
                <div
                  v-if="fueEnviado(item)"
                  style="color: green; text-align: center"
                >
                  ✓
                </div>
                <div v-else style="color: #ccc; text-align: center">⭕</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- modal de confirmacion -->
    <div
      class="modal fade"
      id="confirmModal"
      tabindex="-1"
      aria-labelledby="confirmModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">Confirmar Envío</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p id="modalMessage">
              ¿Estás seguro que deseas enviar estos correos?
            </p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-eclose"
              data-bs-dismiss="modal"
              id="cancelBtn"
            >
              Cancelar
            </button>
            <button type="button" class="btn btn-econf" id="confirmBtn">
              Confirmar Envío
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- fin modal -->

    <!-- MODAL DE VISTA PREVIA -->

    <!-- MODAL DE VISTA PREVIA ACTUALIZADO -->
    <div class="modal fade" id="previewModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Vista previa del mensaje</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body">
            <!-- Mensaje dinámico que cambia según el idioma -->
            <div v-html="mensajePreviewCompleto"></div>
          </div>
          <div class="modal-footer">
            <div class="cont-langue w-100 d-flex">
              <div class="ms-auto d-flex flex-row">
                <!-- Botones de idioma con funcionalidad -->
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'EN'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('EN')"
                >
                  EN
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'FR'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('FR')"
                >
                  FR
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'KO'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('KO')"
                >
                  KO
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'UK'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('UK')"
                >
                  UK
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'RU'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('RU')"
                >
                  RU
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'ES'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('ES')"
                >
                  ES
                </button>
                <button
                  class="btn btn-sm m-1 btn-langue"
                  :class="
                    idiomaSeleccionado === 'JP'
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="cambiarIdioma('JP')"
                >
                  JP
                </button>
              </div>
            </div>
            <button
              type="button"
              class="btn btn-eclose"
              data-bs-dismiss="modal"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- FIN MODAL VISTA PREVIA -->

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

    <!-- modal backdrop -->

    <transition name="overlay">
      <div
        v-if="mostrandoOverlay"
        class="overlay-loading d-flex justify-content-center align-items-center"
      >
        <div
          class="card text-center p-4 shadow-lg text-light rounded-4 container-overlay"
        >
          <div class="mb-3">
            <div
              class="spinner-grow text-light"
              style="width: 4rem; height: 4rem"
              role="status"
            >
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
          <h5 class="mb-2">{{ textoOverlay }}</h5>
          <small class="text-secondary subtitulo"
            >Esto puede tardar varios minutos...</small
          >
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import "@/assets/toggle-style.css";

const showSelect = ref(false); // false = input

const campEnviadas = ref([]);
const emailsEnviados = ref([]);
const filtroYaEnviados = ref(false);
const emailsYaEnviados = ref([]);
const enviadoresEmails = ref([]);
const filtrarPorPais = ref("");
const filtrarPorPaisExistente = ref("");

// filtrando las inactivas
const campActivasEnviadas = computed(() => {
  return (
    campEnviadas.value?.filter((camp) => camp.activa == 1 && camp.sb != 0) || []
  );
});

console.log(
  "las enviadas active",
  campEnviadas.value.map((c) => ({
    id: c.id,
    activa: c.activa,
    sb: c.sb,
    tipo: typeof c.sb,
  }))
);

const mensajePreview = ref("");

const campagne = ref(""); //   input 1
const lancement = ref(""); //   select 1
const signature = ref("");
const nomLancement = ref("");
const pays = ref(""); //   input 3

const lien = ref(""); //   input 2
const genre = ref(""); //   select 2

const deshabilitarChecks = computed(() => {
  if (!showSelect.value) {
    // Modo crear campaña: deshabilitar si formulario incompleto
    return !formularioCompleto.value;
  } else {
    // Modo usar existente: deshabilitar si no hay campaña seleccionada
    return !selectedCamp.value;
  }
});

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

//  obtener la campaña seleccionada
const selectedCamp = computed(() => {
  if (!campagne.value) return null;

  return campEnviadas.value.find((camp) => camp.id === campagne.value);
});

console.log("Valor inicial de showSelect:", showSelect.value);

const formularioCompleto = computed(() => {
  return !!(
    campagne.value &&
    lancement.value &&
    lien.value &&
    genre.value &&
    signature.value &&
    nomLancement.value &&
    !nombreCampanaExiste.value
  );
});

onMounted(() => {
  fetch("http://localhost/prom_system/api/get_email_accounts.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error al traer users: " + response.status);
      }
      return response.json();
    })
    .then((json) => {
      console.log("emails accounts cargados: ", json);
      enviadoresEmails.value = json;
    })
    .catch((err) => {
      console.error("Error en la respuesta de enviadores: ", err);
    });
});

watch(formularioCompleto, (nuevo) => {
  console.log("formularioCompleto:", nuevo, "tipo:", typeof nuevo);
});

onMounted(() => {
  console.log(
    "formularioCompleto ahora esta completo:",
    formularioCompleto.value
  );
});

const mostrarTabla = ref(false);

//
const idiomaSeleccionado = ref("FR"); // idioma por defecto

// funcion para obtener las traducciones
function obtenerTraducciones() {
  const tradLanzamiento = {
    Album: {
      French: "album",
      English: "album",
      Russian: "альбом",
      Ukrainian: "альбом",
      Korean: "앨범",
      Japanese: "アルバム",
      Spanish: "álbum",
    },
    EP: {
      French: "Ep",
      English: "Ep",
      Russian: "Ep",
      Ukrainian: "Ep",
      Korean: "Ep",
      Japanese: "Ep",
      Spanish: "Ep",
    },
    Single: {
      French: "single",
      English: "single",
      Russian: "сингл",
      Ukrainian: "сингл",
      Korean: "싱글",
      Japanese: "シングル",
      Spanish: "sencillo",
    },
  };

  const traduccionesMedia = {
    Radio: {
      French: "programme",
      English: "program",
      Russian: "программа",
      Ukrainian: "програма",
      Korean: "프로그램",
      Japanese: "番組",
      Spanish: "programa",
    },
    TV: {
      French: "programme",
      English: "program",
      Russian: "программа",
      Ukrainian: "програма",
      Korean: "프로그램",
      Japanese: "番組",
      Spanish: "programa",
    },
    Magazine: {
      French: "magazine",
      English: "magazine",
      Russian: "журнал",
      Ukrainian: "журнал",
      Korean: "잡지",
      Japanese: "雑誌",
      Spanish: "revista",
    },
  };

  // Mapeo de códigos de idioma
  const mapaIdiomas = {
    FR: "French",
    EN: "English",
    RU: "Russian",
    UK: "Ukrainian",
    KO: "Korean",
    JP: "Japanese",
    ES: "Spanish",
  };

  const idioma = mapaIdiomas[idiomaSeleccionado.value] || "French";

  // Obtener valores actuales
  const tipoLanzamiento = showSelect.value
    ? selectedCamp.value?.tipo_de_lanzamiento || lancement.value
    : lancement.value;

  const enlace = showSelect.value
    ? selectedCamp.value?.enlace || lien.value
    : lien.value;

  const lanzTraducido =
    tradLanzamiento[tipoLanzamiento]?.[idioma] || tipoLanzamiento;
  const mediaTraducido = traduccionesMedia["Radio"]?.[idioma] || "programme"; // Por defecto Radio

  return {
    asunto: {
      French: `Nouveau lancement de ${lanzTraducido}`,
      English: `New release of ${lanzTraducido}`,
      Russian: `Новый релиз ${lanzTraducido}`,
      Ukrainian: `Новий реліз ${lanzTraducido}`,
      Korean: `${lanzTraducido}의 새 출시`,
      Japanese: `${lanzTraducido}の新リリース`,
      Spanish: `Nuevo lanzamiento de ${lanzTraducido}`,
    },
    mensaje: {
      French: `<p>Bonjour, il y a quelque temps, j'ai découvert votre ${mediaTraducido} et j'aime beaucoup.<br> Je vous informe que j'ai un projet et j'aimerais savoir si je pourrais avoir une opportunité.</p><p><a href="${enlace}">${enlace}</a></p><p>Merci pour votre temps.</p>`,
      English: `<p>Hello, some time ago I discovered your ${mediaTraducido} and I like it a lot.<br> I want to tell you I have a project and would like to know if I could have an opportunity.</p><p><a href="${enlace}">${enlace}</a></p><p>Thank you for your time.</p>`,
      Russian: `<p>Здравствуйте, некоторое время назад я узнал о вашем ${mediaTraducido} и мне очень понравилось.<br> Хочу сообщить, что у меня есть проект, и я хотел бы узнать, есть ли возможность.</p><p><a href="${enlace}">${enlace}</a></p><p>Спасибо за ваше время.</p>`,
      Ukrainian: `<p>Привіт, деякий час тому я дізнався про ваш ${mediaTraducido} і він мені дуже подобається.<br> Хочу повідомити, що у мене є проєкт, і я хотів би дізнатись, чи можу я мати можливість.</p><p><a href="${enlace}">${enlace}</a></p><p>Дякую за ваш час.</p>`,
      Korean: `<p>안녕하세요, 얼마 전에 귀하의 ${mediaTraducido}를 알게 되었고 매우 좋아합니다.<br> 프로젝트가 있어 기회가 있을지 알고 싶습니다.</p><p><a href="${enlace}">${enlace}</a></p><p>시간 내주셔서 감사합니다.</p>`,
      Japanese: `<p>こんにちは、しばらく前にあなたの${mediaTraducido}を知り、とても気に入っています.<br>プロジェクトがあり、チャンスがあるかどうか知りたいです。</p><p><a href="${enlace}">${enlace}</a></p><p>お時間をいただきありがとうございます。</p>`,
      Spanish: `<p>Hola, hace un tiempo conocí su ${mediaTraducido} y me gusta mucho.<br> Le comento que tengo un proyecto y me gustaría saber si podría tener alguna oportunidad<br></p><p><a href="${enlace}">${enlace}</a></p><p>gracias por su tiempo.</p>`,
    },
  };
}

// computed para el mensaje de vista previa actualizado
const mensajePreviewCompleto = computed(() => {
  const traducciones = obtenerTraducciones();
  const mapaIdiomas = {
    FR: "French",
    EN: "English",
    RU: "Russian",
    UK: "Ukrainian",
    KO: "Korean",
    JP: "Japanese",
    ES: "Spanish",
  };

  const idioma = mapaIdiomas[idiomaSeleccionado.value] || "French";
  const asunto = traducciones.asunto[idioma];
  const mensaje = traducciones.mensaje[idioma];

  return `
    <p><strong>Asunto:</strong> ${asunto}</p>
    <hr>
    ${mensaje}
  `;
});

// funcion para cambiar idioma
function cambiarIdioma(nuevoIdioma) {
  idiomaSeleccionado.value = nuevoIdioma;
  console.log("Idioma cambiado a:", nuevoIdioma);
}

// comparador con campañas existentes
const nombreCampanaExiste = computed(() => {
  return campEnviadas.value.some(
    (camp) =>
      camp.artista === campagne.value &&
      camp.tipo_de_lanzamiento === lancement.value &&
      camp.nombre_lanzamiento === nomLancement.value
  );
});

//  mostrar previsualizacion
function mostrarPrevisualizacion() {
  // verificar que hay datos suficientes
  if (!showSelect.value && (!lancement.value || !lien.value)) {
    // alert('Por favor completa los campos de lanzamiento y enlace');
    return;
  }

  if (showSelect.value && !selectedCamp.value) {
    alert("Por favor selecciona una campaña");
    return;
  }

  const modal = new bootstrap.Modal(document.getElementById("previewModal"));
  modal.show();
}

// watcher para actualizar el mensaje cuando cambie el idioma
watch(idiomaSeleccionado, () => {
  mensajePreview.value = mensajePreviewCompleto.value;
});

// watcher para actualizar cuando cambien los datos de la campaña
watch([lancement, lien, selectedCamp], () => {
  mensajePreview.value = mensajePreviewCompleto.value;
});

// inicializar el mensaje de vista previa
onMounted(() => {
  mensajePreview.value = mensajePreviewCompleto.value;
});

// fin mostrar previo

const datosFiltrados = computed(() => {
  return data.value.filter((item) => {
    const dbGenre = (item.music_genre || "").trim().toLowerCase();

    let genreToCompare;

    if (showSelect.value) {
      genreToCompare = selectedCamp.value?.music_genre || "";
    } else {
      genreToCompare = genre.value || "";
    }

    const selectedGenre = genreToCompare.trim().toLowerCase();

    console.log("el genero seleccionado es: ", selectedGenre);

    // filtrar por género
    const coincideGenero = selectedGenre ? dbGenre === selectedGenre : false;

    //  si no coincide el genero, no mostrar
    if (!coincideGenero) return false;

    // NUEVO: Filtro por país
    // Filtro por país (ambos modos)
    const filtroCountry = showSelect.value
      ? filtrarPorPaisExistente.value
      : filtrarPorPais.value;
    if (filtroCountry.trim()) {
      const paisItem = (item.country || "").trim().toLowerCase();
      const paisBuscado = filtroCountry.trim().toLowerCase();
      if (!paisItem.includes(paisBuscado)) {
        return false;
      }
    }

    //  filtro de enviados cuando esta activo
    if (showSelect.value && filtroYaEnviados.value) {
      // mostrar solo los que no han sido enviados (filtrar enviados)
      return !fueEnviado(item);
    }

    // si el filtro no está activo, mostrar todos los que coinciden en genero
    return true;
  });
});

const data = ref([]);

function limpiarYCargar(nuevoValor) {
  campagne.value = "";
  lancement.value = "";
  lien.value = "";
  genre.value = "";
  nomLancement.value = "";
  signature.value = "";
  filtrarPorPais.value = "";
  filtrarPorPaisExistente.value = "";
  // ocultar  tabla
  mostrarTabla.value = false;

  // limpiar selecciones y datos activos
  checkeds.value = [];
  showPreview.value = false;
  cantidadCcheks.value = 0;

  if (nuevoValor === true) {
    console.log("Cambiando a usar campaña existente, recargando datos...");

    // volver a cargar campañas
    fetch("http://localhost/prom_system/api/conex-campanas.php")
      .then((response) => response.json())
      .then((json) => {
        console.log("campEnviadas recargadas:", json);
        campEnviadas.value = json;

        json.forEach((campana) => {
          console.log("activa:", campana.activa);
        });
      })
      .catch((err) => console.error("Error al recargar campañas:", err));

    // volver a cargar emails enviados
    fetch("http://localhost/prom_system/api/emails-ya-enviados.php")
      .then((response) => response.json())
      .then((json) => {
        emailsYaEnviados.value = json;
        console.log("Emails enviados recargados");
      })
      .catch((err) => console.error("Error al recargar emails enviados:", err));
  }

  // data.value = [];

  // nextTick(() => {
  //   console.log('Limpieza completada');
  // });
}

watch(showSelect, (nuevoValor) => {
  limpiarYCargar(nuevoValor);
});

watch(genre, (nuevoGenero) => {
  if (showSelect.value) {
    checkeds.value = [];
    showPreview.value = false;
    cantidadCcheks.value = 0;

    // console.log('genero cambiado a: ', nuevoGenero, ' - selecciones limpiadas');
  }
});

watch(campagne, (nuevaCampana) => {
  if (showSelect.value) {
    checkeds.value = [];
    showPreview.value = false;
    cantidadCcheks.value = 0;

    // this.$forceUpdate();

    // console.log('Campaña cambiada - secciones limpiadas');
  }
});

watch(filtroYaEnviados, (otroValor) => {
  // ocultar  tabla
  //   mostrarTabla.value = false;

  // limpiar completamente las selecciones y datos activos
  checkeds.value = [];
  showPreview.value = false;
  cantidadCcheks.value = 0;

  // verificar si el elemento existe antes de acceder a él
  const checkbox = document.querySelector("#flexCheckIndeterminate");
  if (checkbox) {
    checkbox.checked = false;
  }

  // data.value = [];
});

watch(selectedCamp, (nuevaCampana) => {
  if (showSelect.value && nuevaCampana) {
    // asignar valores de la campaña seleccionada a las variables
    lancement.value = nuevaCampana.tipo_de_lanzamiento || "";
    lien.value = nuevaCampana.enlace || "";
    genre.value = nuevaCampana.music_genre || "";

    signature.value = nuevaCampana.id_email_account || "";
    nomLancement.value = nuevaCampana.nombre_lanzamiento || "";

    // console.log("aqui el id de usuario en campaña consultada:", signature.value);
    // console.log("aqui el valor en campaña consultada:", lancement.value);
  }
});

// ESTO DESABILITABA MAL LOS BOTONES

// watch(nomLancement, () => {
//   if (!showSelect.value) { // solo en modo crear
//     ocultaCheks();
//   }
// });

watch(deshabilitarChecks, (nuevoValor, valorAnterior) => {
  if (valorAnterior && !nuevoValor && checkeds.value.length > 0) {
    showPreview.value = true;
  }
});

// obtener campañas para imprimir el select
onMounted(() => {
  fetch("http://localhost/prom_system/api/conex-campanas.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la respuesta: " + response.status);
      }
      return response.json();
    })
    .then((json) => {
      console.log("campEnviadas cargadas:", json);
      console.log("showSelect value", showSelect);
      campEnviadas.value = json;
    })
    .catch((err) => {
      console.error("Error fetch campañas:", err);
    });
});

onMounted(() => {
  fetch("http://localhost/prom_system/api/media-contacts.php")
    .then((response) => response.json())
    .then((json) => {
      console.log(json);
      data.value = json;
    });
});

onMounted(() => {
  fetch("http://localhost/prom_system/api/emails-ya-enviados.php")
    .then((response) => response.json())
    .then((json) => {
      emailsYaEnviados.value = json;
      console.log(json);
    });
});

function fueEnviado(item) {
  if (!showSelect.value || !selectedCamp.value) return false;

  const encontrado = emailsYaEnviados.value.some(
    (e) => e.id_contacto == item.id && e.campaña_id == selectedCamp.value.id
  );

  // Debug temporal para verificar
  console.log(
    `Verificando contacto ID ${item.id} para campaña ${selectedCamp.value.id}:`,
    encontrado
  );

  return encontrado;
}

const checkeds = ref([]);
const showPreview = ref(false);
const cantidadCcheks = ref(0);

watch(checkeds, (nuevos) => {
  showPreview.value = nuevos.length > 0 && !deshabilitarChecks.value;
  cantidadCcheks.value = nuevos.length;
  // console.log("activaste:" + cantidadCcheks.value + " medios");
});

function selectAllChecks(event) {
  const marcado = event.target.checked;

  if (marcado) {
    //   agrega todos los IDs visibles que no estén ya seleccionados
    const nuevos = datosFiltrados.value
      .map((item) => item.id)
      .filter((id) => !checkeds.value.includes(id));
    checkeds.value = [...checkeds.value, ...nuevos];
  } else {
    //elimina visibles ids
    const visibles = datosFiltrados.value.map((item) => item.id);
    checkeds.value = checkeds.value.filter((id) => !visibles.includes(id));
  }

  // info en consola
  checkeds.value.forEach((id) => {
    const item = datosFiltrados.value.find((i) => i.id === id);
    if (item) {
      //   console.log(`${item.test_email} | ${item.media_type} | ${item.secondary_language}`);
    }
  });
}

function mostrarModalConfirmacion(mensaje) {
  return new Promise((resolve, reject) => {
    document.getElementById("modalMessage").textContent = mensaje;

    // mostrar modal
    const modalElement = document.getElementById("confirmModal");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    // botones
    const confirmBtn = document.getElementById("confirmBtn");
    const cancelBtn = document.getElementById("cancelBtn");

    // limpiar eventos
    function limpiarEventos() {
      confirmBtn.removeEventListener("click", confirmar);
      cancelBtn.removeEventListener("click", cancelar);
      modalElement.removeEventListener("hidden.bs.modal", cancelar);
    }

    //funcion confirar
    function confirmar() {
      limpiarEventos();
      document.activeElement.blur();
      modal.hide();

      resolve(true);
    }

    // cancelar
    function cancelar() {
      limpiarEventos();
      document.activeElement.blur();
      modal.hide();
      resolve(false);
    }

    confirmBtn.addEventListener("click", confirmar);
    cancelBtn.addEventListener("click", cancelar);

    modalElement.addEventListener("hidden.bs.modal", cancelar);
  });
}

function mostrarToast(mensaje) {
  // actualizar mensaje
  document.getElementById("toastMessage").textContent = mensaje;

  const toastElement = document.getElementById("successToast");
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: 4000,
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

//     // obtener la firma
// async function obtenerFirma() {
//     const response = await fetch(`http://localhost/prom_system/api/credenciales.php?id=${signature.value}`);
//     const firma = await response.json();

//     console.log('Firma obtenida:', firma);

//     return firma;
// }

// // Watch para que se ejecute automáticamente cuando cambie signature
// watch(signature, (nuevoId) => {
//     if (nuevoId) {
//         obtenerFirma(nuevoId);
//     }
// });

async function enviarCorreos() {
  console.log("=== DEBUG ENVIAR CORREOS ===");
  console.log("showSelect.value:", showSelect.value);
  console.log("selectedCamp.value:", selectedCamp.value);
  console.log("signature.value:", signature.value);
  console.log("checkeds.value:", checkeds.value);
  console.log("datosFiltrados.value:", datosFiltrados.value);

  const seleccionados = datosFiltrados.value.filter((item) =>
    checkeds.value.includes(item.id)
  );
  console.log("seleccionados:", seleccionados);

  console.log("el valor de signature es:", signature.value);

  // const seleccionados = datosFiltrados.value.filter(item => checkeds.value.includes(item.id));

  const datosParaEnviar = seleccionados.map((item) => {
    const tradLanzamiento = {
      // tipo de lanzamiento
      Album: {
        French: "album",
        English: "album",
        Russian: "альбом",
        Ukrainian: "альбом",
        Korean: "앨범",
        Japanese: "アルバム",
        Spanish: "álbum",
      },
      EP: {
        French: "EP",
        English: "EP",
        Russian: "EP",
        Ukrainian: "EP",
        Korean: "EP",
        Japanese: "EP",
        Spanish: "EP",
      },
      Single: {
        French: "single",
        English: "single",
        Russian: "сингл",
        Ukrainian: "сингл",
        Korean: "싱글",
        Japanese: "シングル",
        Spanish: "sencillo",
      },
    };

    const traduccionesMedia = {
      // programa o revista

      Radio: {
        French: "programme",
        English: "program",
        Russian: "программа",
        Ukrainian: "програма",
        Korean: "프로그램",
        Japanese: "番組",
        Spanish: "programa",
      },
      TV: {
        French: "programme",
        English: "program",
        Russian: "программа",
        Ukrainian: "програма",
        Korean: "프로그램",
        Japanese: "番組",
        Spanish: "programa",
      },
      Magazine: {
        French: "magazine",
        English: "magazine",
        Russian: "журнал",
        Ukrainian: "журнал",
        Korean: "잡지",
        Japanese: "雑誌",
        Spanish: "revista",
      },
    };

    const mediaTraducido =
      traduccionesMedia[item.media_type]?.[item.secondary_language] ||
      item.media_type;

    // Usar el valor correcto según si es campaña nueva o existente
    const tipoLanzamientoActual =
      showSelect.value && selectedCamp.value
        ? selectedCamp.value.tipo_de_lanzamiento
        : lancement.value;

    const lanzTraducido =
      tradLanzamiento[tipoLanzamientoActual]?.[item.secondary_language] ||
      tipoLanzamientoActual;

    const langue = item.secondary_language;
    const elMedio = mediaTraducido; // programa o revista
    const lanzamiento = lanzTraducido; // tipo de lanzamiento

    // Usar el enlace correcto según si es campaña nueva o existente
    const enlace =
      showSelect.value && selectedCamp.value
        ? selectedCamp.value.enlace
        : lien.value;

    function obtenerTradAsunto(lanzamiento, elMedio, enlace) {
      return {
        asunto: {
          French: `Nouveau lancement de ${lanzamiento}`,
          English: `New release of ${lanzamiento}`,
          Russian: `Новый релиз ${lanzamiento}`,
          Ukrainian: `Новий реліз ${lanzamiento}`,
          Korean: `${lanzamiento}의 새 출시`,
          Japanese: `${lanzamiento}の新リリース`,
          Spanish: `Nuevo lanzamiento de ${lanzamiento}`,
        },
        mensaje: {
          French: `<p>Bonjour, il y a quelque temps, j'ai découvert votre ${elMedio} et j'aime beaucoup.<br> Je vous informe que j'ai un projet et j'aimerais savoir si je pourrais avoir une opportunité.</p><p><a href="${enlace}">${enlace}</a></p><p>Merci pour votre temps.</p>`,
          English: `<p>Hello, some time ago I discovered your ${elMedio} and I like it a lot.<br> I want to tell you I have a project and would like to know if I could have an opportunity.</p><p><a href="${enlace}">${enlace}</a></p><p>Thank you for your time.</p>`,
          Russian: `<p>Здравствуйте, некоторое время назад я узнал о вашем ${elMedio} и мне очень понравилось.<br> Хочу сообщить, что у меня есть проект, и я хотел бы узнать, есть ли возможность.</p><p><a href="${enlace}">${enlace}</a></p><p>Спасибо за ваше время.</p>`,
          Ukrainian: `<p>Привіт, деякий час тому я дізнався про ваш ${elMedio} і він мені дуже подобається.<br> Хочу повідомити, що у мене є проєкт, і я хотів би дізнатись, чи можу я мати можливість.</p><p><a href="${enlace}">${enlace}</a></p><p>Дякую за ваш час.</p>`,
          Korean: `<p>안녕하세요, 얼마 전에 귀하의 ${elMedio}를 알게 되었고 매우 좋아합니다.<br> 프로젝트가 있어 기회가 있을지 알고 싶습니다.</p><p><a href="${enlace}">${enlace}</a></p><p>시간 내주셔서 감사합니다.</p>`,
          Japanese: `<p>こんにちは、しばらく前にあなたの${elMedio}を知り、とても気に入っています.<br>プロジェクトがあり、チャンスがあるかどうか知りたいです。</p><p><a href="${enlace}">${enlace}</a></p><p>お時間をいただきありがとうございます。</p>`,
          Spanish: `<p>Hola, hace un tiempo conocí su ${elMedio} y me gusta mucho.<br> Le comento que tengo un proyecto y me gustaría saber si podría tener alguna oportunidad<br></p><p><a href="${enlace}">${enlace}</a></p><p>gracias por su tiempo.</p>`,
        },
      };
    }

    const tradAsunto = obtenerTradAsunto(lanzamiento, elMedio, enlace);
    const asuntoEnIdioma =
      tradAsunto.asunto[item.secondary_language] || tradAsunto.asunto.English;

    const mensajeEnIdioma =
      tradAsunto.mensaje[item.secondary_language] || tradAsunto.mensaje.English;

    return {
      name: item.name,
      idioma: item.secondary_language,
      //	 email: item.test_email,
      idContact: item.id,
      tradAsunto: asuntoEnIdioma,
      tradMensaje: mensajeEnIdioma,
    };
  });

  const datosCampana = {
    nombreDeCampana: campagne.value,
    lanzamiento: lancement.value,
    elLink: lien.value,
    elGenero: genre.value,
    nombreLanzamiento: nomLancement.value,
    idFirma: signature.value,
  };

  console.log("estos son los datos de la campaña: ", datosCampana);

  emailsEnviados.value = datosParaEnviar.map((item) => ({
    //email: item.email,
    name: item.name,
    idioma: item.idioma,
    idContact: item.idContact,
  }));

  console.log("emailsEnviados:", emailsEnviados.value);
  console.log("datosParaEnviar:", datosParaEnviar);

  // HACER ARRAY DE ENVIADOS

  const yaExiste = campEnviadas.value.find(
    (c) => c.id_email_account == signature.value && c.activa == 1
  );

  let mensaje = "";

  if (yaExiste && !showSelect.value) {
    mensaje = `Crear esta campaña cerrará la campaña activa: "${yaExiste.nombre_lanzamiento}" de ${yaExiste.artista}.\n\n¿Seguro de enviar estos ${datosParaEnviar.length} correos?`;
  } else {
    mensaje = `¿Seguro de enviar estos ${datosParaEnviar.length} correos?`;
  }

  const confirmacion = await mostrarModalConfirmacion(mensaje);

  if (!confirmacion) {
    console.log("envio cancelado por el usuario");
    return;
  }

  console.log("usuaio confirmo");

  mostrarOverlay("Enviando correos, por favor espera...");

  await new Promise((resolve) => setTimeout(resolve, 100));

  fetch("http://localhost/prom_system/api/enviar-emails.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      firma: signature.value,
      correos: datosParaEnviar,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Correos enviados correctamente:", data.enviados);

      mostrarToast(
        `muy bien, se enviaron ${data.enviados.length} correos exitosamente`
      );

      limpiarYCargar(showSelect.value);
    })
    .catch((error) => {
      console.error("Error al enviar correos:", error);
      mostrarToastError(
        "Hubo un error al enviar los correos. Inténtalo de nuevo."
      );
    })
    .finally(() => {
      ocultarOverlay();
    });

  // emailsEnviados.value = datosParaEnviar.map(item => item.email);

  // esta cosa hacia que se seleccionen todos en usar campaña existente
  // emailsEnviados.value = [...emailsEnviados.value, ...direccionesEnviadas];

  console.log("array de direcciones enviadas: ", emailsEnviados.value);
  console.log("se enviaron ", emailsEnviados.value.length, " emails");
  // console.log('id de campaña seleccionada: ', selectedCamp.value.id);

  // mostrarToast(`muy bien, se enviaron ${data.enviados.length} emails`);

  // function limpiarHistorialEmails() {
  //     emailsEnviados.value = [];
  //     console.log('Historial de emails limpiado');
  // }

  // limpiarHistorialEmails() ;

  // enviar a 2 tablas

  const datosAEnviar = {
    envioDeCampana: datosCampana,
    envioDeEmails: emailsEnviados.value,
  };

  console.log("los datos a enviar son: ", datosAEnviar);

  // console.

  // console.log('showSelect value', showSelect.value);

  if (!showSelect.value) {
    fetch("http://localhost/prom_system/api/insert-campanas.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datosAEnviar),
    });

    // .then(res => res.text())
    // .then(data => {
    // 	console.log('respuesta gigante si lanzamos data', data );
    // });
  } else {
    const emailsParaEnviar = {
      envioDeEmails: emailsEnviados.value,
      idCampExistente: selectedCamp.value ? selectedCamp.value.id : null,
    };

    console.log(
      "enviar a campaña existente (emails para envar):",
      emailsParaEnviar
    );

    fetch("http://localhost/prom_system/api/insert-enviados.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(emailsParaEnviar),
    })
      .then((res) => res.text())
      .then((data) => console.log("respuesta insert-enviados:", data));
  }
}

// import { watch } from "vue";

// watch(formularioCompleto, (nuevoValor) => {
// 	if(nuevoValor) {
// 		console.log("Formulario completo");
// 		console.log("nom de la campagne: ", campagne.value);
// 		console.log("type de lancement: ", lancement.value);
// 		console.log("lien: ", lien.value);
// 		console.log("genre: ", genre.value);
// 	}
// })
</script>

<style scoped>
/* spinner */
/* Fondo desenfocado y oscuro */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;

  backdrop-filter: blur(3px) saturate(120%) !important;
  background-color: rgba(8, 47, 56, 0.432) !important;
  /* -webkit-backdrop-filter: blur(8px); */
}

/* Contenedor central tipo glass */
.div-espere {
  background-color: var(--dark-echo) !important;
  color: var(--light-echo);
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
  text-align: center;
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
}

/* Texto claro */
.div-espere p {
  color: var(--light-echo);
  font-size: 1.1rem;
}

/* Spinner blanco */
.div-espere .spinner-border {
  color: var(--light-echo);
  border-color: var(--light-echo);
  border-right-color: transparent;
}

#errorToastMessage,
#toastMessage {
  color: var(--light-echo);
}

.caja {
  width: 345px;
  height: 333px;
  background-color: burlywood;
  color: brown;
}

:global(body.modal-open) {
  overflow-y: scroll !important;
  padding-right: 0 !important;
}

.btn-group-toggle input[type="radio"] {
  display: none;
}

/* inputs */

.datos-camp input.form-control,
.datos-camp select.form-select,
.form-control {
  background-color: var(--medium-echo);
  color: var(--bright-echo);
  border: 1px solid var(--medium-echo);
  border-radius: 12px;
  padding: 8px 14px;
  transition: all 0.3s ease;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

/* placeholder */
.datos-camp input.form-control::placeholder {
  color: var(--bright-echo);
  opacity: 0.6;
}

/* focus */
.datos-camp input.form-control:focus,
.datos-camp select.form-select:focus {
  outline: none;
  background-color: var(--dark-echo);
  border-color: var(--light-echo);
  box-shadow: 0 0 0 2px var(--light-echo), 0 0 8px var(--medium-echo);
  color: var(--light-echo);
}

/* labels */
.datos-camp .form-label.subti {
  color: var(--dark-echo) !important;
  font-weight: 00;
}

/* options select */
.datos-camp select.form-select option {
  background-color: var(--dark-echo);
  color: var(--light-echo);
}

/* non validation */
.text-danger {
  color: #ff5555 !important;
}

#confirmModal,
#previewModal {
  backdrop-filter: blur(2px) saturate(130%);
  background-color: rgba(8, 47, 56, 0.322);
}

/* mantenimiento */
/* 

:global(body.modal-open) {
  padding-right: 0 !important;
}


:global(.modal-open .container),
:global(.modal-open .miContenedor) {
  padding-right: 0 !important;
  margin-right: auto !important;
}


:global(.modal-open) {
  overflow: hidden;
  padding-right: 0 !important;
}


:global(.modal-backdrop) ~ * .container,
:global(.modal-backdrop) ~ * .miContenedor {
  transition: none !important;
  padding-right: 0 !important;
} */

/* fin del problema */

/* .modal-content {
  background-color: var(--dark-echo);
  color: var(--light-echo);
  border-radius: 12px;
  border: 1px solid var(--medium-echo);
}

.modal-body{
  background-color: white;
  color: black;
  padding: 20px;
} */

.modal-footer,
.modal-header {
  background-color: var(--dark-echo);
  color: var(--light-echo);
  border-top: 1px solid var(--medium-echo);
}

.btn-langue {
  background-color: var(--dark-echo);
  color: var(--light-echo);
}

.btn-langue:hover {
  background-color: var(--light-echo);
  color: var(--dark-echo);
}

.btn-langue:focus {
  outline: yellowgreen;
  box-shadow: 0 0 0 2px var(--light-echo);
}
.btn-langue:focus-visible {
  outline: none;
  box-shadow: none;
}
.btn-langue.selected {
  background-color: var(--light-echo);
  color: var(--dark-echo);
}

.btn-close {
  filter: brightness(0) saturate(100%) invert(90%) sepia(10%) saturate(500%)
    hue-rotate(180deg);
  /* Esto se puede ajustar para el color deseado */
}

.btn-close:focus {
  box-shadow: none; /* quita el borde azul al hacer clic */
}
</style>
