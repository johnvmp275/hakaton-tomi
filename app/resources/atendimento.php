<?php 
include("app/Database/connect.php");
$pessoa_id = $_GET["idUser"];
echo $pessoa_id;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar os dados enviados via POST
    
    $altura = $_POST['altura'] ?? null;
    $peso = $_POST['peso'] ?? null;
    $tipo_sanguineo = $_POST['tipo_sanguineo'];
    $pressao = $_POST['pressao'] ?? null;
    $data_atendimento = $_POST['data_atendimento'];
    $localAtendimento = $_POST['localAtendimento'];
    $atendente_nome = $_POST['atendente_nome'];
    $observacao = $_POST['observacao'] ?? null;

    // Preparar a consulta SQL
    $sql = "INSERT INTO atendimentos (pessoa_id, altura, peso, tipo_sanguineo, pressao, data_atendimento, localAtendimento, atendente_nome, observacao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind dos parâmetros
        $stmt->bind_param("iddssssss", $pessoa_id, $altura, $peso, $tipo_sanguineo, $pressao, $data_atendimento, $localAtendimento, $atendente_nome, $observacao);

        // Executar a consulta
        if ($stmt->execute()) {
            echo "Atendimento registrado com sucesso!";
        } else {
            echo "Erro ao registrar o atendimento: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $conn->error;
    }

    // Fechar a conexão
    $conn->close();
}
?>

<form method="POST">

    <label for="altura">Altura:</label>
    <input type="number" step="0.01" name="altura"><br>

    <label for="peso">Peso:</label>
    <input type="number" step="0.01" name="peso"><br>

    <label for="tipo_sanguineo">Tipo Sanguíneo:</label>
    <select name="tipo_sanguineo" required>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
    </select><br>

    <label for="pressao">Pressão:</label>
    <input type="text" name="pressao"><br>

    <label for="data_atendimento">Data do Atendimento:</label>
    <input type="date" name="data_atendimento" required><br>

    <label for="localAtendimento">Local:</label>
    <input type="text" name="localAtendimento" required><br>

    <label for="atendente_nome">Nome do Atendente:</label>
    <input type="text" name="atendente_nome" required><br>

    <label for="observacao">Observação:</label>
    <textarea name="observacao"></textarea><br>

    <input type="submit" value="Enviar">
</form>
