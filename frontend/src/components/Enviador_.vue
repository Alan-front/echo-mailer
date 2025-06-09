<template>

	<div class="container miContenedor">

		<h1 class="text-center">Gestor de Campañas</h1>
		<h5 class="text-center">Crea y administra tus campañas de email marketing.</h5>
		
		<br>
		
			<form action="">
			<div class="d-flex justify-content-center ">

			<div class="row" data-toggle="buttons">

  <label class="col btn-campaña"
  :class="{ active: !showSelect}"
  @click="showSelect = false">
    <input type="radio" name="options" id="option1" autocomplete="off" 
	checked> 
	<span class="icon-circle">
    <i class="fa fa-plus-circle ico-btn" aria-hidden="true"></i>
  </span><br>Crear campaña
  <p class="descripcion">Inicia una nueva campaña desde cero.</p> 
  </label>

  <label class="col btn-campaña"
  :class="{ active: showSelect}"
  @click="showSelect = true">
    <input type="radio" 
	name="options" 
	id="option2" 
	autocomplete="off"> 
	<i class="fa-solid fa-folder-open ico-btn"></i><br>Usar campaña existente
	<p class="descripcion">Continua con una campaña anterior.</p>  
</label>
  
</div>

	</div>
				<!-- tabla para crear -->

			<div class="mb-3 datos-camp" v-if="!showSelect">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="campagneInput" class="form-label subti">Nombre de la campaña</label>
            <input v-model="campagne" type="email" class="form-control" id="exampleFormControlInput1" placeholder="Nom de la campagne">
            <div v-if="nombreCampanaExiste()" class="text-danger small">
                ⚠️ Ya existe una campaña con este nombre
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="lancementSelect" class="form-label subti">Type de lancement</label>
            <select v-model="lancement" class="form-select" aria-label="Default select example">
                <option disabled value="">Type de lancement</option>
                <option value="Album">Album</option>
                <option value="Ep">Ep</option>
                <option value="Single">Single</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="lienInput" class="form-label subti">Lien</label>
            <input v-model="lien" type="email" class="form-control" id="exampleFormControlInput1" placeholder="lien">
        </div>

        <div class="col-md-6 mb-3">
            <label for="genreSelect" class="form-label subti">Genre</label>
            <select v-model="genre" class="form-select" aria-label="Default select example">
                <option disabled value="">Genre</option>
                <option value="Jazz">Jazz</option>
                <option value="Pop">Pop</option>
                <option value="Rock">Rock</option>
                <option value="World Music">World Music</option>
                <option value="Classical">Classical</option>
            </select>
        </div>
    </div>
</div>



			<div class="mb-3 datos-camp" v-else>
    		<label for="campagneSelect" 
			class="form-label subti">Elegir campaña existente</label>

			<select 
			v-model="campagne" 
			class="form-select" aria-label="Default select example"
			>
				<option v-for="camp in campEnviadas" :key="camp.id" :value="camp.id">
					{{ camp.nombre }} 
				</option>
				
				</select>
				<br>


	<div class="mb-4 data-camp">
  <p>
    <span class="campo-nombre">Lancement:</span><br>
    <span class="campo-valor">{{ selectedCamp?.tipo_de_lanzamiento || '' }}</span>
  </p>
</div>

<div class="mb-4 data-camp">
  <p>
    <span class="campo-nombre">Lien:</span><br>
    <span v-if="selectedCamp?.enlace" class="campo-valor">
      <a class="link-camp" :href="selectedCamp.enlace" target="_blank" rel="noopener">{{ selectedCamp.enlace }}</a>
    </span>
  </p>
</div>

<div class="mb-4 data-camp">
  <p>
    <span class="campo-nombre">Genre:</span><br>
    <span class="campo-valor">{{ selectedCamp?.music_genre || '' }}</span>
  </p>
</div>


				<!-- div de boton de filtrar toggle -->

			<div v-if="lancement && mostrarTabla" class="text-end ms-auto">
  <div class="toggle-wrapper">
    <label class="toggle-switch">
      <span class="toggle-label" :class="{ 'active-green': !filtroYaEnviados }">All</span>

      <input type="checkbox" v-model="filtroYaEnviados">
      <span class="slider" :class="filtroYaEnviados ? 'red' : 'green'"></span>

      <span class="toggle-label" :class="{ 'active-red': filtroYaEnviados }">No enviados</span>
    </label>
  </div>
