<template>

<!-- <h1> ESTAMOS EN CAMPAÑAS </h1> -->

<div class="container mt-4">
	<table class="table">
		<thead class="table-dark ma-table rounded overflow-hidden">
			<tr>
				<th>Campaña</th>
				
				<th>Genero</th>
				<th>Link</th>
				<th>Fecha</th>
				<th>Firma</th>
				<th>Activa</th>
				<th>Estadisticas</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="c in [...campanas].reverse()" :key="c.id">
				<td>
					<div class="colum-campana">
				<div><h4>{{c.nombre_lanzamiento }}</h4></div>
				<div>
				<h5>{{ c.artista }} - {{ c.tipo_de_lanzamiento }}</h5>
				</div>
				</div>
			</td>
				
				<td class="align-middle">{{ c.music_genre }}</td>
				
				
				<td class="align-middle leLien"><a :href="c.enlace" target="_blank" rel="noopener noreferrer">
    			<i class="fa-brands fa-youtube"></i>
  				</a></td>
				

				
				<td class="align-middle">{{ c.fecha_creacion.split(' ')[0] }}</td>
				
				<td class="align-middle"> {{c.sender_name}} </td>

				<td class="activeColum align-middle">

					<div class="status-indicator d-flex flex-column align-items-center justify-content-center">
						
						<div v-if="c.activa == 2" class="cerrada-label">CERRADA</div>
						
						<label 
							v-if="c.activa != 2" 
							class="toggle-switch">
							<input class="slideActive" 
								type="checkbox" 
								:checked="c.activa == 1"
								@change="actualizarActiva(c)">
							<span class="slider"></span>
							</label>

						<div class="status-dot active"></div>
						<span class="status-text">{{ c.activa == 1 ? 'Activa' : 'Inactiva' }}</span>
						</div>

					</td>


				<td class="align-middle text-center">
    <i class="fa-solid fa-chart-simple" 
       role="button" 
       data-bs-toggle="modal" 
       data-bs-target="#modalEstadisticas"
       @click="cargarEstadisticasCampaña(c.id)"></i>
</td>
			</tr>
			
		</tbody>
	</table>

</div>


    <!-- modal resultados -->

    <div class="modal modal-premium-glass" id="modalEstadisticas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content text-white modal-all">
      
      <!-- <div class="modal-header">
        <h5 class="modal-title">Estadísticas</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div> -->
      
      <div class="modal-body">

        <!-- 🔷 Card con resumen -->
        <div class="card text-white mb-4 shadow-sm card-campaign">
          <div class="card-body">
            <h2 class="card-title mb-1" v-if="datosEstadisticas">
  {{ datosEstadisticas.tipo_de_lanzamiento }} - 
  {{ datosEstadisticas.nombre_lanzamiento }} - 
  {{ datosEstadisticas.artista }}
</h2>

<p v-if="datosEstadisticas" class="card-text small">
  Enviados: {{ datosEstadisticas.total_enviados }} | 
  Respuestas: {{ datosEstadisticas.total_respuestas }} | 
  %: {{ datosEstadisticas.porcentaje_respuesta }}
</p>

            <!-- <p class="card-text small">Aquí puedes mostrar KPIs rápidos como campañas activas, correos enviados, etc.</p> -->
          </div>
        </div>

        <div class="glass-section">
          <div class="row">
            <div class="col-6">
              <canvas id="graficoTorta"></canvas>
            </div>
            <div class="col-6">
              <canvas id="barrasCanvas"></canvas>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn-edark" data-bs-dismiss="modal">
          <i class="fas fa-check-circle"></i> Cerrar
        </button>
      </div>

    </div>
  </div>
</div>



</template>

<script setup>


import { ref, onMounted } from 'vue';
import { Chart } from 'chart.js/auto'



function elAlert() {
	alert('el putisimo alert');
}



const datosEstadisticas = ref(null);
const cargandoEstadisticas = ref(false);

