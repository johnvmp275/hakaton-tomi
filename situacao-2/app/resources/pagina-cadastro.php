<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Verifica se o formulário de cadastro de departamento foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_departamento'])) {
    $nome_departamento = $_POST['nome_departamento'];

    // Insere o departamento no banco de dados usando mysqli
    $stmt = $conn->prepare("INSERT INTO departamentos (nome) VALUES (?)");
    $stmt->bind_param("s", $nome_departamento);

    if ($stmt->execute()) {
        echo "<p>Departamento '$nome_departamento' cadastrado com sucesso!</p>";
    } else {
        echo "<p>Erro ao cadastrar o departamento: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Verifica se o formulário de cadastro de funcionário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_funcionario'])) {
    $nome_funcionario = $_POST['nome_funcionario'];
    $dataNasc = $_POST['dataNasc']; // Data já vem no formato YYYY-MM-DD
    $salario = $_POST['salario'];
    $departamento_id = $_POST['departamento_id'];

    // Insere o funcionário no banco de dados usando mysqli
    $stmt = $conn->prepare("INSERT INTO funcionarios (nome, dataNasc, salario, departamento_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nome_funcionario, $dataNasc, $salario, $departamento_id);

    if ($stmt->execute()) {
        echo "<p>Funcionário '$nome_funcionario' cadastrado com sucesso!</p>";
    } else {
        echo "<p>Erro ao cadastrar o funcionário: " . $conn->error . "</p>";
    }

    $stmt->close();
}


// Obtém a lista de departamentos usando mysqli
$result = $conn->query("SELECT * FROM departamentos");
$departamentos = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">
    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'funcionarios')">Funcionários</button>
        <button class="tablinks" onclick="openCity(event, 'departamentos')">Departamento</button>
    </div>

    <div id="funcionarios" class="tabcontent">
        <h1 class="titulo">Cadastrar Funcionário</h1>
        <form method="POST" class="form-group">
            <div class="form-group">
                <label for="">Nome do Funcionário:</label>
                <input class="form-control" type="text" name="nome_funcionario" required>
            </div>
            <div class="form-group">
                <label for="dataNasc">Data de Nascimento:</label>
                <input class="form-control" type="date" name="dataNasc" required>
            </div>
            <div class="form-group">
                <label for="">Salário:</label>
                <input class="form-control" type="number" name="salario" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="">Departamento:</label>
                <select class="form-control" name="departamento_id" required>
                    <option value="">Selecione o Departamento</option>
                    <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?= $departamento['id'] ?>"><?= $departamento['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input class="btn" type="submit" name="cadastrar_funcionario" value="Cadastrar Funcionário">
        </form>
    </div>

    <div id="departamentos" class="tabcontent">
        <h1 class="titulo">Cadastrar Departamento</h1>
        <form method="POST" class="form-group">
            <div class="form-group">
                <label for="">Nome do Departamento:</label>
                <input class="form-control" type="text" name="nome_departamento" required>
            </div>
            <input class="btn" type="submit" name="cadastrar_departamento" value="Cadastrar Departamento">
        </form>
    </div>
</div>