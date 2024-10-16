<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Verifica se o ID do funcionário foi passado na URL
if (!isset($_GET['id'])) {
    die("ID do funcionário não fornecido.");
}

// Obtém o ID do funcionário da URL
$funcionario_id = $_GET['id'];

// Busca as faltas do funcionário
$query_faltas = "SELECT * FROM faltas WHERE funcionario_id = ?";
$stmt = $conn->prepare($query_faltas);
$stmt->bind_param("i", $funcionario_id);
$stmt->execute();
$result_faltas = $stmt->get_result();

// Busca as folgas do funcionário
$query_folgas = "SELECT * FROM folgas WHERE funcionario_id = ?";
$stmt = $conn->prepare($query_folgas);
$stmt->bind_param("i", $funcionario_id);
$stmt->execute();
$result_folgas = $stmt->get_result();

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
    <h1>Histórico de Faltas e Folgas para <?= htmlspecialchars($funcionario['nome']) ?></h1>
    <a href="/gerenciamento">
        <button>
            Voltar a Listagem
        </button>
    </a>

    <h2>Faltas</h2>
    <table class="table-data">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_faltas->num_rows > 0): ?>
                <?php while ($faltas = $result_faltas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $faltas['idFaltas'] ?></td>
                        <td><?= htmlspecialchars($faltas['dataFaltas']) ?></td>
                        <td><?= htmlspecialchars($faltas['motivoFaltas']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhuma falta registrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Folgas</h2>
    <table class="table-data">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_folgas->num_rows > 0): ?>
                <?php while ($folga = $result_folgas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $folga['idFolgas'] ?></td>
                        <td><?= htmlspecialchars($folga['dataFolgas']) ?></td>
                        <td><?= htmlspecialchars($folga['motivoFolgas']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhuma folga registrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>