function cargarEstadisticasCampaña(campaniaId) {
  cargandoEstadisticas.value = true;

  fetch(`http://localhost/conex-prom-system/api/chart.php?campana_id=${campaniaId}`)
    .then(response => response.json())
    .then(data => {
      datosEstadisticas.value = data;
      cargandoEstadisticas.value = false;

      // 🚀 Renderizar gráficos solo después de tener los datos
      renderGraficos();
    })
    .catch(err => {
      console.error('Error cargando estadísticas:', err);
      cargandoEstadisticas.value = false;
    });
}




const campanas = ref ([]);

const campanasTest = [
  { id: 1, nombre_lanzamiento: "Nocturne des Cimes", artista: "Ensemble Lumière", tipo_de_lanzamiento: "Álbum", music_genre: "Classical", enlace: "https://youtu.be/abc123", fecha_creacion: "2025-07-10 14:30:00", sender_name: "Claire Dumas", activa: 1 },
  { id: 2, nombre_lanzamiento: "Echoes of Han", artista: "Seo Yuna Trio", tipo_de_lanzamiento: "EP", music_genre: "Jazz", enlace: "https://youtu.be/def456", fecha_creacion: "2025-07-11 10:20:00", sender_name: "Yuna Seo", activa: 1 },
  { id: 3, nombre_lanzamiento: "Raíces del Sur", artista: "Grupo Tierra Libre", tipo_de_lanzamiento: "Single", music_genre: "World", enlace: "https://youtu.be/ghi789", fecha_creacion: "2025-07-12 18:45:00", sender_name: "Carlos Molina", activa: 0 },
  { id: 4, nombre_lanzamiento: "Весенний свет", artista: "Ансамбль Белый Ветер", tipo_de_lanzamiento: "Álbum", music_genre: "Classical", enlace: "https://youtu.be/jkl012", fecha_creacion: "2025-07-13 09:10:00", sender_name: "Irina Volnova", activa: 1 },
  { id: 5, nombre_lanzamiento: "Desierto Azul", artista: "Luz y Arena", tipo_de_lanzamiento: "EP", music_genre: "World", enlace: "https://youtu.be/mno345", fecha_creacion: "2025-07-14 12:00:00", sender_name: "Gabriela M.", activa: 2 },
  { id: 6, nombre_lanzamiento: "Moonlight Sketches", artista: "The Velvet Quintet", tipo_de_lanzamiento: "Single", music_genre: "Jazz", enlace: "https://youtu.be/pqr678", fecha_creacion: "2025-07-15 13:50:00", sender_name: "J. Hillman", activa: 1 },
  { id: 7, nombre_lanzamiento: "Montagnes Sacrées", artista: "Trio Nomade", tipo_de_lanzamiento: "Álbum", music_genre: "World", enlace: "https://youtu.be/stu901", fecha_creacion: "2025-07-16 17:20:00", sender_name: "Luc Renard", activa: 0 },
  { id: 8, nombre_lanzamiento: "Haenyeo", artista: "Nam Jisoo Project", tipo_de_lanzamiento: "EP", music_genre: "World", enlace: "https://youtu.be/vwx234", fecha_creacion: "2025-07-17 20:00:00", sender_name: "Nam Jisoo", activa: 1 },
  { id: 9, nombre_lanzamiento: "Suite para un Río", artista: "Orquesta Nuevo Ande", tipo_de_lanzamiento: "Álbum", music_genre: "Classical", enlace: "https://youtu.be/yz1234", fecha_creacion: "2025-07-18 08:25:00", sender_name: "R. Valdivia", activa: 1 },
  { id: 10, nombre_lanzamiento: "Frozen Stars", artista: "Nordic Pulse Ensemble", tipo_de_lanzamiento: "Single", music_genre: "Classical", enlace: "https://youtu.be/abc567", fecha_creacion: "2025-07-19 11:15:00", sender_name: "E. Johansen", activa: 0 },
  { id: 11, nombre_lanzamiento: "La Tarde Azul", artista: "Dúo Bruma", tipo_de_lanzamiento: "EP", music_genre: "Jazz", enlace: "https://youtu.be/def890", fecha_creacion: "2025-07-20 14:55:00", sender_name: "Lucía Barrios", activa: 2 },
  { id: 12, nombre_lanzamiento: "Зов земли", artista: "Голос Байкала", tipo_de_lanzamiento: "Álbum", music_genre: "World", enlace: "https://youtu.be/ghi321", fecha_creacion: "2025-07-21 19:30:00", sender_name: "Yelena M.", activa: 1 },
  { id: 13, nombre_lanzamiento: "Bordes", artista: "Cecilia Collazos", tipo_de_lanzamiento: "Ep", music_genre: "World music", enlace: "https://youtu.be/ghi321", fecha_creacion: "2025-07-21 19:30:00", sender_name: "Morgana Aoul.", activa: 1 },
  { id: 14, nombre_lanzamiento: "Corre y salta", artista: "Nena pioson", tipo_de_lanzamiento: "Álbum", music_genre: "Jazz", enlace: "https://youtu.be/ghi321", fecha_creacion: "2025-07-21 19:30:00", sender_name: "Alan", activa: 1 }
];

