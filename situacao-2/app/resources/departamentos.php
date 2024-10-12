<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Inicializa a variável de pesquisa
$search = '';

// Verifica se a pesquisa foi enviada
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Busca todos os departamentos com o número de funcionários em cada um, considerando a pesquisa
$query = "
    SELECT d.id, d.nome, COUNT(f.id) AS num_funcionarios
    FROM departamentos d
    LEFT JOIN funcionarios f ON d.id = f.departamento_id
";

// Adiciona a cláusula WHERE se houver pesquisa
if (!empty($search)) {
    $query .= " WHERE d.nome LIKE ?";
}

// Agrupa os resultados
$query .= " GROUP BY d.id, d.nome";

// Prepara a consulta
$stmt = $conn->prepare($query);

// Se houver pesquisa, vincula o parâmetro
if (!empty($search)) {
    $param = "%$search%";
    $stmt->bind_param("s", $param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamentos</title>
    <style>
  
    </style>
</head>
<body>

<div class="container">
    <h1>Departamentos</h1>

    <div class="search-container">
        <form action="" method="get">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Pesquisar departamento...">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Departamento</th>
                <th>Número de Funcionários</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($departamento = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $departamento['id'] ?></td>
                        <td><?= htmlspecialchars($departamento['nome']) ?></td>
                        <td><?= $departamento['num_funcionarios'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum departamento encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
