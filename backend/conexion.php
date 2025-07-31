<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); // Asegúrate de que OPTIONS esté aquí
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Establecer el código de estado HTTP a 200 OK para el preflight
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