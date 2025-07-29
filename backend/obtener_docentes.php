<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "escuela";

// --- Configuración de CORS ---
// Permite que cualquier origen (dominio) acceda a este script.
// En producción, es recomendable restringirlo a los dominios específicos de tu frontend.
header("Access-Control-Allow-Origin: *");
// Define los métodos HTTP permitidos (GET, POST, OPTIONS, etc.).
// Para obtener datos, principalmente necesitamos GET.
header("Access-Control-Allow-Methods: GET, OPTIONS");
// Define las cabeceras que el cliente puede enviar.
header("Access-Control-Allow-Headers: Content-Type");
// Establece la duración en segundos durante la cual la respuesta del pre-vuelo CORS puede ser cacheada.
header("Access-Control-Max-Age: 86400"); // 24 horas

// Manejo de solicitudes OPTIONS (pre-vuelo CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Responde con éxito para el pre-vuelo
    exit();
}

// --- Configuración de cabeceras de respuesta ---
// Asegura que el contenido sea JSON y use UTF-8.
header("Content-Type: application/json; charset=UTF-8");

// --- Conexión a la base de datos ---
$conexion = new mysqli($host, $user, $password, $dbname);

// Manejo de errores de conexión
if ($conexion->connect_error) {
    http_response_code(500); // Internal Server Error
    // Devolver un JSON de error en lugar de un 'die()' para que el frontend pueda manejarlo.
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]);
    exit();
}

// --- Consulta de docentes ---
// Es crucial incluir el 'id' en la consulta, ya que tu frontend lo usa para la clave ":key"
// y para las operaciones de eliminación.
// Además, la lógica de tu frontend espera una propiedad 'condicion',
// no 'entregada' y 'noentregado' por separado.
// Asumiendo que 'entregada' y 'noentregado' son booleanos (0/1) o similares,
// se debe mapear a la cadena 'entregada', 'noentregada' o 'sinmarcar'.
// Para simplificar, si la condición se guarda como un string en la DB, sería más directo.
// Si son columnas separadas, necesitarás una lógica para combinarlas o tener una columna 'condicion'.
// Por ahora, asumiré que hay una columna 'condicion' o mapearé si las tuyas son booleanas.

// Opción 1: Si tienes una columna 'condicion' en la DB que guarda 'entregada', 'noentregada', 'sinmarcar'
$sql = "SELECT id, nombre, apellido, materia, condicion FROM docentes";


$resultado = $conexion->query($sql);

if ($resultado === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error al ejecutar la consulta: " . $conexion->error]);
    $conexion->close();
    exit();
}

$docentes = [];

while ($fila = $resultado->fetch_assoc()) {
    // Si tu DB guarda 'entregada' y 'noentregado' como flags, necesitarías esta lógica:
    // $condicion = 'sinmarcar';
    // if (isset($fila['entregada']) && $fila['entregada'] == 1) {
    //     $condicion = 'entregada';
    // } elseif (isset($fila['noentregado']) && $fila['noentregado'] == 1) {
    //     $condicion = 'noentregada';
    // }
    // $fila['condicion'] = $condicion; // Añadir la propiedad 'condicion'

    $docentes[] = $fila;
}

// Devolver como JSON con código 200 OK
http_response_code(200);
echo json_encode($docentes);

// --- Cerrar la conexión a la base de datos ---
$conexion->close();
?>