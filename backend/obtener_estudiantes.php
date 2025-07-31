<?php
include 'conexion.php';

$sql = "SELECT id, nombre, curso, materia, aprobado, desaprobado FROM estudiantes";
$resultado = $conn->query($sql);

$estudiantes = [];
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        if ($fila['aprobado'] == 1) {
            $fila['condicion'] = 'aprobado';
        } elseif ($fila['desaprobado'] == 1) {
            $fila['condicion'] = 'desaprobado';
        } else {
            $fila['condicion'] = 'sinmarcar';
        }

        $fila['nombre_apellido'] = $fila['nombre'];
        
        unset($fila['nombre']);
        unset($fila['aprobado']);
        unset($fila['desaprobado']);
        
        $estudiantes[] = $fila;
    }
}

echo json_encode($estudiantes);

$conn->close();
?>