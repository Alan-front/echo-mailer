<template>

<h1> ESTAMOS EN CAMPAÑAS </h1>

<div class="container mt-4">
	<table class="table">
		<thead class="table-dark ma-table">
			<tr>
				<th>Campaña</th>
				
				<th>genero</th>
				<th>link</th>
				<th>fecha</th>
				<th>firma</th>
				<th>activa</th>
				<th>estadisticas</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="c in [...campanas].reverse()" :key="c.id">
				<td><div>
				<div><h4>{{c.nombre_lanzamiento }}</h4></div>
				<div>
				{{ c.artista }} - {{ c.tipo_de_lanzamiento }}
				</div>
				</div></td>
				
				<td>{{ c.music_genre }}</td>
				
				
				<td><a :href="c.enlace" target="_blank" rel="noopener noreferrer">
    			<i class="fa-brands fa-youtube"></i>
  				</a></td>
				

				
				<td>{{ c.fecha_creacion.split(' ')[0] }}</td>
				
				<td> {{c.sender_name}} </td>

				<td class="activeColum">
					<div class="status-indicator">
                                <label class="toggle-switch">
                                    <input class="slideActive" 
									type="checkbox" 
									:checked="c.activa == 1"
									:disabled="c.activa == 2"
									@change="actualizarActiva(c)"
									>
                                    <span class="slider"></span>
                                </label>
                                <div class="status-dot active"></div>
                                <span class="status-text">{{ c.activa == 1 ? 'Activa' : 'Inactiva' }}</span>
                            </div>
					</td>


				<td><i class="fas fa-chart-line"></i>
				</td>
			</tr>
			
		</tbody>
	</table>

</div>


</template>

<script setup>
import { ref, onMounted } from 'vue';


function elAlert() {
	alert('el putisimo alert');
}



const campanas = ref ([]);

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

</script>

<style scoped>

th{
	background-color: rgba(226, 223, 218, 0.205) !important;
	color: antiquewhite;
	padding: 20px 10px;
	font-size: 1.3rem;
}

tr:hover{
	background-color: rgba(63, 61, 61, 0.349);
	cursor: pointer;
	transition: background-color 0.3s ease;

}

td{
	background-color: rgba(39, 38, 38, 0.068);
	color: antiquewhite;
	padding-block: 20px;
	font-size: 1.2em;
}

a{
	color: antiquewhite;
}

.activeColum{
	padding-inline: 1px;
	border: 1px solid red;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 140px;
}

.status-indicator{
	border: 1px solid yellow;
	width: 100%;
	display: flex;
	justify-content: space-between;
}

.slideActive{
	width: 60px;

}

/* Mostrar letrero "CERRADA" encima del switch cuando está deshabilitado */
input.slideActive:disabled + .slider::after {
  content: "CERRADA";
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: crimson;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 4px;
  white-space: nowrap;
  z-index: 10;
}


span{
	background-color: rgba(0, 255, 255, 0.048);
	color: black;
}

</style>