// simulacion de recibidos


  const estadisticasSimuladas = {
    enviados: 110,
    respuestas: [
      // Día 1 (15)
      '2025-07-01','2025-07-01','2025-07-01','2025-07-01','2025-07-01',
      '2025-07-01','2025-07-01','2025-07-01','2025-07-01','2025-07-01',
      '2025-07-01','2025-07-01','2025-07-01','2025-07-01','2025-07-01',

      // Día 2 (14)
      '2025-07-02','2025-07-02','2025-07-02','2025-07-02','2025-07-02',
      '2025-07-02','2025-07-02','2025-07-02','2025-07-02','2025-07-02',
      '2025-07-02','2025-07-02','2025-07-02','2025-07-02',

      // Día 3 (10)
      '2025-07-03','2025-07-03','2025-07-03','2025-07-03','2025-07-03',
      '2025-07-03','2025-07-03','2025-07-03','2025-07-03','2025-07-03',

      // Día 4 (8)
      '2025-07-04','2025-07-04','2025-07-04','2025-07-04',
      '2025-07-04','2025-07-04','2025-07-04','2025-07-04',

      // Día 5 (6)
      '2025-07-05','2025-07-05','2025-07-05','2025-07-05','2025-07-05','2025-07-05',

      // Días 6–10 (20)
      '2025-07-06','2025-07-06','2025-07-06',
      '2025-07-07','2025-07-07','2025-07-07',
      '2025-07-08','2025-07-08','2025-07-08','2025-07-08',
      '2025-07-09','2025-07-09',
      '2025-07-10','2025-07-10','2025-07-10','2025-07-10',
      '2025-07-10','2025-07-10','2025-07-10','2025-07-10',

      // Días 11–30 (19)
      '2025-07-11','2025-07-12','2025-07-12','2025-07-13','2025-07-13',
      '2025-07-14','2025-07-15','2025-07-16','2025-07-17','2025-07-18',
      '2025-07-19','2025-07-20','2025-07-21','2025-07-22','2025-07-23',
      '2025-07-24','2025-07-25','2025-07-26','2025-07-27'
    ],
    get respondidos() {
      return this.respuestas.length;
    },
    get porcentaje() {
      return Math.round((this.respondidos / this.enviados) * 100);
    }
  };

  const respuestasPorDia = estadisticasSimuladas.respuestas.reduce((acc, fecha) => {
    acc[fecha] = (acc[fecha] || 0) + 1;
    return acc;
  }, {});



// Variables globales para los gráficos
let graficoDonaInstance = null;
let graficoBarrasInstance = null;

