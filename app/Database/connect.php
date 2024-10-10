<?php
// Conectar ao banco de dados NÃO ESQUEÇA DE MUDAR O LOCALHOST <--
$servername = "localhost:3306";
$username = "root";
$password = "etec2023";
$database = "enfermagem";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}