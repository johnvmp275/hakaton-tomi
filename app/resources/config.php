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
}

// Fechar conexão com o banco
$conn->close();
?>

<div class="container">
    <h1>Configurações</h1>

    <!-- Exibir mensagens de sucesso ou erro -->
    <?php if ($erro): ?>
        <div class="error-message"><?php echo $erro; ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="success-message"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <!-- Formulário para adicionar Atendentes -->
    <h2>Adicionar Atendente</h2>
    <form action="config" method="POST">
        <label for="nome_atendente">Nome do Atendente:</label>
        <input type="text" id="nome_atendente" name="nome_atendente" required placeholder="Nome do Atendente">

        <label for="especialidade">Especialidade (opcional):</label>
        <input type="text" id="especialidade" name="especialidade" placeholder="Especialidade">

        <label for="contato_atendente">Contato do Atendente:</label>
        <input type="text" id="contato_atendente" name="contato_atendente" required placeholder="Contato do Atendente">

        <button type="submit" name="add_atendente">Adicionar Atendente</button>
    </form>

    <!-- Formulário para adicionar Locais de Atendimento -->
    <h2>Adicionar Local de Atendimento</h2>
    <form action="config" method="POST">
        <label for="nome_local">Nome do Local:</label>
        <input type="text" id="nome_local" name="nome_local" required placeholder="Nome do Local">

        <label for="endereco_local">Endereço:</label>
        <input type="text" id="endereco_local" name="endereco_local" required placeholder="Endereço">

        <label for="contato_local">Contato do Local:</label>
        <input type="text" id="contato_local" name="contato_local" required placeholder="Contato do Local">

        <button type="submit" name="add_local">Adicionar Local</button>
    </form>
</div>