function renderGraficos() {
  // Destruir gráficos existentes si existen
  if (graficoDonaInstance) {
    graficoDonaInstance.destroy();
  }
  if (graficoBarrasInstance) {
    graficoBarrasInstance.destroy();
  }

  const canvasTorta = document.getElementById('graficoTorta');
  const ctxTorta = canvasTorta.getContext('2d');

  const styles = getComputedStyle(document.documentElement);
  const darkEcho = styles.getPropertyValue('--dark-echo').trim();
  const mediumEcho = styles.getPropertyValue('--medium-echo').trim();
  const lightEcho = styles.getPropertyValue('--light-echo').trim();

  // Gráfico de dona
  graficoDonaInstance = new Chart(ctxTorta, {
    type: 'doughnut',
    data: {
      labels: ['Enviados', 'Respondidos'],
      datasets: [{
        data: [
          datosEstadisticas.value.total_enviados,
          datosEstadisticas.value.total_respuestas
        ],
        backgroundColor: [darkEcho, lightEcho],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        animateRotate: true,
        animateScale: true,
        duration: 1200,
        easing: 'easeOutQuart'
      },
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 20,
            color: lightEcho
          }
        }
      }
    }
  });

  // Gráfico de barras
  const canvasBarras = document.getElementById('barrasCanvas');
  const ctxBarras = canvasBarras.getContext('2d');

  const fechas = Object.keys(datosEstadisticas.value.respuestas_por_fecha);
  const cantidades = Object.values(datosEstadisticas.value.respuestas_por_fecha);

  graficoBarrasInstance = new Chart(ctxBarras, {
    type: 'bar',
    data: {
      labels: fechas,
      datasets: [{
        label: 'Respuestas por fecha',
        data: cantidades,
        backgroundColor: mediumEcho,
        borderColor: lightEcho,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: lightEcho }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: lightEcho }
        },
        x: {
          ticks: { color: lightEcho }
        }
      }
    }
  });
}


  // console.log({
  //   enviados: estadisticasSimuladas.enviados,
  //   respondidos: estadisticasSimuladas.respondidos,
  //   porcentaje: estadisticasSimuladas.porcentaje,
  //   respuestasPorDia
  // });


		// fin de simulacion




onMounted(()  =>{
  fetch('http://localhost/conex-prom-system/api/conex-campanas.php')
  .then(response => {
    if(!response.ok) {
      throw new Error ('Error al traer campañas: ' + response.status);
    }
    return response.json();
  })
  .then(json => {
    console.log('campañas cargadas: ', json);
    campanas.value = json;
  })
  .catch(err => {
    console.error('Error en la respuesta de campañas: ', err);
  });
});


function actualizarActiva(campana) {
	const nuevoEstado = campana.activa == 1 ? 0 : 1;

	console.log('Enviando:', { id: campana.id, activa: nuevoEstado });


	fetch('http://localhost/conex-prom-system/api/actualizar_estado.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({
			id: campana.id,
			activa: nuevoEstado
		})

	})
	.then(res => res.json())
	.then(data => {
	if (data.success) {
		campana.activa = nuevoEstado;
	} else {
		alert('Error al enviar estado');
	}
})

	.catch(err => {
    console.error('Error en actualización:', err);
  });
}

// onMounted(() => {
//   const modal = document.getElementById('modalEstadisticas');
//   modal.addEventListener('shown.bs.modal', () => {
//     renderGraficos();
//   });
// });



</script>

<style scoped>

/* :root {
  --dark-echo: #374d5f;
  --medium-echo: #6f8ba2;
  --light-echo: #cbd5df;
  --cta-echo: rgb(102, 29, 29);
  --rojo-echo: #aa1a1a;
  --bright-echo: rgba(242, 252, 250, 0.774);
  --confirm-echo: #1e5329;
} */


.table{
	border-radius: 12px;
  overflow: hidden;
  box-shadow: 2px -4px 28px var(--dark-echo);
}

 .ma-table{
	padding: 0;
	
	
}


	/* encabezado */
th{
	background-color: var(--dark-echo) !important;
	color: var(--light-echo) !important;
	padding: 10px;
	font-size: 1.2rem;
	
}

	/*filas*/

tr {
  padding: 0;
  background: linear-gradient(90deg, var(--dark-echo) 35%, var(--medium-echo) 100%);
  transition: background 0.6s ease;
}

tr:hover {
  background: linear-gradient(90deg, var(--dark-echo) 15%, var(--medium-echo) 100%);
}




