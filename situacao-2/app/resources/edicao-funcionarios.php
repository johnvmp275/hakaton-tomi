<?php
// Inclui o arquivo de conexão
include("app/Database/connect.php");

// Verifica se o ID do funcionário foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Busca as informações do funcionário no banco de dados
    $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $funcionario = $result->fetch_assoc();

    // Verifica se o funcionário foi encontrado
    if (!$funcionario) {
        echo "Funcionário não encontrado!";
        exit;
    }
} else {
    echo "ID do funcionário não fornecido!";
    exit;
}

// Mensagem inicial
$mensagem = '';

// Verifica se o formulário de edição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário e faz validações
    $nome = htmlspecialchars($_POST['nome']);
    $dataNasc = $_POST['dataNasc'];
    $salario = filter_var($_POST['salario'], FILTER_VALIDATE_FLOAT);
    $departamento_id = filter_var($_POST['departamento_id'], FILTER_VALIDATE_INT);

    if ($salario && $departamento_id) {
        // Atualiza as informações do funcionário no banco de dados
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = ?, dataNasc = ?, salario = ?, departamento_id = ? WHERE id = ?");
        $stmt->bind_param("ssdii", $nome, $dataNasc, $salario, $departamento_id, $id);

        if ($stmt->execute()) {
            // Mensagem de sucesso
            echo "Informações atualizadas com sucesso!";
        } else {
            // Mensagem de erro
            echo "Erro ao atualizar as informações: " . $conn->error;
        }
    } else {
        echo "Dados inválidos fornecidos!";
    }
    exit; // Importante para evitar que o código abaixo seja executado após um POST
}
?>

<div class="container">
    <h1 class="titulo">Editar Funcionário</h1>
    <a href="/gerenciamento">
        <button type="submit" class="btn">Voltar a Listagem</button>
    </a>

    <!-- Exibe a mensagem de sucesso ou erro -->
    <p id="mensagem"><?= $mensagem ?></p>

    <form id="form-editar" class="form-group" method="POST">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input class="form-control" type="text" id="nome" name="nome" value="<?= htmlspecialchars($funcionario['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label for="dataNasc">Data de Nascimento:</label>
            <input class="form-control" type="date" id="dataNasc" name="dataNasc" value="<?= htmlspecialchars($funcionario['dataNasc']) ?>" required>
        </div>

        <div class="form-group">
            <label for="salario">Salário:</label>
            <input class="form-control" type="number" id="salario" name="salario" step="0.01" value="<?= htmlspecialchars($funcionario['salario']) ?>" required>
        </div>

        <div class="form-group">
            <label for="departamento_id">Departamento:</label>
            <select class="form-control" id="departamento_id" name="departamento_id" required>
                <?php
                // Consulta para buscar todos os departamentos
                $result = $conn->query("SELECT * FROM departamentos");

                // Itera sobre os departamentos para criar o dropdown
                while ($departamento = $result->fetch_assoc()) {
                    // Verifica se o departamento é o mesmo do funcionário atual
                    $selected = $departamento['id'] == $funcionario['departamento_id'] ? 'selected' : '';
                    echo "<option value='{$departamento['id']}' $selected>{$departamento['nome']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit">Salvar Alterações</button>
    </form>
</div>

<script>
    document.getElementById('form-editar').addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        const formData = new FormData(this); // Coleta os dados do formulário

        fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Recarrega a página após o envio do formulário
                window.location.reload();
            })
            .catch(error => {
                console.error('Erro:', error);
            });
    });
</script>