</div>

		<!-- fin del toggle -->
			
		</div>	

			
<div class="btn-group " role="group" aria-label="...">
          <button @click="mostrarPrevisualizacion" 
		  class="btn btn-secondary" type="button">
            <i class="fas fa-eye" aria-hidden="true"></i> Previsualizar Correos
          </button>
          
          <button
			id="btn-preview"
			class="btn btn-secondary"
			type="button"
			:disabled="!formularioCompleto && !selectedCamp"
			@click="mostrarTabla = true"
			>
			<i class="fas fa-table" aria-hidden="true"></i> Mostrar Contactos
			</button>
          
          <button class="btn btn-success" type="button" 
		  :disabled="!showPreview || deshabilitarChecks" @click="enviarCorreos">
            <i class="fa fa-paper-plane"></i> Enviar Seleccionados
          </button>

		  
        </div>

		</form>



		

		<div class="la-tabla" v-if="mostrarTabla">
			<div
				class="table-responsive cont-tabla"
			>
				<table
					class="table mi-tabla"
				>
					<thead>
						<tr>
							<th scope="col">
								<div class="form-check">
                  <input 
				  class="form-check-input mi-checkbox" 
				  type="checkbox" 
				  
				  id="flexCheckIndeterminate"
				  :disabled="deshabilitarChecks"
				  @change="selectAllChecks" >
                  <label class="form-check-label" for="flexCheckIndeterminate"></label>
                </div>
							</th>
							<th scope="col">Email</th>
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
					
					>
                  <label class="form-check-label" :for="'checkbox-' + item.id"></label>
                </div>
							</td>
							<td>{{ item.test_email }}</td>
							
							<td>{{ item.name }}</td>
							<td>{{ item.country }}</td>
							<td>{{ item.media_type }}</td>
							<td>{{ item.music_genre }}</td>
							<td>{{ item.secondary_language }}</td>
							<td v-if="showSelect">
  <div v-if="fueEnviado(item)" style="color: green; text-align: center;">
    ✓ 
	 
  </div>
  <div v-else style="color: #ccc; text-align: center;">
    ⭕ 
	 
  </div>
</td>

							
						</tr>
					</tbody>
				</table>
			</div>
			

		</div>

		

		<!-- modal de confirmacion -->
<div class="modal fade" id="confirmModal" tabindex="-1" 
aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar Envío</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="modalMessage">¿Estás seguro que deseas enviar estos correos?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelBtn">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirmBtn">Confirmar Envío</button>
      </div>
    </div>
  </div>
</div>

	<!-- fin modal -->

	 <!-- MODAL DE VISTA PREVIA -->

<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista previa del mensaje</h5>
		
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div v-html="mensajePreview"></div>
		
      </div>
      <div class="modal-footer">
		<div class="cont-langue w-100 d-flex">
			
			<div class="ms-auto d-flex flex-row">
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">EN</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">FR</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">KO</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">UK</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">RU</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">ES</button>
              <button class="btn btn-sm btn-outline-secondary m-1 btn-langue">JP</button>
            </div>
		</div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

	 <!-- FIN MODAL VISTA PREVIA -->


	<!-- container toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bi bi-check-circle-fill text-success me-2"></i>
      <strong class="me-auto">Éxito</strong>
      <small class="text-body-secondary">Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toastMessage">
      <!-- mensaje -->
    </div>
  </div>
</div>

<!-- toasts de errores -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
  <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
      <strong class="me-auto">Error</strong>
      <small class="text-body-secondary">Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="errorToastMessage">
      <!-- mensaje de error -->
    </div>
  </div>
</div>

		<!-- fin de toast -->

		<!-- modal backdrop -->

		<div v-if="enviando" class="modal-backdrop show" style="z-index: 9999;">
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="text-center bg-white p-4 rounded shadow">
      <div class="spinner-border text-primary mb-3" role="status"></div>
      <p class="mb-0">Enviando correos, por favor espera...</p>
    </div>
  </div>
</div>



	</div>

	

</template>

<script setup>

import { ref, computed, onMounted, watch } from "vue";
import '@/assets/toggle-style.css';


const showSelect = ref(false) // false = input

const campEnviadas = ref ([])
const emailsEnviados = ref([])
const filtroYaEnviados = ref(false)
const emailsYaEnviados = ref([])

const mensajePreview = ref('')

const campagne = ref("");  		//   input 1
const lancement = ref("Album");		//   select 1
const lien = ref("www.realcamaradegas.com");	//   input 2
const genre = ref("Jazz");			//   select 2

