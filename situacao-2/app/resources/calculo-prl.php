<?php
require('.\app\Database\connect.php');

// Inicializa as variáveis para armazenar os dados
$funcionarioNome = '';
$salario = '';
$resultado = '';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe o nome do funcionário
    $funcionarioNome = $_POST['funcionario'];

    // Consulta o salário do funcionário com base no nome
    $sql = "SELECT salario FROM funcionarios WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $funcionarioNome);
    $stmt->execute();
    $stmt->bind_result($salario);
    $stmt->fetch();
    $stmt->close();

    // Calcular PLR
    $porcentagem = 65; // Percentual padrão
    $mesesTrab = $_POST['mesesTrab'];

    // Define o total de meses no ano
    $meses = 12;

    // Calcula o percentual mensal
    $porcentual = $porcentagem / $meses;

    // Calcula a base para os meses trabalhados
    $calcBase = $porcentual * $mesesTrab;

    // Calcula a PLR
    $plr = $salario * ($calcBase / 100);

    // Calcula o total a receber
    $total = $salario + $plr;

    // Exibe os resultados
    $resultado = "
        <h2>Resultados</h2>
        PLR: R$ " . number_format($plr, 2) . "<br>
        Total a receber: R$ " . number_format($total, 2) . "
    ";
}
?>

<div class="container">
    <h1 class="titulo">Calculadora de PLR</h1>
    <form method="post" action="" class="form-group">
        <label for="funcionario">Funcionário: <?php echo htmlspecialchars($funcionarioNome); ?></label>
        <select id="lista-funcionarios">
            <?php
            // Selecionar funcionário
            $sql = "SELECT id, nome FROM funcionarios";
            $result = $conn->query($sql);
            // Loop para exibir cada funcionário na lista de opções
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['nome']) . "'>" .$row['nome']."</option>";
                }
            } else {
                echo "<option value=''>Nenhum funcionário encontrado</option>";
            }
            ?>
        </select>

        <div class="form-group">
            <label for="porcentagem">Porcentagem da PLR (%): </label>
            <input class="form-control" type="number" id="porcentagem" name="porcentagem" value="65" disabled>
        </div>

        <div class="form-group">
            <label for="mesesTrab">Meses Trabalhados: </label>
            <input class="form-control" type="number" id="mesesTrab" name="mesesTrab" value="10" required>
        </div>

        <div class="form-group">
            <label for="salario">Salário (R$): </label>
            <input class="form-control" type="number" id="salario" name="salario" required value="<?php echo htmlspecialchars($salario); ?>" disabled>
        </div>

        <button type="submit" class="btn">Calcular</button>
    </form>

    <div id="resultado">
        <?php
        // Exibe o resultado, se houver
        if ($resultado) {
            echo $resultado;
        }
        ?>
    </div>
</div>

<?php
$conn->close();
?>