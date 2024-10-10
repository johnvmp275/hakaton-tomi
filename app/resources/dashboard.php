<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Consulta para trazer os locais de atendimento
$sql_locais = "SELECT id_local, nome_local FROM locais_atendimento";
$result_locais = $conn->query($sql_locais);
?>

<!-- Formulário com select para selecionar o local -->
<form method="GET" action="">
    <label for="local_atendimento">Selecione o Local de Atendimento:</label>
    <select name="local_atendimento" id="local_atendimento">
        <option value="">Selecione um local</option>

        <?php
        // Verificar se há locais de atendimento
        if ($result_locais->num_rows > 0) {
            // Criar opções para o select
            while ($local = $result_locais->fetch_assoc()) {
                $id_local = $local['id_local'];
                $nome_local = htmlspecialchars($local['nome_local']); // Proteção contra XSS
                // Exibir cada local como uma opção
                echo "<option value='$id_local'>$nome_local</option>";
            }
        } else {
            echo "<option value=''>Nenhum local encontrado</option>";
        }
        ?>

    </select>
</form>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>