const deshabilitarChecks = ref(false);

const enviando = ref(false);


  //  obtener la campaña seleccionada
const selectedCamp = computed(() => {

  if (!campagne.value) return null
  
  return campEnviadas.value.find(camp => camp.id === campagne.value)
})



const mostrarTabla = ref(false)

const formularioCompleto = computed(() => {
	return campagne.value &&
	lancement.value &&
	lien.value &&
	genre.value &&
	!nombreCampanaExiste();
});

	// mostrar previo

	function mostrarPrevisualizacion() {
  mensajePreview.value = `
    <p><strong>Asunto:</strong> Nouveau lancement d'album</p>
    <hr>
    <p>Bonjour, il y a quelque temps, j'ai découvert votre programme et j'aime beaucoup.<br> 
    Je vous informe que j'ai un projet et j'aimerais savoir si je pourrais avoir une opportunité.</p>
    <p><a href="https://ejemplo.com">https://ejemplo.com</a></p>
    <p>Merci pour votre temps.</p>
  `;
  
  const modal = new bootstrap.Modal(document.getElementById('previewModal'));
  modal.show();
}




const datosFiltrados = computed(() => {
  return data.value.filter(item => {
    const dbGenre = (item.music_genre || '').trim().toLowerCase();
    
    let genreToCompare;
    
    if (showSelect.value) {
      genreToCompare = selectedCamp.value?.music_genre || '';
    } else {
      genreToCompare = genre.value || '';
    }
    
    const selectedGenre = genreToCompare.trim().toLowerCase();
    
    console.log('el genero seleccionado es: ', selectedGenre);
    
    // filtrar por género
    const coincideGenero = selectedGenre ? dbGenre === selectedGenre : false;
    
    //  si no coincide el genero, no mostrar
    if (!coincideGenero) return false;
    
    //  filtro de enviados cuando esta activo
    if (showSelect.value && filtroYaEnviados.value) {
      // mostrar solo los que no han sido enviados (filtrar enviados)
      return !fueEnviado(item);
    }
    
     // si el filtro no está activo, mostrar todos los que coinciden en genero
    return true;
  });
});



const data = ref([])

function limpiarYCargar(nuevoValor){
	
  campagne.value = "";
  lancement.value = "";
  lien.value = "";
  genre.value = "";
  
  	// ocultar  tabla
  mostrarTabla.value = false;

  
  	// limpiar selecciones y datos activos
  checkeds.value = [];
  showPreview.value = false;
  cantidadCcheks.value = 0;
  
  if (nuevoValor === true) {
    console.log('Cambiando a usar campaña existente, recargando datos...');
    
    // volver a cargar campañas
    fetch('http://localhost/conex-prom-system/api/conex-campanas.php')
      .then(response => response.json())
      .then(json => {
        console.log('campEnviadas recargadas:', json);
        campEnviadas.value = json;
      })
      .catch(err => console.error('Error al recargar campañas:', err));
    
    // volver a cargar emails enviados
    fetch('http://localhost/conex-prom-system/api/emails-ya-enviados.php')
      .then(response => response.json())
      .then(json => {
        emailsYaEnviados.value = json;
        console.log('Emails enviados recargados');
      })
      .catch(err => console.error('Error al recargar emails enviados:', err));
  }
 
  // data.value = [];
}

watch(showSelect, (nuevoValor) => {
  limpiarYCargar(nuevoValor);
});

watch(genre, (nuevoGenero) => {
	if(showSelect.value){
		checkeds.value = [];
	showPreview.value = false;
	cantidadCcheks.value = 0;

	console.log('genero cambiado a: ', nuevoGenero, ' - selecciones limpiadas');
	}
});

watch(campagne, (nuevaCampana) => {
	if(showSelect.value){
		checkeds.value = [];
	showPreview.value = false;
	cantidadCcheks.value = 0;

	console.log('Campaña cambiada - secciones limpiadas');
	}
});

watch(filtroYaEnviados, (otroValor) =>{

  
  	// ocultar  tabla
//   mostrarTabla.value = false;
  
  	// limpiar completamente las selecciones y datos activos
  checkeds.value = [];
  showPreview.value = false;
  cantidadCcheks.value = 0;

  document.querySelector('#flexCheckIndeterminate').checked = false;
  
 

  // data.value = [];
})

