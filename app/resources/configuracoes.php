<?php
// Conectar ao banco de dados
include("app/Database/connect.php");

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adicionar novo Atendente
    if (isset($_POST['add_atendente'])) {
        $nome_atendente = $_POST['nome_atendente'];
        $especialidade = $_POST['especialidade'];
        $contato_atendente = $_POST['contato_atendente'];

        if (!empty($nome_atendente) && !empty($contato_atendente)) {
            $sql_atendente = "INSERT INTO atendentes (nome_atendente, especialidade, contato_atendente) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql_atendente)) {
                $stmt->bind_param("sss", $nome_atendente, $especialidade, $contato_atendente);
                if ($stmt->execute()) {
                    $sucesso = "Atendente adicionado com sucesso!";
                } else {
                    $erro = "Erro ao adicionar atendente: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $erro = "Erro na preparação da consulta: " . $conn->error;
            }
        } else {
            $erro = "Preencha todos os campos obrigatórios!";
        }
    }

    // Adicionar novo Local de Atendimento
    if (isset($_POST['add_local'])) {
        $nome_local = $_POST['nome_local'];
        $endereco_local = $_POST['endereco_local'];
        $contato_local = $_POST['contato_local'];

        if (!empty($nome_local) && !empty($endereco_local) && !empty($contato_local)) {
            $sql_local = "INSERT INTO locais_atendimento (nome_local, endereco_local, contato_local) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql_local)) {
                $stmt->bind_param("sss", $nome_local, $endereco_local, $contato_local);
                if ($stmt->execute()) {
                    $sucesso = "Local de atendimento adicionado com sucesso!";
                } else {
                    $erro = "Erro ao adicionar local: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $erro = "Erro na preparação da consulta: " . $conn->error;
            }
        } else {
            $erro = "Preencha todos os campos obrigatórios!";
        }
    }

    // Adicionar novo Local de Atendimento
    if (isset($_POST['add_acao'])) {
        $nome_acao = $_POST['nome_acao'];

        if (!empty($nome_acao)) {
            $sql_local = "INSERT INTO acoes (nome_acao) VALUES (?)";
            if ($stmt = $conn->prepare($sql_local)) {
                $stmt->bind_param("s", $nome_acao);
                if ($stmt->execute()) {
                    $sucesso = "Ação adicionada com sucesso!";
                } else {
                    $erro = "Erro ao adicionar ação: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $erro = "Erro na preparação da consulta: " . $conn->error;
            }
        } else {
            $erro = "Preencha todos os campos obrigatórios!";
        }
    }
}

// Fechar conexão com o banco
$conn->close();
?>

<h1 class="titulo">Configurações</h1>

<div class="tab">
    <button class="tablinks active" onclick="openCity(event, 'atendente')">Atendente</button>
    <button class="tablinks" onclick="openCity(event, 'local')">Local de Atendimento</button>
    <button class="tablinks" onclick="openCity(event, 'acao')">Ação do Atendimento</button>
</div>

<div id="atendente" class="tabcontent">
    <form method="POST" class="container">
        <?php if ($erro): ?>
            <div class="error-message"><?php echo $erro; ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="success-message"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        <label for="nome_atendente">Nome do Atendente:</label>
        <input type="text" id="nome_atendente" name="nome_atendente" required placeholder="Nome do Atendente" class="form-control">

        <label for="especialidade">Especialidade (opcional):</label>
        <input type="text" id="especialidade" name="especialidade" placeholder="Especialidade" class="form-control">

        <label for="contato_atendente">Contato do Atendente:</label>
        <input type="text" id="contato_atendente" name="contato_atendente" required placeholder="Contato do Atendente" class="form-control">

        <button type="submit" name="add_atendente" class="login-button">Adicionar Atendente</button>
    </form>
</div>

<div id="local" class="tabcontent">
    <form method="POST" class="container">
        <label for="nome_local">Nome do Local:</label>
        <input type="text" id="nome_local" name="nome_local" required placeholder="Nome do Local" class="form-control">

        <label for="endereco_local">Endereço:</label>
        <input type="text" id="endereco_local" name="endereco_local" required placeholder="Endereço" class="form-control">

        <label for="contato_local">Contato do Local:</label>
        <input type="text" id="contato_local" name="contato_local" required placeholder="Contato do Local" class="form-control">

        <button type="submit" name="add_local" class="login-button">Adicionar Local</button>
    </form>
</div>

<div id="acao" class="tabcontent">
    <form method="POST" class="container">
        <label for="nome_acao">Nome da Ação:</label>
        <input type="text" id="nome_acao" name="nome_acao" required placeholder="Nome da Ação" class="form-control">

        <button type="submit" name="add_acao" class="login-button">Adicionar Ação</button>
    </form>
</div>