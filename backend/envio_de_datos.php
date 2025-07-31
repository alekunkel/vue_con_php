<?php
include 'conexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->nombre) && !empty(trim($data->nombre)) &&
    isset($data->apellido) && !empty(trim($data->apellido)) &&
    isset($data->materia) && !empty(trim($data->materia))
) {
    $nombre = $conn->real_escape_string(trim($data->nombre));
    $apellido = $conn->real_escape_string(trim($data->apellido));
    $materia = $conn->real_escape_string(trim($data->materia));
    $condicion = isset($data->condicion) ? $conn->real_escape_string($data->condicion) : 'sinmarcar';

    $sql = "INSERT INTO docentes (nombre, apellido, materia, condicion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $materia, $condicion);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Docente guardado con éxito", "id" => $conn->insert_id]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Error al guardar el docente: " . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Datos incompletos para guardar el docente."]);
}

$conn->close();
?>