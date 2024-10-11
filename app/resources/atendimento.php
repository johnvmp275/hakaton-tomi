<?php
include("app/Database/connect.php");

// Capturar o ID do paciente via GET
$pessoa_id = $_GET["idUser"] ?? null;

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && $pessoa_id) {
    // Capturar os dados enviados via POST
    $altura = $_POST['altura'] ?? null;
    $peso = $_POST['peso'] ?? null;
    $tipo_sanguineo = $_POST['tipo_sanguineo'];
    $pressao = $_POST['pressao'] ?? null;
    $data_atendimento = $_POST['data_atendimento'];
    $localAtendimento = $_POST['local_atendimento'];  // ID do local
    $atendente_id = $_POST['atendente_id'];           // ID do atendente
    $id_acao = $_POST['id_acao'];                     // ID da ação
    $observacao = $_POST['observacao'] ?? null;

    // Verificar se o usuário já realizou a ação (id_acao) previamente no mesmo local (localAtendimento)
    $sql_verifica = "SELECT * FROM atendimentos WHERE pessoa_id = ? AND id_acao = ? AND localAtendimento = ?";

    if ($stmt_verifica = $conn->prepare($sql_verifica)) {
        // Bind dos parâmetros
        $stmt_verifica->bind_param("iii", $pessoa_id, $id_acao, $localAtendimento);
        $stmt_verifica->execute();
        $result_verifica = $stmt_verifica->get_result();

        // Se já existir um registro com o mesmo `pessoa_id`, `id_acao` e `localAtendimento`
        if ($result_verifica->num_rows > 0) {
            echo "Este usuário já realizou este atendimento anteriormente neste local.";
        } else {
            // Preparar a consulta SQL para inserir o atendimento
            $sql = "INSERT INTO atendimentos (pessoa_id, altura, peso, tipo_sanguineo, pressao, data_atendimento, localAtendimento, atendente_id, id_acao, observacao)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                // Bind dos parâmetros
                $stmt->bind_param("iddsssssis", $pessoa_id, $altura, $peso, $tipo_sanguineo, $pressao, $data_atendimento, $localAtendimento, $atendente_id, $id_acao, $observacao);

                // Executar a consulta
                if ($stmt->execute()) {
                    echo "Atendimento registrado com sucesso!";
                } else {
                    echo "Erro ao registrar o atendimento: " . $stmt->error;
                }

                // Fechar a declaração
                $stmt->close();
            } else {
                echo "Erro na preparação da consulta: " . $conn->error;
            }
        }

        // Fechar a declaração da verificação
        $stmt_verifica->close();
    } else {
        echo "Erro na preparação da consulta de verificação: " . $conn->error;
    }
}

// Consultas para obter os locais de atendimento, atendentes e ações
$sql_locais = "SELECT id_local, nome_local FROM locais_atendimento";
$result_locais = $conn->query($sql_locais);

$sql_atendentes = "SELECT id_atendente, nome_atendente FROM atendentes";
$result_atendentes = $conn->query($sql_atendentes);

$sql_acao = "SELECT id_acao, nome_acao FROM acoes";
$result_acao = $conn->query($sql_acao);

// Fechar a conexão após as consultas
$conn->close();
?>

<div class="container">

    <h1 class="titulo">Atendimento ao Cliente</h1>
    <form class="container" method="POST">

        <!-- Seleção do local de atendimento -->
        <div class="form-group">
            <span class="">Selecione o Local do Atendimento:</span>
            <select name="local_atendimento" required>
                <option value="">Selecione um local</option>
                <?php
                if ($result_locais->num_rows > 0) {
                    while ($local = $result_locais->fetch_assoc()) {
                        $id_local = $local['id_local'];
                        $nome_local = htmlspecialchars($local['nome_local']);
                        echo "<option value='$id_local'>$nome_local</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum local encontrado</option>";
                }
                ?>
            </select>
        </div>

        <!-- Seleção do atendente -->
        <div class="form-group">
            <span>Selecione o Nome do Atendente:</span>
            <select required class="form-control" name="atendente_id">
                <option value="" disabled selected>Selecione o Nome do Atendente</option>
                <?php
                if ($result_atendentes->num_rows > 0) {
                    while ($row = $result_atendentes->fetch_assoc()) {
                        $id_atendente = htmlspecialchars($row['id_atendente']);
                        $nome_atendente = htmlspecialchars($row['nome_atendente']);
                        echo "<option value='$id_atendente'>$nome_atendente</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum atendente encontrado</option>";
                }
                ?>
            </select>
        </div>

        <!-- Seleção da ação -->
        <div class="form-group">
            <span>Selecione a Ação:</span>
            <select required class="form-control" name="id_acao">
                <option value="" disabled selected>Selecione a Ação</option>
                <?php
                if ($result_acao->num_rows > 0) {
                    while ($row = $result_acao->fetch_assoc()) {
                        $id_acao = htmlspecialchars($row['id_acao']);
                        $nome_acao = htmlspecialchars($row['nome_acao']);
                        echo "<option value='$id_acao'>$nome_acao</option>";
                    }
                } else {
                    echo "<option value=''>Nenhuma ação encontrada</option>";
                }
                ?>
            </select>
        </div>

        <!-- Dados adicionais do atendimento -->
        <div class="form-double">
            <div class="form-group">
                <span class="">Insira a Data do Atendimento:</span>
                <input required class="form-control" type="date" name="data_atendimento"
                    placeholder="Digite a Data do Atendimento">
            </div>
        </div>

        <div class="form-double">
            <div class="form-group">
                <span class="">Insira a Altura:</span>
                <input required class="form-control" type="number" name="altura" step="0.01"
                    placeholder="Digite a Altura (m)">
            </div>

            <div class="form-group">
                <span class="">Insira o Peso:</span>
                <input required class="form-control" type="number" name="peso" step="0.01"
                    placeholder="Digite o Peso (kg)">
            </div>
        </div>

        <div class="form-group">
            <span class="">Tipo Sanguíneo:</span>
            <select required class="form-control" name="tipo_sanguineo">
                <option value="" disabled selected>Selecione o Tipo Sanguíneo</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        <div class="form-group">
            <span class="">Insira a Pressão Arterial:</span>
            <input required class="form-control" type="text" name="pressao" placeholder="Digite a Pressão Arterial">
        </div>

        <div class="form-group">
            <span class="">Observações:</span>
            <input class="form-control" type="text" name="observacao" placeholder="Digite as Observações (opcional)">
        </div>

        <input class="login-button" type="submit" value="Enviar">
    </form>
</div>