td, .status-text{
	background-color: rgba(39, 38, 38, 0.068);
	color: var(--light-echo);
	padding: 8px 8;
	font-size: 1.2em;
	border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.colum-campana{
	/* border: 1px solid rgba(153, 205, 50, 0.397); */
	padding-left: 16px;
}

h4{
	color: var(--bright-echo);
	font-size: 1em;
}

h5{
	color: var(--medium-echo);
	font-size: .9rem;
}

a{
	/* color: var(--rojo-echo); */
	font-size: 1.5em;
	color: var(--light-echo);
	transition: color 0.3s ease;
}

/* a:hover{
	color: var(--cta-echo);
	text-decoration: none;
} */



.status-text{
	background-color: none;
}

.activeColum{
	padding-inline: 1px;	
}




.slideActive{
	width: 60px;

}


.status-indicator {
  position: relative; /* esto es clave */
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 60px; /* puedes ajustar según tu diseño */
}

.cerrada-label {
  position: absolute;
  top: 12px;
  left: 50%;
  transform: translateX(-50%) rotate(-15deg);
  background-color: var(--rojo-echo)  !important;
  color: rgb(226, 228, 222);
  font-size: 1rem;
  font-weight: bold;
  padding: 4px 10px;
  border: 2px solid white;
  border-radius: 5px;
  box-shadow: 0 0 6px rgba(255, 0, 0, 0.6);
  pointer-events: none;
  z-index: 3;
}




.slider{
	background-color: rgba(0, 255, 255, 0.048);
	color: black;
}

/* glass */




.modal-premium-glass {
  backdrop-filter: blur(3px) saturate(120%) !important;
  background-color: rgba(8, 47, 56, 0.3) !important;
}

.modal-premium-glass .modal-content {
  background: rgba(55, 77, 95, 0.1) !important;
  backdrop-filter: blur(25px) brightness(1.1);
  border: 1px solid rgba(242, 252, 250, 0.2);
  border-radius: 25px;
  box-shadow: 
    0 30px 60px rgba(0, 0, 0, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.modal-premium-glass .card-campaign {
  background: rgba(242, 252, 250, 0.05);
  border: 1px solid rgba(203, 213, 223, 0.3);
  border-radius: 20px;
  backdrop-filter: blur(10px);
  box-shadow: inset 0 2px 10px rgba(255, 255, 255, 0.1);
  text-align: center;
}

.modal-premium-glass .glass-section {
  background: rgba(111, 139, 162, 0.1);
  border-radius: 15px;
  padding: 20px;
  margin: 15px 0;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(203, 213, 223, 0.2);
}

#estadisticasCanvas {
  width: 100% !important;
  min-height: 400px !important;
  border: 1px solid rgba(211, 18, 18, 0.932);
  
}

.modal-all{
  
  padding: 20px;
  padding: 30px;
 
}

/* no scroll */

.modal-premium-glass {
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.modal-premium-glass::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
}

.modal-premium-glass .modal-dialog {
  margin: 1.75rem auto !important;
  transform: none !important;
}

.modal-premium-glass .modal-content {
  overflow: auto !important;
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.modal-premium-glass .modal-content::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
}

/* fin no scroll */

.modal {
  padding-right: 0 !important;
}

h2.card-title {
  color: var(--bright-echo);
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.div-donna {
  margin-bottom: 2rem;
  height: 260px; /* más grande para que la dona luzca */
  display: flex;
  align-items: center;
  justify-content: center;
}

.div-donna,
#graficoTorta {
  animation: none !important;
  transition: none !important;
  transform: none !important;
  opacity: 1 !important;
}

/* Nuevos contenedores para gráficos lado a lado */
.chart-container-dona, 
.chart-container-barras {
  height: 350px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
}

#graficoTorta {
  width: 100% !important;
  height: 100% !important;
  max-height: 320px;
}

#barrasCanvas {
  width: 100% !important;
  height: 100% !important;
  max-height: 320px;
}

/* Responsive para tablets y móviles */
@media (max-width: 991px) {
  .chart-container-dona, 
  .chart-container-barras {
    height: 280px;
    margin-bottom: 20px;
  }
  
  .modal-dialog {
    margin: 15px !important;
  }
}

.btn-edark {
  background: var(--dark-echo);
  color: var(--light-echo);
  border: 1px solid var(--medium-echo);
  padding: 8px 20px;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-edark:hover {
  background: var(--medium-echo);
  color: white;
  transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
  .modal-dialog {
    margin: 10px !important;
  }
  
  .chart-container-dona, 
  .chart-container-barras {
    height: 250px;
  }
}

@media (max-width: 576px) {
  .chart-container-dona, 
  .chart-container-barras {
    height: 220px;
    padding: 10px;
  }
}

</style>