watch(selectedCamp, (nuevaCampana) => {
	if (showSelect.value && nuevaCampana) {
		// asignar valores de la campaña seleccionada a las variables
		lancement.value = nuevaCampana.tipo_de_lanzamiento || '';
		lien.value = nuevaCampana.enlace || '';
		genre.value = nuevaCampana.music_genre || '';
	}
});




		// obtener campañas para imprimir el select
onMounted(() => {
  fetch('http://localhost/conex-prom-system/api/conex-campanas.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la respuesta: ' + response.status);
      }
      return response.json();
    })
    .then(json => {
      console.log('campEnviadas cargadas:', json);
	console.log('showSelect value', showSelect);
      campEnviadas.value = json;
    })
    .catch(err => {
      console.error('Error fetch campañas:', err);
    });
});

onMounted(() => {

	fetch('http://localhost/conex-prom-system/api/media-contacts.php')
.then(response => response.json())
.then(json => {
   console.log(json)
    data.value = json

	
	})
});

onMounted(() => {
	fetch('http://localhost/conex-prom-system/api/emails-ya-enviados.php')
	.then(response => response.json())
	.then(json => {
		emailsYaEnviados.value = json
		console.log(json);

		// emailsYaEnviados.value.forEach(item => {
		// 	console.log('campaña ID:', item.campaña_id, 'Email: ', item.email);
		// });
	})
}); 

// comprobar que no exista ese nombre de campaña

function nombreCampanaExiste() {
	if (!campagne.value) return false;

	return campEnviadas.value.some(camp => 
	camp.nombre.toLowerCase() === campagne.value.toLowerCase()
	);
}


// AREGLAR ESTOQQ
function ocultaCheks() {
	if(!showSelect.value && nombreCampanaExiste()){
		
		console.log('ocultar cheks activado');
		deshabilitarChecks.value = true;
	}else {
		console.log('ocultar cheks desactivado');
		deshabilitarChecks.value = false;
	}
};

watch(campagne, (nuevoValor) => {
//   if (!showSelect.value && nombreCampanaExiste()) 
    ocultaCheks(); 
  
});


function fueEnviado(item) {
  if (!showSelect.value || !selectedCamp.value) return false;

  const encontrado = emailsYaEnviados.value.some(
    e => e.email === item.test_email && e.campaña_id == selectedCamp.value.id
  );

  // Debug temporal para verificar
  console.log(`Verificando ${item.test_email} para campaña ${selectedCamp.value.id}:`, encontrado);

  return encontrado;
}

	

const checkeds = ref([]);
const showPreview = ref(false);
const cantidadCcheks = ref(0);

watch(checkeds, (nuevos) => {
	showPreview.value = nuevos.length > 0;
	cantidadCcheks.value = nuevos.length;
	// console.log("activaste:" + cantidadCcheks.value + " medios");

});


function selectAllChecks(event) {
  const marcado = event.target.checked;

  if (marcado) {
      //   agrega todos los IDs visibles que no estén ya seleccionados
    const nuevos = datosFiltrados.value
      .map(item => item.id)
      .filter(id => !checkeds.value.includes(id));
    checkeds.value = [...checkeds.value, ...nuevos];
  } else {
      //elimina visibles ids
    const visibles = datosFiltrados.value.map(item => item.id);
    checkeds.value = checkeds.value.filter(id => !visibles.includes(id));
  }

    // info en consola
  checkeds.value.forEach(id => {
    const item = datosFiltrados.value.find(i => i.id === id);
    if (item) {
    //   console.log(`${item.test_email} | ${item.media_type} | ${item.secondary_language}`);
    }
  });
}


