<?php
// Inclui o arquivo de conexão com o banco de dados
include("app/Database/connect.php");

// Verifica se o ID do departamento foi passado via GET
if (isset($_GET['id'])) {
    $departamento_id = $_GET['id'];

    // Busca o nome do departamento
    $stmt = $conn->prepare("SELECT nome FROM departamentos WHERE id = ?");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamento = $result->fetch_assoc();

    // Se o departamento não for encontrado
    if (!$departamento) {
        echo "Departamento não encontrado!";
        exit;
    }

    // Busca os funcionários que trabalham no departamento
    $stmt = $conn->prepare("
        SELECT f.nome, f.salario
        FROM funcionarios f 
        WHERE f.departamento_id = ?
    ");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $funcionarios = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "ID do departamento não fornecido!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários do Departamento - <?= htmlspecialchars($departamento['nome']) ?></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Funcionários do Departamento: <?= htmlspecialchars($departamento['nome']) ?></h1>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Salário</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($funcionarios) > 0): ?>
            <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                    <td><?= htmlspecialchars($funcionario['nome']) ?></td>
                    <td>R$ <?= number_format($funcionario['salario'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Nenhum funcionário encontrado para este departamento.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="gerenciar-departamentos">Voltar aos Departamentos</a>

</body>
</html>
