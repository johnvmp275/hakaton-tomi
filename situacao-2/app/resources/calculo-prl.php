<?php
require('.\app\Database\connect.php');

// Inicializa as variáveis para armazenar os dados
$funcionarioNome = '';
$salario = 0.0;
$resultado = '';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe o nome do funcionário
    if (isset($_POST['funcionario'])) {
        $funcionarioNome = $_POST['funcionario'];
    } else {
        echo "Erro: Funcionário não selecionado.";
        exit;
    }

    // Consulta o salário do funcionário com base no nome
    $sql = "SELECT salario FROM funcionarios WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $funcionarioNome);
    $stmt->execute();
    $stmt->bind_result($salario);
    $stmt->fetch();
    $stmt->close();

    // Certifica-se que o salário seja um número
    $salario = floatval($salario);

    // Verifica se o salário foi retornado corretamente
    if (!$salario) {
        echo "Erro: Salário não encontrado.";
        exit;
    }

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
        <h2 class='titulo subtitle'>Resultados</h2>
        PLR: R$ " . number_format($plr, 2) . "<br>
        Total a receber: R$ " . number_format($total, 2) . "
    ";
}
?>

<div class="container">
    <h1 class="titulo">Calculadora de PLR</h1>
    <form method="post" action="" class="form-group">
        <div class="form-group">
            <label for="">
                Funcionário
            </label>
            <input class="form-control" type="text" id="funcionario" name="funcionario" list="lista-funcionarios" required value="<?php echo htmlspecialchars($funcionarioNome); ?>">
            <datalist class="form-control" id="lista-funcionarios">
                <?php
                // Selecionar funcionário
                $sql = "SELECT id, nome FROM funcionarios";
                $result = $conn->query($sql);
                // Loop para exibir cada funcionário na lista de opções
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['nome']) . "'>" . $row['id'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum funcionário encontrado</option>";
                }
                ?>
            </datalist>
        </div>

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