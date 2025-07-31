<?php
include 'conexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && !empty(trim($data->id))) {
    $id = $conn->real_escape_string(trim($data->id));

    $sql = "DELETE FROM docentes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); 

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Docente eliminado correctamente."]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["message" => "Docente con ID " . $id . " no encontrado."]);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Error al eliminar el docente: " . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "ID de docente no proporcionado."]);
}

$conn->close();
?>