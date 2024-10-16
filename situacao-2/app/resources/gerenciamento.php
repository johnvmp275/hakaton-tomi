<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Inicializa a variável de pesquisa
$searchFuncionarios = '';

// Verifica se a pesquisa foi enviada
if (isset($_GET['searchFuncionarios'])) {
    $searchFuncionarios = $_GET['searchFuncionarios'];
}

// Busca todos os funcionários no banco de dados, considerando a pesquisa
$query = "
    SELECT f.id, f.nome, f.dataNasc, f.salario, d.nome AS departamento 
    FROM funcionarios f 
    JOIN departamentos d ON f.departamento_id = d.id
";

// Adiciona a cláusula WHERE se houver pesquisa
if (!empty($searchFuncionarios)) {
    $query .= " WHERE f.nome LIKE ? OR d.nome LIKE ? OR f.salario LIKE ?";
}

// Prepara a consulta
$stmt = $conn->prepare($query);

// Se houver pesquisa, vincula os parâmetros
if (!empty($searchFuncionarios)) {
    $param = "%$searchFuncionarios%";
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


// Inicializa a variável de pesquisa
$searchDepartamentos = '';

// Verifica se a pesquisa foi enviada
if (isset($_GET['searchDepartamentos'])) {
    $searchDepartamentos = $_GET['searchDepartamentos'];
}

// Busca todos os departamentos com o número de funcionários em cada um, considerando a pesquisa
$query = "
    SELECT d.id, d.nome, COUNT(f.id) AS num_funcionarios
    FROM departamentos d
    LEFT JOIN funcionarios f ON d.id = f.departamento_id
";

// Adiciona a cláusula WHERE se houver pesquisa
if (!empty($searchDepartamentos)) {
    $query .= " WHERE d.nome LIKE ?";
}

// Agrupa os resultados
$query .= " GROUP BY d.id, d.nome";

// Prepara a consulta
$stmt = $conn->prepare($query);

// Se houver pesquisa, vincula o parâmetro
if (!empty($searchDepartamentos)) {
    $param = "%$searchDepartamentos%";
    $stmt->bind_param("s", $param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'funcionarios')">Funcionários</button>
        <button class="tablinks" onclick="openCity(event, 'departamentos')">Departamentos</button>
    </div>

    <div id="funcionarios" class="tabcontent">
        <h1 class="titulo">Lista de Funcionários</h1>
        <div class="search-container">
            <form action="" method="get" class="pesquisar">
                <input type="text" name="searchFuncionarios" value="<?= htmlspecialchars($searchFuncionarios) ?>" placeholder="Pesquisar funcionários geral" class="form-control">
                <button type="submit">Buscar</button>
            </form>
        </div>
        <table class="funcionarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Nascimento</th>
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
                            <td><?= $funcionario['dataNasc'] ?></td>
                            <td>R$ <?= number_format($funcionario['salario'], 2, ',', '.') ?></td>
                            <td><?= $funcionario['departamento'] ?></td>
                            <td>
                                <!-- Botão para ver o histórico do funcionário -->
                                <a href="historico?id=<?= $funcionario['id'] ?>" class="">
                                    <button>
                                        <span class="material-symbols-outlined">
                                            history
                                        </span>
                                    </button>
                                </a>

                                <!-- Botão para cadastrar faltas e folgas -->
                                <a href="cadastrar-faltas-folgas?id=<?= $funcionario['id'] ?>" class="">
                                    <button>
                                        <span class="material-symbols-outlined">
                                            visibility
                                        </span>
                                    </button>
                                </a>

                                <a href="edicao-funcionarios?id=<?= $funcionario['id'] ?>" class="">
                                    <button>
                                        <span class="material-symbols-outlined">
                                            edit
                                        </span>
                                    </button>
                                </a>
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
    </div>

    <div id="departamentos" class="tabcontent">
        <h1 class="titulo">Departamentos</h1>

        <div class="search-container">
            <form action="" method="get" class="pesquisar">
                <input type="text" name="searchDepartamentos" value="<?= htmlspecialchars($searchDepartamentos) ?>" placeholder="Pesquisar departamento" class="form-control">
                <button class="btn" type="submit">Buscar</button>
            </form>
        </div>

        <table class="departamentos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Departamento</th>
                    <th>Número de Funcionários</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($departamento = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $departamento['id'] ?></td>
                            <td><?= htmlspecialchars($departamento['nome']) ?></td>
                            <td><?= $departamento['num_funcionarios'] ?></td>
                            <td>
                                <!-- Botão para ver funcionários do departamento -->
                                <a href="verFuncionarios?id=<?= $departamento['id'] ?>">
                                    <button>
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhum departamento encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>