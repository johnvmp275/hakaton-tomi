<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Capturar o local_atendimento da URL (se houver)
$local_id = $_GET['local_atendimento'] ?? null;

// Consulta para trazer os locais de atendimento
$sql_locais = "SELECT id_local, nome_local FROM locais_atendimento";
$result_locais = $conn->query($sql_locais);
?>

<div class="container">
    <div class="form-double">
        <h3 for="local_atendimento">Selecione o Local de Atendimento:</h3>
        <select name="local_atendimento" id="local_atendimento" onchange="this.form.submit()" style="max-width:200px; margin-top: 8px">
            <option value="todos">Todos</option>
            <?php
            if ($result_locais->num_rows > 0) {
                while ($local = $result_locais->fetch_assoc()) {
                    $id_local = $local['id_local'];
                    $nome_local = htmlspecialchars($local['nome_local']);
                    $selected = ($local_id == $id_local) ? 'selected' : ''; // Marcar o local selecionado
                    echo "<option value='$id_local' $selected>$nome_local</option>";
                }
            } else {
                echo "<option value=''>Nenhum local encontrado</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-double">
        <?php
        include('app/resources/components/dataAtendimentoGrafico.php');
        include('app/resources/components/generoGrafico.php');
        ?>
    </div>

    <div class="">
        <?php
        include('app/resources/components/situacaoTrabalhistaGrafico.php');
        ?>
    </div>
    
    <div class="form-double">
        <?php
        include('app/resources/components/acoesGrafico.php');
        include('app/resources/components/tipoSanguineo.php');
        ?>
    </div>

    <div class="form-group">
        <?php
        include('app/resources/components/faixaEtariaGrafico.php');
        ?>
    </div>
</div>