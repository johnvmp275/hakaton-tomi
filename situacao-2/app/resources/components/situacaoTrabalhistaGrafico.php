<?php
include("app/Database/connect.php");

// Obter o local de atendimento da query string, se houver
$local_id = $_GET['local_atendimento'] ?? null;


// Inicializar variáveis de contagem
$estudantes = 0;
$empregados = 0;
$desempregados = 0;
$outros = 0;

// Consulta para contar atendimentos por situação trabalhista
if (is_numeric($local_id)) {
    // Se um local específico foi selecionado, contar apenas os atendimentos desse local
    $query = "
        SELECT 
            pessoas.situacao_trabalhista,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        WHERE atendimentos.localAtendimento = ?
        GROUP BY pessoas.situacao_trabalhista";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $local_id);
} else {
    // Se nenhum local foi selecionado, contar atendimentos de todos os locais
    $query = "
        SELECT 
            pessoas.situacao_trabalhista,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        GROUP BY pessoas.situacao_trabalhista";
    $stmt = $conn->prepare($query);
}

// Executar a consulta
$stmt->execute();
$result = $stmt->get_result();

// Loop pelos resultados e atualizar as contagens de acordo com a situação trabalhista
while ($row = $result->fetch_assoc()) {
    switch (strtolower($row['situacao_trabalhista'])) {
        case 'estudante':
            $estudantes = $row['total_atendimentos'];
            break;
        case 'empregado':
            $empregados = $row['total_atendimentos'];
            break;
        case 'desempregado':
            $desempregados = $row['total_atendimentos'];
            break;
        default:
            $outros += $row['total_atendimentos']; // Considera "outros" qualquer situação não categorizada
            break;
    }
}
?>
<div class="form-row">
    <div class="status-card form-group">
        <h3>Estudantes</h3>
        <p data-numero>
            <?php echo $estudantes; ?>
        </p>
    </div>

    <div class="status-card form-group">
        <h3>Empregados</h3>
        <p data-numero>
            <?php echo $empregados; ?>
        </p>
    </div>

    <div class="status-card form-group">
        <h3>Desempregados</h3>
        <p data-numero>
            <?php echo $desempregados; ?>
        </p>
    </div>

    <div class="status-card form-group">
        <h3>Outros</h3>
        <p data-numero>
            <?php echo $outros; ?>
        </p>
    </div>
</div>