function mostrarModalConfirmacion (mensaje) {

	return new Promise((resolve, reject) => {
		document.getElementById('modalMessage').textContent = mensaje;

		// mostrar modal
		const modalElement = document.getElementById('confirmModal');
		const modal = new bootstrap.Modal(modalElement);
		modal.show();

		// botones
		const confirmBtn = document.getElementById('confirmBtn');
		const cancelBtn = document.getElementById('cancelBtn');

		// limpiar eventos
		function limpiarEventos() {
			confirmBtn.removeEventListener('click', confirmar);
			cancelBtn.removeEventListener('click', cancelar);
			modalElement.removeEventListener('hidden.bs.modal', cancelar);			
		}

		//funcion confirar
			function confirmar(){
				limpiarEventos();
				document.activeElement.blur();
				modal.hide();
				
				resolve(true);
			}

		// cancelar
		function cancelar(){
			limpiarEventos();
			document.activeElement.blur();
			modal.hide();
			resolve(false);
		}	

		confirmBtn.addEventListener('click', confirmar);
		cancelBtn.addEventListener('click', cancelar);

		modalElement.addEventListener('hidden.bs.modal', cancelar);

	});

}

	function mostrarToast(mensaje){
		// actualizar mensaje
		document.getElementById('toastMessage').textContent = mensaje;

		const toastElement = document.getElementById('successToast');
		const toast = new bootstrap.Toast(toastElement, {
			autohide: true,
			delay: 4000
		});

		toast.show();
	}

	function mostrarToastError(mensaje){
		// actualizar mensaje
		document.getElementById('errorToastMessage').textContent = mensaje;

		const toastElement = document.getElementById('errorToast');
		const toast = new bootstrap.Toast(toastElement, {
			autohide: true,
			delay: 5000
		});

		toast.show();
	}


