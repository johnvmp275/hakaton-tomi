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
    $idade = $_POST['idade'];
    $salario = $_POST['salario'];
    $departamento_id = $_POST['departamento_id'];

    // Insere o funcionário no banco de dados usando mysqli
    $stmt = $conn->prepare("INSERT INTO funcionarios (nome, idade, salario, departamento_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sidi", $nome_funcionario, $idade, $salario, $departamento_id);
    
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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Departamento e Funcionário</title>
</head>
<body>
    <h1>Cadastrar Departamento</h1>
    <form method="POST" action="">
        Nome do Departamento: <input type="text" name="nome_departamento" required><br><br>
        <input type="submit" name="cadastrar_departamento" value="Cadastrar Departamento">
    </form>

    <hr>

    <h1>Cadastrar Funcionário</h1>
    <form method="POST" action="">
        Nome do Funcionário: <input type="text" name="nome_funcionario" required><br><br>
        Idade: <input type="number" name="idade" required><br><br>
        Salário: <input type="number" name="salario" step="0.01" required><br><br>
        Departamento:
        <select name="departamento_id" required>
            <option value="">Selecione o Departamento</option>
            <?php foreach ($departamentos as $departamento): ?>
                <option value="<?= $departamento['id'] ?>"><?= $departamento['nome'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="submit" name="cadastrar_funcionario" value="Cadastrar Funcionário">
    </form>
</body>
</html>
