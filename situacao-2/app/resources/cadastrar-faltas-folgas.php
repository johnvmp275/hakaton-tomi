<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Verifica se o ID do funcionário foi passado na URL
if (!isset($_GET['id'])) {
    die("ID do funcionário não fornecido.");
}

// Obtém o ID do funcionário da URL
$funcionario_id = $_GET['id'];

// Se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo']; // Pode ser 'falta' ou 'folga'
    $motivo = $_POST['motivo']; // Motivo da falta ou folga

    // Se for folga, utiliza data de início e fim
    if ($tipo === 'folga') {
        $data_inicio = $_POST['dataInicio'];
        $data_fim = $_POST['dataFim'];

        if (empty($data_inicio) || empty($data_fim) || empty($tipo) || empty($motivo)) {
            $erro = "Por favor, preencha todos os campos.";
        } else {
            // Insere a folga no banco de dados
            $query = "INSERT INTO folgas (funcionario_id, dataInicio, dataFim, motivoFolgas) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isss", $funcionario_id, $data_inicio, $data_fim, $motivo);
        }
    } else {
        // Se for falta, utiliza apenas a data única
        $data = $_POST['data'];

        if (empty($data) || empty($tipo) || empty($motivo)) {
            $erro = "Por favor, preencha todos os campos.";
        } else {
            // Insere a falta no banco de dados
            $query = "INSERT INTO faltas (funcionario_id, dataFaltas, motivoFaltas) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iss", $funcionario_id, $data, $motivo);
        }
    }

    // Executa a query e verifica se a operação foi bem-sucedida
    if (isset($stmt) && $stmt->execute()) {
        $sucesso = "Falta/Folga cadastrada com sucesso!";
    } else {
        $erro = "Erro ao cadastrar. Tente novamente.";
    }
}

// Busca informações do funcionário para exibir na página
$query_funcionario = "SELECT nome FROM funcionarios WHERE id = ?";
$stmt = $conn->prepare($query_funcionario);
$stmt->bind_param("i", $funcionario_id);
$stmt->execute();
$result_funcionario = $stmt->get_result();

if ($result_funcionario->num_rows > 0) {
    $funcionario = $result_funcionario->fetch_assoc();
} else {
    die("Funcionário não encontrado.");
}
?>

<div class="container">
    <h1 class="titulo">Cadastrar Ausência (<?= htmlspecialchars($funcionario['nome']) ?>)</h1>
    <a href="/gerenciamento">
        <button class="btn">Voltar a Listagem</button>
    </a>

    <?php if (isset($erro)): ?>
        <p class="msg-erro"><?= $erro ?></p>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <p class="msg-sucesso"><?= $sucesso ?></p>
    <?php endif; ?>

    <form method="post" class="form-group">
        <!-- Campo Tipo primeiro -->
        <div class="form-group">
            <label for="tipo">Tipo:</label>
            <select class="form-control" id="tipo" name="tipo" required onchange="toggleDateFields()">
                <option value="">Selecione</option>
                <option value="falta">Falta</option>
                <option value="folga">Folga</option>
            </select>
        </div>

        <!-- Campo Data único para Falta -->
        <div class="form-group" id="data-falta">
            <label for="data">Data:</label>
            <input class="form-control" type="date" id="data" name="data">
        </div>

        <!-- Campos Data Início e Data Fim para Folga -->
        <div class="form-group" id="data-folga" style="display:none;">
            <label for="dataInicio">Data Início:</label>
            <input class="form-control" type="date" id="dataInicio" name="dataInicio">
        </div>

        <div class="form-group" id="data-fim" style="display:none;">
            <label for="dataFim">Data Fim:</label>
            <input class="form-control" type="date" id="dataFim" name="dataFim">
        </div>

        <!-- Campo Motivo -->
        <div class="form-group">
            <label for="motivo">Motivo:</label>
            <input class="form-control" type="text" id="motivo" name="motivo" required>
        </div>

        <button type="submit" class="btn">Cadastrar</button>
    </form>
</div>

<script>
    function toggleDateFields() {
        const tipo = document.getElementById('tipo').value;
        const dataFaltaField = document.getElementById('data-falta');
        const dataFolgaField = document.getElementById('data-folga');
        const dataFimField = document.getElementById('data-fim');

        if (tipo === 'folga') {
            dataFaltaField.style.display = 'none';  // Esconde o campo 'Data' único
            dataFolgaField.style.display = 'block';  // Mostra 'Data Início'
            dataFimField.style.display = 'block';    // Mostra 'Data Fim'
        } else if (tipo === 'falta') {
            dataFaltaField.style.display = 'block';  // Mostra o campo 'Data' único
            dataFolgaField.style.display = 'none';   // Esconde 'Data Início'
            dataFimField.style.display = 'none';     // Esconde 'Data Fim'
        } else {
            // Se nenhum tipo estiver selecionado, esconde ambos
            dataFaltaField.style.display = 'none';
            dataFolgaField.style.display = 'none';
            dataFimField.style.display = 'none';
        }
    }
</script>
