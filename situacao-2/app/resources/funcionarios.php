<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Inicializa a variável de pesquisa
$search = '';

// Verifica se a pesquisa foi enviada
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Busca todos os funcionários no banco de dados, considerando a pesquisa
$query = "
    SELECT f.id, f.nome, f.idade, f.salario, d.nome AS departamento 
    FROM funcionarios f 
    JOIN departamentos d ON f.departamento_id = d.id
";

// Adiciona a cláusula WHERE se houver pesquisa
if (!empty($search)) {
    $query .= " WHERE f.nome LIKE ? OR d.nome LIKE ? OR f.salario LIKE ?";
}

// Prepara a consulta
$stmt = $conn->prepare($query);

// Se houver pesquisa, vincula os parâmetros
if (!empty($search)) {
    $param = "%$search%";
    $stmt->bind_param("sss", $param, $param, $param);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Armazena os funcionários em um array
    $funcionarios = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $funcionarios = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários</title>
    <style>
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            border-radius: 5px;
        }

        .btn-historico {
            background-color: #2196F3;
        }

        .btn-faltas {
            background-color: #FF5722;
        }
    </style>
</head>
<body>

<h1>Lista de Funcionários</h1>

<div class="search-container">
    <form action="" method="get">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Pesquisar por nome, departamento ou salário...">
        <button type="submit">Buscar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Idade</th>
            <th>Salário</th>
            <th>Departamento</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($funcionarios) > 0): ?>
            <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                    <td><?= $funcionario['id'] ?></td>
                    <td><?= $funcionario['nome'] ?></td>
                    <td><?= $funcionario['idade'] ?></td>
                    <td>R$ <?= number_format($funcionario['salario'], 2, ',', '.') ?></td>
                    <td><?= $funcionario['departamento'] ?></td>
                    <td>
                        <!-- Botão para ver o histórico do funcionário -->
                        <a href="historico?id=<?= $funcionario['id'] ?>" class="btn btn-historico">Ver Histórico</a>

                        <!-- Botão para cadastrar faltas e folgas -->
                        <a href="cadastrar-faltas-folgas?id=<?= $funcionario['id'] ?>" class="btn btn-faltas">Cadastrar Faltas/Folgas</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nenhum funcionário cadastrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
