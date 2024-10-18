<?php
include("app/Database/connect.php");

// Inicializando a variável de pesquisa e ação
$pesquisa = "";

// Verifica se foi enviada uma pesquisa via GET
if (isset($_GET['pesquisa'])) {
    $pesquisa = trim($_GET['pesquisa']);
}

// Consulta SQL para buscar todas as pessoas ou filtrar com base na pesquisa
$sql = "SELECT * FROM pessoas";
if (!empty($pesquisa)) {
    $sql .= " WHERE idPaciente LIKE ? OR nomePaciente LIKE ? OR documento LIKE ? OR bairro LIKE ? OR contato LIKE ? OR genero LIKE ? OR situacao_trabalhista LIKE ?";
}

$stmt = $conn->prepare($sql);

if (!empty($pesquisa)) {
    $pesquisa_param = "%" . $pesquisa . "%";
    $stmt->bind_param("sssssss", $pesquisa_param, $pesquisa_param, $pesquisa_param, $pesquisa_param, $pesquisa_param, $pesquisa_param, $pesquisa_param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1 class="titulo">Listagem de Pessoas</h1>
    <div class="listagem-topo">

        <a href="/pagina-cadastro">
            <button>Cadastrar Pessoa +</button>
        </a>

        <!-- Alterado o método para GET -->
        <form method="GET" class="pesquisar">
            <input type="text" name="pesquisa" placeholder="Pesquisar Dados do Paciente"
                value="<?php echo htmlspecialchars($pesquisa); ?>" class="form-control">
            <button type="submit"><span class="material-symbols-outlined">
                    search
                </span></button>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Nascimento</th>
                <th>Sexo</th>
                <th>Documento</th>
                <th>Contato</th>
                <th>Ocupação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['nomePaciente']); ?>
                        </span>
                    </td>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['dataNasc']); ?>
                        </span>
                    </td>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['genero']); ?>
                        </span>
                    </td>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['documento']); ?>
                        </span>
                    </td>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['contato']); ?>
                        </span>
                    </td>
                    <td>
                        <span>
                            <?php echo htmlspecialchars($row['situacao_trabalhista']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="/atendimento?idUser=<?php echo urlencode($row['idPaciente']); ?>">
                            <span class="material-symbols-outlined icon">
                                edit_document
                            </span>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>