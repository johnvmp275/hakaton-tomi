<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Obter o funcionario_id da query string, se houver
$funcionario_id = $_GET['funcionario_id'] ?? null; // ID do funcionário passado pelo input

// Inicializar variáveis de contagem
$faltas = 0;
$folgas = 0;

if (is_numeric($funcionario_id)) {
    // Contar as faltas do funcionário específico
    $query_faltas = "
        SELECT COUNT(f.idFaltas) AS total_faltas
        FROM faltas f
        WHERE f.funcionario_id = ?";
    $stmt_faltas = $conn->prepare($query_faltas);
    $stmt_faltas->bind_param("i", $funcionario_id);
    $stmt_faltas->execute();
    $result_faltas = $stmt_faltas->get_result();
    
    if ($row_faltas = $result_faltas->fetch_assoc()) {
        $faltas = $row_faltas['total_faltas'];
    }
    $stmt_faltas->close();

    // Contar as folgas do funcionário específico
    $query_folgas = "
        SELECT COUNT(f.idFolgas) AS total_folgas
        FROM folgas f
        WHERE f.funcionario_id = ?";
    $stmt_folgas = $conn->prepare($query_folgas);
    $stmt_folgas->bind_param("i", $funcionario_id);
    $stmt_folgas->execute();
    $result_folgas = $stmt_folgas->get_result();

    if ($row_folgas = $result_folgas->fetch_assoc()) {
        $folgas = $row_folgas['total_folgas'];
    }
    $stmt_folgas->close();
}

// Fechar a conexão
$conn->close();
?>

<div class="form-row">
<form method="GET" action="">
    <div class="form-group">
        <label for="funcionario_id">Selecione o Funcionário (ID):</label>
        <input type="number" name="funcionario_id" id="funcionario_id" class="form-control" value="<?php echo htmlspecialchars($funcionario_id); ?>" placeholder="Digite o ID do funcionário" required>
        <button type="submit" class="btn btn-primary">Consultar</button>
    </div>
</form>

<div class="form-row2">
    <div class="status-card form-group" style="background-color: #1D9EF5">
        <h3>Faltas</h3>
        <p data-numero>
            <?php echo $faltas; ?>
        </p>
    </div>

    <div class="status-card form-group" style="background-color: #FF6384">
        <h3>Folgas</h3>
        <p data-numero>
            <?php echo $folgas; ?>
        </p>
    </div>
</div>
