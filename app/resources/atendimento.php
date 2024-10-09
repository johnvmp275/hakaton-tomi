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

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./app/resources/css/style-atendimento.css">
</head>

<body>

    <main>
        <div class="container">
            <div class="heading">Atendimento ao Cliente</div>
            <form action="" class="form" method="POST">
                <div class="form-bloco">
                    <div>
                        <span class="forgot-password"><a>Insira a Altura:</a></span>
                        <input required="" class="input" type="number" name="altura" placeholder="Digite a Altura">
                    </div>

                    <div>
                        <span class="forgot-password"><a>Insira o Peso:</a></span>
                        <input required="" class="input" type="number" name="peso" placeholder="Digite o Peso">
                    </div>
                </div>

                <div class="form-bloco">
                    <div>
                        <span class="forgot-password"><a>Insira a Pressão Arterial:</a></span>
                        <input required="" class="input" type="text" name="pressao"
                            placeholder="Digite a Pressão Arterial">
                    </div>

                    <div>
                        <span class="forgot-password"><a>Insira a Data do Atendimento:</a></span>
                        <input required="" class="input" type="date" name="data_atendimento"
                            placeholder="Digite a Data do Atendimento">
                    </div>
                </div>

                <div class="form-bloco">
                    <div>
                        <span class="forgot-password"><a>Insira o Local do Atendimento:</a></span>
                        <input required="" class="input" type="text" name="localAtendimento"
                            placeholder="Digite o Local do Atendimento">
                    </div>

                    <div>
                        <span class="forgot-password"><a>Insira o Nome do Atendente:</a></span>
                        <input required="" class="input" type="text" name="atendente_nome"
                            placeholder="Digite o Nome do Atendente">
                    </div>
                </div>

                <div>
                    <span class="forgot-password"><a>Observações:</a></span>
                    <input required="" class="input" type="text" name="observacao" placeholder="Digite as Observações">
                </div>

                <input class="login-button" type="submit" value="Enviar">
            </form>
        </div>
    </main>

</body>

</html>