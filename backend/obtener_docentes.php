<?php
include 'conexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$sql = "SELECT id, nombre, apellido, materia, condicion FROM docentes ORDER BY id DESC";
$result = $conn->query($sql);

$docentes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $docentes[] = $row;
    }
}

echo json_encode($docentes);

$conn->close();
?>