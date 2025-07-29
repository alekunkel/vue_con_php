<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Datos del formulario
const profesor = ref({
  nombre: '',
  apellido: '',
  materia: '',
  condicion: 'sinmarcar', // 'entregada', 'noentregada', 'sinmarcar'
})

// Lista de docentes
const profesores = ref([])

const mensajeExito = ref(null)
const mensajeError = ref(null)

const URL_BACKEND = 'http://localhost/formulario/backend'

onMounted(async () => {
  try {
    const response = await axios.get(`${URL_BACKEND}/obtener_docentes.php`)
    profesores.value = response.data
  } catch (error) {
    console.error('Error al obtener docentes:', error)
    mensajeError.value = 'No se pudieron cargar los docentes. Intentá recargar la página.'
  }
})

// Guardar docente
async function guardarDocente() {
  mensajeExito.value = null
  mensajeError.value = null

  // Validación básica del formulario
  if (
    !profesor.value.nombre.trim() ||
    !profesor.value.apellido.trim() ||
    !profesor.value.materia.trim()
  ) {
    mensajeError.value = 'Por favor, completá todos los campos requeridos (Nombre, Apellido, Materia).'
    return
  }

  try {
    const response = await axios.post(`${URL_BACKEND}/envio_de_datos.php`, profesor.value)

    // Asumiendo que el backend devuelve el objeto completo o el ID del nuevo registro
    if (response.data && response.data.id) {
      profesores.value.push({ ...profesor.value, id: response.data.id })
    } else {
      // Fallback si el backend no devuelve el ID, aunque es preferible que lo haga
      profesores.value.push({ ...profesor.value, id: Date.now() })
    }

    mensajeExito.value = 'Docente guardado con éxito!'

    // Resetear los campos del formulario
    profesor.value.nombre = ''
    profesor.value.apellido = ''
    profesor.value.materia = ''
    profesor.value.condicion = 'sinmarcar'
  } catch (error) {
    console.error('Error al guardar docente:', error)
    mensajeError.value = 'Error al guardar el docente. Por favor, verificá la conexión y los datos.'
  }
}

// Eliminar docente
function eliminarDocente(id) {
  if (!confirm('¿Estás seguro de que querés eliminar este docente? Esta acción no se puede deshacer.')) {
    return
  }

  mensajeExito.value = null
  mensajeError.value = null

  axios.post(`${URL_BACKEND}/eliminar_docente.php`, { id })
    .then(() => {
      profesores.value = profesores.value.filter(docente => docente.id !== id)
      mensajeExito.value = 'Docente eliminado correctamente.'
    })
    .catch(error => {
      console.error('Error al eliminar docente:', error)
      mensajeError.value = 'Error al eliminar el docente. Por favor, intentá de nuevo.'
    })
}
</script>

<template>
  <h1>Formulario</h1>
  <form @submit.prevent="guardarDocente">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" v-model="profesor.nombre" required />

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" v-model="profesor.apellido" required />

    <label for="materia">Materia:</label>
    <input type="text" id="materia" v-model="profesor.materia" required />

    <div class="radio-group">
      <label>
        <input type="radio" value="entregada" v-model="profesor.condicion" />
        Entregada
      </label>
      <label>
        <input type="radio" value="noentregada" v-model="profesor.condicion" />
        No entregada
      </label>  
    </div>

    <button type="submit">Guardar</button>
  </form>

  <p v-if="mensajeError" class="mensaje error">{{ mensajeError }}</p>
  <p v-if="mensajeExito" class="mensaje exito">{{ mensajeExito }}</p>

  <hr>

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
      <tr v-for="docente in profesores" :key="docente.id">
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
      <tr v-if="profesores.length === 0">
        <td colspan="5" style="text-align: center;">No hay docentes registrados.</td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
/* Estilos existentes */
h1 {
  font-size: 24px;
  margin-bottom: 20px;
  color: #333;
}

h2 {
  font-size: 20px;
  margin-top: 30px;
  margin-bottom: 15px;
  color: #333;
}

form {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  margin-bottom: 30px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #555;
}

input[type="text"] {
  width: 100%;
  padding: 8px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.radio-group label {
  display: inline-flex;
  align-items: center;
  margin-right: 15px;
  margin-bottom: 15px;
  font-weight: normal; /* Para que no sea tan bold como los otros labels */
}

input[type="radio"] {
  margin-right: 8px;
  transform: scale(1.2);
}

button {
  background-color: #4caf50;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #45a049;
}

button.eliminar {
  background-color: #f44336;
}

button.eliminar:hover {
  background-color: #d32f2f;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  margin-top: 20px;
}

th,
td {
  padding: 10px;
  border: 1px solid #ccc;
  text-align: left;
}

th {
  background-color: #4caf50;
  color: white;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

tr:hover {
  background-color: #e0f7fa;
}

.mensaje {
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
  font-weight: bold;
}

.mensaje.error {
  background-color: #ffe0e0;
  color: #d32f2f;
  border: 1px solid #d32f2f;
}

.mensaje.exito {
  background-color: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #2e7d32;
}

hr {
  border: 0;
  height: 1px;
  background: #ccc;
  margin: 40px 0;
}
</style>