<?php
include("app/Database/connect.php");
$pessoa_id = $_GET["idUser"];

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

    <h2 class="titulo">Atendimento ao Cliente</h2>
    <form class="form" method="POST">

        <div class="form-group">
            <span class="">Insira a Altura:</span>
            <input required="" class="form-control" type="number" name="altura" placeholder="Digite a Altura">
        </div>

        <div class="form-group">
            <span class="">Insira o Peso:</span>
            <input required="" class="form-control" type="number" name="peso" placeholder="Digite o Peso">
        </div>

        <div class="form-group">
            <span class="">Insira a Pressão Arterial:</span>
            <input required="" class="form-control" type="text" name="pressao"
                placeholder="Digite a Pressão Arterial">
        </div>

        <div class="form-group">
            <span class="">Insira a Data do Atendimento:</span>
            <input required="" class="form-control" type="date" name="data_atendimento"
                placeholder="Digite a Data do Atendimento">
        </div>

        <div class="form-group">
            <span class="">Insira o Local do Atendimento:</span>
            <input required="" class="form-control" type="text" name="localAtendimento"
                placeholder="Digite o Local do Atendimento">
        </div>

        <div class="form-group">
            <span class="">Insira o Nome do Atendente:</span>
            <input required="" class="form-control" type="text" name="atendente_nome"
                placeholder="Digite o Nome do Atendente">
        </div>

        <div class="form-group">
            <span class="titulo">Selecione o Tipo Sanguíneo:</span>
            <select required class="" name="tipo_sanguineo">
                <option value="" disabled selected>Selecione o Tipo Sanguíneo</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        <div class="form-group">
            <span class="">Observações:</span>
            <input required="" class="form-control" type="text" name="observacao" placeholder="Digite as Observações">
        </div>

        <input class="login-button" type="submit" value="Enviar">
    </form>
