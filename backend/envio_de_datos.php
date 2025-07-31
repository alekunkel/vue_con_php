<?php
include 'conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => "JSON no válido"]);
    $conn->close();
    exit;
}

$nombre = $data['nombre_apellido'] ?? null;
$curso = $data['curso'] ?? null;
$materia = $data['materia'] ?? null;
$condicion = $data['condicion'] ?? 'sinmarcar';

if (!$nombre || !$curso || !$materia) {
    http_response_code(400);
    echo json_encode(["error" => "Todos los campos son obligatorios."]);
    $conn->close();
    exit;
}

$aprobado = 0;
$desaprobado = 0;

if ($condicion === 'aprobado') {
    $aprobado = 1;
} elseif ($condicion === 'desaprobado') {
    $desaprobado = 1;
}

$sql = "INSERT INTO estudiantes (nombre, curso, materia, aprobado, desaprobado) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $nombre, $curso, $materia, $aprobado, $desaprobado);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Estudiante guardado con éxito!", "id" => $conn->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al guardar el estudiante: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>