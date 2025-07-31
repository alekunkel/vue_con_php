<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const docente = ref({
  nombre: '',
  apellido: '',
  materia: '',
  condicion: 'sinmarcar',
})

const docentes = ref([])

const mensajeExito = ref(null)
const mensajeError = ref(null)

const URL_BACKEND = 'http://localhost/formulario/backend'

// Obtener docentes al montar
onMounted(async () => {
  try {
    const response = await axios.get(`${URL_BACKEND}/obtener_docentes.php`)
    docentes.value = response.data
  } catch (error) {
    console.error('Error al obtener docentes:', error)
    mensajeError.value = 'No se pudieron cargar los docentes. Intentá recargar la página.'
  }
})

// Guardar docente
async function guardarDocente() {
  if (!docente.value.nombre || !docente.value.apellido || !docente.value.materia) {
    mensajeError.value = 'Por favor, completá todos los campos requeridos.'
    return
  }

  try {
    const response = await axios.post(`${URL_BACKEND}/envio_de_datos.php`, docente.value)

    const nuevoDocente = {
      ...docente.value,
      id: response.data.id ?? Date.now()
    }

    docentes.value.push(nuevoDocente)

    mensajeExito.value = 'Docente guardado con éxito!'
    mensajeError.value = null

    // Resetear formulario
    docente.value = {
      nombre: '',
      apellido: '',
      materia: '',
      condicion: 'sinmarcar',
    }
  } catch (error) {
    console.error('Error al guardar docente:', error)
    mensajeError.value = 'Error al guardar el docente. Verificá la conexión y los datos.'
  }
}

// Eliminar docente
function eliminarDocente(id) {
  if (!confirm('¿Estás seguro de que querés eliminar este docente?')) return

  mensajeExito.value = null
  mensajeError.value = null

  axios.post(`${URL_BACKEND}/eliminar_docente.php`, { id })
    .then(() => {
      docentes.value = docentes.value.filter(doc => doc.id !== id)
      mensajeExito.value = 'Docente eliminado correctamente.'
    })
    .catch(error => {
      console.error('Error al eliminar docente:', error)
      mensajeError.value = 'Error al eliminar el docente. Intentá de nuevo.'
    })
}
</script>

<template>
  <h1>Formulario</h1>
  <form @submit.prevent="guardarDocente">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" v-model="docente.nombre" required />
<br>
    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" v-model="docente.apellido" required />
<br>
    <label for="materia">Materia:</label>
    <input type="text" id="materia" v-model="docente.materia" required />

    <div class="radio-group">
      <label>
        <input type="radio" value="entregada" v-model="docente.condicion" />
        Entregada
      </label>
      <label>
        <input type="radio" value="noentregada" v-model="docente.condicion" />
        No entregada
      </label>
    </div>

    <button type="submit">Guardar</button>
  </form>

  <p v-if="mensajeError" class="mensaje error">{{ mensajeError }}</p>
  <p v-if="mensajeExito" class="mensaje exito">{{ mensajeExito }}</p>

  <hr />

  <h2>Lista de Docentes</h2>
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Materia</th>
        <th>Condición</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="docente in docentes" :key="docente.id">
        <td>{{ docente.nombre }}</td>
        <td>{{ docente.apellido }}</td>
        <td>{{ docente.materia }}</td>
        <td>
          <span v-if="docente.condicion === 'entregada'">Entregado</span>
          <span v-else-if="docente.condicion === 'noentregada'">No entregado</span>
          <span v-else>Sin marcar</span>
        </td>
        <td>
          <button class="eliminar" @click="eliminarDocente(docente.id)">Eliminar</button>
        </td>
      </tr>
      <tr v-if="docentes.length === 0">
        <td colspan="5" style="text-align: center;">No hay docentes registrados.</td>
      </tr>
    </tbody>
  </table>
</template>
