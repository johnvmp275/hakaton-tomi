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
    $data = $_POST['data'];
    $tipo = $_POST['tipo']; // Pode ser 'falta' ou 'folga'
    $motivo = $_POST['motivo']; // Motivo da falta ou folga

    // Validação simples
    if (empty($data) || empty($tipo) || empty($motivo)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        if ($tipo === 'falta') {
            // Insere a falta no banco de dados
            $query = "INSERT INTO faltas (funcionario_id, dataFaltas, motivoFaltas) VALUES (?, ?, ?)";
        } else {
            // Insere a folga no banco de dados
            $query = "INSERT INTO folgas (funcionario_id, dataFolgas, motivoFolgas) VALUES (?, ?, ?)";
        }
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $funcionario_id, $data, $motivo);

        if ($stmt->execute()) {
            $sucesso = "Falta/Folga cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar. Tente novamente.";
        }
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
    <h1>Cadastrar Falta/Folga para <?= htmlspecialchars($funcionario['nome']) ?></h1>

    <?php if (isset($erro)): ?>
        <p class="msg-erro"><?= $erro ?></p>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <p class="msg-sucesso"><?= $sucesso ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="falta">Falta</option>
                <option value="folga">Folga</option>
            </select>
        </div>

        <div class="form-group">
            <label for="motivo">Motivo:</label>
            <input type="text" id="motivo" name="motivo" required>
        </div>

        <button type="submit" class="btn">Cadastrar</button>
    </form>
</div>
