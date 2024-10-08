<?php
include("app/Database/connect.php");

// Variável para armazenar as mensagens
$erro = ""; 
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe e sanitiza os dados do formulário
    $nomePaciente = trim($_POST['nomePaciente']);
    $dataNasc = $_POST['dataNasc'];
    $bairro = trim($_POST['bairro']);
    $genero = $_POST['genero'];
    $situacao_trabalhista = $_POST['situacao_trabalhista'];
    $contato = trim($_POST['contato']);
    $documento = trim($_POST['documento']);
    $observacao = trim($_POST['observacao']);

    // Validação de campos obrigatórios
    if (empty($nomePaciente) || empty($dataNasc) || empty($bairro) || empty($genero) || empty($situacao_trabalhista) || empty($documento)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }

    // Verificação se o documento já existe no banco de dados
    if (empty($erro)) {
        $sql_check_documento = "SELECT * FROM pessoas WHERE documento = ?";
        $stmt_check_documento = $conn->prepare($sql_check_documento);
        $stmt_check_documento->bind_param("s", $documento);
        $stmt_check_documento->execute();
        $result_check_documento = $stmt_check_documento->get_result();

        if ($result_check_documento->num_rows > 0) {
            $erro = "Este documento (RG ou SUS) já está cadastrado.";
        }
    }

    // Verificação se o contato (telefone ou email) já existe no banco de dados
    if (empty($erro) && !empty($contato)) {
        $sql_check_contato = "SELECT * FROM pessoas WHERE contato = ?";
        $stmt_check_contato = $conn->prepare($sql_check_contato);
        $stmt_check_contato->bind_param("s", $contato);
        $stmt_check_contato->execute();
        $result_check_contato = $stmt_check_contato->get_result();

        if ($result_check_contato->num_rows > 0) {
            $erro = "Este contato (telefone ou email) já está cadastrado.";
        }

        // Fecha a declaração após a verificação de contato
        $stmt_check_contato->close();
    }

    // Inserção no banco de dados se não houver erros
    if (empty($erro)) {
        $sql = "INSERT INTO pessoas (nomePaciente, dataNasc, bairro, genero, situacao_trabalhista, contato, documento, observacao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $nomePaciente, $dataNasc, $bairro, $genero, $situacao_trabalhista, $contato, $documento, $observacao);

        if ($stmt->execute()) {
            $sucesso = "Cadastro realizado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }

        // Fecha a declaração de inserção
        $stmt->close();
    }

    // Fecha a verificação de duplicidade de documento
    if (isset($stmt_check_documento)) {
        $stmt_check_documento->close();
    }
}

// Fecha a conexão
$conn->close();
?>

<h2>Cadastro de Pessoas - Ação Social</h2>

<?php if (!empty($erro)): ?>
    <div style="color: red;"><?php echo $erro; ?></div>
<?php elseif (!empty($sucesso)): ?>
    <div style="color: green;"><?php echo $sucesso; ?></div>
<?php endif; ?>

<form method="POST">
    <label for="nomePaciente">Nome Completo *</label>
    <input type="text" id="nomePaciente" name="nomePaciente" required value="">

    <label for="dataNasc">Data de Nascimento *</label>
    <input type="date" id="dataNasc" name="dataNasc" required value="">

    <label for="bairro">Bairro *</label>
    <input type="text" id="bairro" name="bairro" required value="">

    <label for="genero">Gênero *</label>
    <select id="genero" name="genero" required>
        <option value="">Selecione</option>
        <option value="Masculino">Masculino</option>
        <option value="Feminino">Feminino</option>
        <option value="Outro">Outro</option>
    </select>

    <label for="situacao_trabalhista">Situação Trabalhista *</label>
    <select id="situacao_trabalhista" name="situacao_trabalhista" required>
        <option value="">Selecione</option>
        <option value="Empregado">>Empregado</option>
        <option value="Desempregado">Desempregado</option>
        <option value="Estudante" >Estudante</option>
        <option value="Outro" >Outro</option>
    </select>

    <label for="contato">Contato (Telefone ou Email)</label>
    <input type="text" id="contato" name="contato" value="">

    <label for="documento">Documento (RG ou SUS) *</label>
    <input type="text" id="documento" name="documento" required value="" pattern="[0-9]+">

    <label for="observacao">Observação</label>
    <textarea id="observacao" name="observacao"></textarea>

    <button type="submit">Cadastrar</button>
</form>

<script>
    // Validação no frontend para aceitar apenas números no campo documento
    document.getElementById('documento').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
