<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "escuela";

header("Access-Control-Allow-Origin: *"); // Permite solicitudes desde cualquier origen
header("Content-Type: application/json; charset=UTF-8"); // Indica que la respuesta será JSON
header("Access-Control-Allow-Methods: POST"); // Permite solo el método POST
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Cabeceras permitidas

// Crear la conexión a la base de datos
$conexion = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    http_response_code(500); // Error interno del servidor
    echo json_encode(["message" => "Error de conexión a la base de datos: " . $conexion->connect_error]);
    exit();
}

// Recibir el JSON crudo del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"), true);

// Validar si los datos recibidos son válidos
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Datos de entrada no válidos o JSON mal formado."]);
    exit();
}

if (
    !isset($data["nombre"]) ||
    !isset($data["apellido"]) ||
    !isset($data["materia"]) ||
    !isset($data["condicion"])  
) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Faltan campos obligatorios."]);
    exit();
}

// Sanitizar los datos (¡IMPORTANTE para seguridad!)
$nombre = htmlspecialchars(strip_tags($data["nombre"]));
$apellido = htmlspecialchars(strip_tags($data["apellido"]));
$materia = htmlspecialchars(strip_tags($data["materia"]));
$condicion = htmlspecialchars(strip_tags($data["condicion"])); // Sanitizar la condición también

$stmt = $conexion->prepare("INSERT INTO docentes (nombre, apellido, materia, condicion) VALUES (?, ?, ?, ?)");

// 'ssss' indica que todos los parámetros son strings
$stmt->bind_param("ssss",
    $nombre,
    $apellido,
    $materia,
    $condicion
);

if ($stmt->execute()) {
    $newId = $conexion->insert_id;
    http_response_code(201); // Created
    echo json_encode([
        "message" => "Docente guardado correctamente.",
        "id" => $newId, // ¡Devuelve el ID!
        "nombre" => $nombre, // También puedes devolver los datos guardados para confirmación
        "apellido" => $apellido,
        "materia" => $materia,
        "condicion" => $condicion
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Error al guardar el docente: " . $stmt->error]);
}

// Cerrar la declaración y la conexión
$stmt->close();
$conexion->close();

?>