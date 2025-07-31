<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); // Asegúrate de que OPTIONS esté aquí
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$host = "localhost";  
$user = "root";  
$password = "";  
$dbname = "escuela";  
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}