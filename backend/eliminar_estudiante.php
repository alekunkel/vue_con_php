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

$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID del estudiante no proporcionado."]);
    $conn->close();
    exit;
}

$sql = "DELETE FROM estudiantes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["mensaje" => "Estudiante eliminado correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar el estudiante: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>