	<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const estudiante = ref({
  nombre_apellido: '',
  curso: '',
  materia: '',
  condicion: 'sinmarcar', 
})

const estudiantes = ref([])

const mensajeExito = ref(null)
const mensajeError = ref(null)

const URL_BACKEND = 'http://localhost/formulario/backend'

onMounted(async () => {
  try {
    const response = await axios.get(`${URL_BACKEND}/obtener_estudiantes.php`) 
    estudiantes.value = response.data
  } catch (error) {
    console.error('Error al obtener estudiantes:', error)
    mensajeError.value = 'No se pudieron cargar los estudiantes. Intentá recargar la página.'
  }
})

async function guardarEstudiante() {
  if (!estudiante.value.nombre_apellido || !estudiante.value.curso || !estudiante.value.materia) {
    mensajeError.value = 'Por favor, completá todos los campos requeridos.'
    return
  }

  try {
    const response = await axios.post(`${URL_BACKEND}/envio_de_datos.php`, estudiante.value)

    const nuevoEstudiante = {
      ...estudiante.value,
      id: response.data.id ?? Date.now()
    }

    estudiantes.value.push(nuevoEstudiante) 

    mensajeExito.value = 'Estudiante guardado con éxito!'
    mensajeError.value = null

    estudiante.value = {
      nombre_apellido: '',
      curso: '',
      materia: '',
      condicion: 'sinmarcar',
    }
  } catch (error) {
    console.error('Error al guardar estudiante:', error)
    mensajeError.value = 'Error al guardar el estudiante. Verificá la conexión y los datos.'
  }
}

function eliminarEstudiante(id) {
  if (!confirm('¿Estás seguro de que querés eliminar este estudiante?')) return

  mensajeExito.value = null
  mensajeError.value = null

  axios.post(`${URL_BACKEND}/eliminar_estudiante.php`, { id })
    .then(() => {
      estudiantes.value = estudiantes.value.filter(est => est.id !== id)
      mensajeExito.value = 'Estudiante eliminado correctamente.'
    })
    .catch(error => {
      console.error('Error al eliminar estudiante:', error)
      mensajeError.value = 'Error al eliminar el estudiante. Intentá de nuevo.'
    })
}
</script>

<template>
  <h1>Formulario de Estudiantes</h1>
  <form @submit.prevent="guardarEstudiante">
    <label for="nombre_apellido">Nombre y Apellido:</label>
    <input type="text" id="nombre_apellido" v-model="estudiante.nombre_apellido" required />
<br>
    <label for="curso">Curso:</label>
    <input type="text" id="curso" v-model="estudiante.curso" required />
<br>
    <label for="materia">Materia:</label>
    <input type="text" id="materia" v-model="estudiante.materia" required />

    <div class="radio-group">
      <label>
        <input type="radio" value="aprobado" v-model="estudiante.condicion" />
        Aprobado
      </label>
      <label>
        <input type="radio" value="desaprobado" v-model="estudiante.condicion" />
        Desaprobado
      </label>
    </div>

    <button type="submit">Guardar</button>
  </form>

  <p v-if="mensajeError" class="mensaje error">{{ mensajeError }}</p>
  <p v-if="mensajeExito" class="mensaje exito">{{ mensajeExito }}</p>

  <hr />

  <h2>Lista de Estudiantes</h2>
  <table>
    <thead>
      <tr>
        <th>Nombre y Apellido</th>
        <th>Curso</th>
        <th>Materia</th>
        <th>Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="estudiante in estudiantes" :key="estudiante.id">
        <td>{{ estudiante.nombre_apellido }}</td>
        <td>{{ estudiante.curso }}</td>
        <td>{{ estudiante.materia }}</td>
        <td>
          <span v-if="estudiante.condicion === 'aprobado'">Aprobado</span>
          <span v-else-if="estudiante.condicion === 'desaprobado'">Desaprobado</span>
          <span v-else>Sin marcar</span>
        </td>
        <td>
          <button class="eliminar" @click="eliminarEstudiante(estudiante.id)">Eliminar</button>
        </td>
      </tr>
      <tr v-if="estudiantes.length === 0">
        <td colspan="5" style="text-align: center;">No hay estudiantes registrados.</td>
      </tr>
    </tbody>
  </table>
</template>