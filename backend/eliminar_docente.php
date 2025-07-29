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
header("Access-Control-Allow-Methods: POST, OPTIONS"); // POST para eliminar, OPTIONS para pre-vuelo CORS
// Define las cabeceras que el cliente puede enviar.
header("Access-Control-Allow-Headers: Content-Type");
// Establece la duración en segundos durante la cual la respuesta del pre-vuelo CORS puede ser cacheada.
header("Access-Control-Max-Age: 86400"); // 24 horas

// Manejo de solicitudes OPTIONS (pre-vuelo CORS)
// Los navegadores envían una solicitud OPTIONS antes de una solicitud POST/PUT/DELETE
// para verificar si el servidor permite la solicitud.
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
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]);
    exit();
}

// --- Obtener datos de la solicitud ---
// Obtener el cuerpo de la solicitud (JSON)
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true); // Decodificar a un array asociativo

// Verificar si la decodificación JSON fue exitosa
if ($json_data === false || $data === null) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Formato de datos JSON inválido o vacío."]);
    exit();
}

// --- Validación de datos ---
// Verificar si se recibió el ID y si es un número entero válido
if (!isset($data['id']) || !is_numeric($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "ID no proporcionado o formato inválido."]);
    exit();
}

$id = intval($data['id']); // Asegura que sea un entero

// --- Preparar y ejecutar la consulta SQL para eliminar ---
// Usar sentencias preparadas para prevenir inyección SQL
$stmt = $conexion->prepare("DELETE FROM docentes WHERE id = ?");

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error al preparar la consulta: " . $conexion->error]);
    exit();
}

// "i" indica que el parámetro es un entero (integer)
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Verificar si se eliminó alguna fila
    if ($stmt->affected_rows > 0) {
        http_response_code(200); // OK
        echo json_encode(["mensaje" => "Docente eliminado correctamente."]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["mensaje" => "No se encontró ningún docente con el ID proporcionado."]);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error al ejecutar la eliminación: " . $stmt->error]);
}

// --- Cerrar la sentencia y la conexión a la base de datos ---
$stmt->close();
$conexion->close();
?>