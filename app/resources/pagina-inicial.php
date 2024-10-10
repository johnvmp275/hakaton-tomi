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

<h2 class="titulo">Listagem de Pessoas</h2>
<div class="listagem-topo">

<a href="/pagina-cadastro">
    <button>Cadastrar Pessoa +</button>
</a>


<!-- Alterado o método para GET -->
<form method="GET" class="pesquisar">
    <input type="text" name="pesquisa" placeholder="Pesquisar Paciente"
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
            <th>Bairro</th>
            <th>Gênero</th>
            <th>Documento</th>
            <th>Contato</th>
            <th>Ocupação</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nomePaciente']); ?></td>
                <td><?php echo htmlspecialchars($row['dataNasc']); ?></td>
                <td><?php echo htmlspecialchars($row['bairro']); ?></td>
                <td><?php echo htmlspecialchars($row['genero']); ?></td>
                <td><?php echo htmlspecialchars($row['documento']); ?></td>
                <td><?php echo htmlspecialchars($row['contato']); ?></td>
                <td><?php echo htmlspecialchars($row['situacao_trabalhista']); ?></td>
                <td>
                    <a href="/atendimento?idUser=<?php echo urlencode($row['idPaciente']); ?>"><span
                            class="material-symbols-outlined">
                            edit_document
                        </span></a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>