async function enviarCorreos() {
	
const seleccionados = datosFiltrados.value.filter(item => checkeds.value.includes(item.id));

		

			
	
	const datosParaEnviar = seleccionados.map(item => {

		const tradLanzamiento = { // tipo de lanzamiento
			Album: {
			French: "album",
			English: "album",
			Russian: "альбом",
			Ukrainian: "альбом",
			Korean: "앨범",
			Japanese: "アルバム",
			Spanish: "álbum"
			},
			EP: {
			French: "EP",
			English: "EP",
			Russian: "EP",
			Ukrainian: "EP",
			Korean: "EP",
			Japanese: "EP",
			Spanish: "EP"
			},
			Single: {
			French: "single",
			English: "single",
			Russian: "сингл",
			Ukrainian: "сингл",
			Korean: "싱글",
			Japanese: "シングル",
			Spanish: "sencillo"
			}

		}

	const traduccionesMedia = { // programa o revista

		Radio: {
		French: "programme",
		English: "program",
		Russian: "программа",
		Ukrainian: "програма",
		Korean: "프로그램",
		Japanese: "番組",
		Spanish: "programa"
		},
		TV: {
		French: "programme",
		English: "program",
		Russian: "программа",
		Ukrainian: "програма",
		Korean: "프로그램",
		Japanese: "番組",
		Spanish: "programa"
		},
		Magazine: {
		French: "magazine",
		English: "magazine",
		Russian: "журнал",
		Ukrainian: "журнал",
		Korean: "잡지",
		Japanese: "雑誌",
		Spanish: "revista"
		}

	}

		
		const mediaTraducido = traduccionesMedia[item.media_type]?.[item.secondary_language] || item.media_type;
		const lanzTraducido = tradLanzamiento[lancement.value]?.[item.secondary_language] || lancement.value;

		const langue = item.secondary_language;
		const elMedio= mediaTraducido;    // programa o revista
		const lanzamiento = lanzTraducido;   // tipo de lanzamiento
		const enlace = lien.value;

		function obtenerTradAsunto(lanzamiento, elMedio, enlace) {
			return{
				asunto: {
    French: `Nouveau lancement de ${lanzamiento}`,
    English: `New release of ${lanzamiento}`,
    Russian: `Новый релиз ${lanzamiento}`,
    Ukrainian: `Новий реліз ${lanzamiento}`,
    Korean: `${lanzamiento}의 새 출시`,
    Japanese: `${lanzamiento}の新リリース`,
    Spanish: `Nuevo lanzamiento de ${lanzamiento}`
  },
  mensaje: {
     French: `<p>Bonjour, il y a quelque temps, j'ai découvert votre ${elMedio} et j'aime beaucoup.<br> Je vous informe que j'ai un projet et j'aimerais savoir si je pourrais avoir une opportunité.</p><p><a href="${enlace}">${enlace}</a></p><p>Merci pour votre temps.</p>`,
    English: `<p>Hello, some time ago I discovered your ${elMedio} and I like it a lot.<br> I want to tell you I have a project and would like to know if I could have an opportunity.</p><p><a href="${enlace}">${enlace}</a></p><p>Thank you for your time.</p>`,
    Russian: `<p>Здравствуйте, некоторое время назад я узнал о вашем ${elMedio} и мне очень понравилось.<br> Хочу сообщить, что у меня есть проект, и я хотел бы узнать, есть ли возможность.</p><p><a href="${enlace}">${enlace}</a></p><p>Спасибо за ваше время.</p>`,
    Ukrainian: `<p>Привіт, деякий час тому я дізнався про ваш ${elMedio} і він мені дуже подобається.<br> Хочу повідомити, що у мене є проєкт, і я хотів би дізнатись, чи можу я мати можливість.</p><p><a href="${enlace}">${enlace}</a></p><p>Дякую за ваш час.</p>`,
    Korean: `<p>안녕하세요, 얼마 전에 귀하의 ${elMedio}를 알게 되었고 매우 좋아합니다.<br> 프로젝트가 있어 기회가 있을지 알고 싶습니다.</p><p><a href="${enlace}">${enlace}</a></p><p>시간 내주셔서 감사합니다.</p>`,
    Japanese: `<p>こんにちは、しばらく前にあなたの${elMedio}を知り、とても気に入っています.<br>プロジェクトがあり、チャンスがあるかどうか知りたいです。</p><p><a href="${enlace}">${enlace}</a></p><p>お時間をいただきありがとうございます。</p>`,
    Spanish: `<p>Hola, hace un tiempo conocí su ${elMedio} y me gusta mucho.<br> Le comento que tengo un proyecto y me gustaría saber si podría tener alguna oportunidad<br></p><p><a href="${enlace}">${enlace}</a></p><p>gracias por su tiempo.</p>`
 
  }


			};
		}

		const tradAsunto = obtenerTradAsunto(lanzamiento, elMedio, enlace);
		const asuntoEnIdioma = tradAsunto.asunto[item.secondary_language] || tradAsunto.asunto.English;
		
		
		const mensajeEnIdioma = tradAsunto.mensaje[item.secondary_language] || tradAsunto.mensaje.English;

		return {
		// 	name: item.name,
		 email: item.test_email,
		tradAsunto: asuntoEnIdioma,
		tradMensaje: mensajeEnIdioma
		
		};
	
	});

	// console.log('datos para enviar: ',  datosParaEnviar);

	const datosCampana = {
		nombreDeCampana: campagne.value,
		lanzamiento: lancement.value,
		elLink: lien.value,
		elGenero: genre.value
	}

	console.log(datosCampana);

	// HACER ARRAY DE ENVIADOS
	


	const mensaje = `¿seguro de enviar estos ${datosParaEnviar.length} correos?`;
	const confirmacion = await mostrarModalConfirmacion(mensaje);

	if(!confirmacion) {
		console.log('envio cancelado por el usuario');
		return;
	}
	
	console.log('usuaio confirmo');


enviando.value = true;

	
fetch('http://localhost/conex-prom-system/api/enviar-emails.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(datosParaEnviar)
  })
  .then(response => response.json())
  .then(data => {
    console.log('Correos enviados correctamente:', data.enviados);
    
		
		mostrarToast(`muy bien, se enviaron ${data.enviados.length} correos exitosamente`);
	
		limpiarYCargar(showSelect.value);
  })
  .catch(error => {
    console.error('Error al enviar correos:', error);
    mostrarToastError('Hubo un error al enviar los correos. Inténtalo de nuevo.');
  })
  .finally(() => {
  enviando.value = false;
});







  const direccionesEnviadas = datosParaEnviar.map(item => item.email);
    
emailsEnviados.value = [...emailsEnviados.value, ...direccionesEnviadas];

console.log('array de direcciones enviadas: ', emailsEnviados.value);
console.log('se enviaron ', emailsEnviados.value.length, ' emails');
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
	envioDeEmails: emailsEnviados.value
};
	
	console.log('los datos a enviar son: ', datosAEnviar);


	// console.log('showSelect value', showSelect.value);

if(!showSelect.value){
fetch('http://localhost/conex-prom-system/api/insert-campanas.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json' 
		},
		body: JSON.stringify(datosAEnviar)
	})

	// .then(res => res.text())
	// .then(data => {
	// 	console.log('respuesta gigante si lanzamos data', data );
	// });

} else {

	const emailsParaEnviar = {
	envioDeEmails: emailsEnviados.value,
	idCampExistente: selectedCamp.value ? selectedCamp.value.id : null
};
	fetch('http://localhost/conex-prom-system/api/insert-enviados.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json' 
		},
		body: JSON.stringify(emailsParaEnviar)
	})	


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

.caja{

	width: 345px;
	height: 333px;
	background-color: burlywood;
	color: brown;
}


.btn-group-toggle input[type="radio"] {
  display: none;
}


</style>

