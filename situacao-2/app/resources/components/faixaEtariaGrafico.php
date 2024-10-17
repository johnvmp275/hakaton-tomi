<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Obter o ano e o departamento_id da query string
$dataAno = date('Y'); // Usar o ano atual
$departamento_id = $_GET['departamento_id'] ?? null; // Adaptação para filtrar por departamento

// Inicializa variáveis para contagem de faixas etárias
$ate_17_count = 0;
$ate_30_count = 0;
$ate_40_count = 0;
$ate_60_count = 0;
$ate_80_count = 0;

// Consulta para obter a data de nascimento e calcular a idade, filtrando pelo ano e departamento
if (is_numeric($departamento_id)) {
    // Filtrar apenas os funcionários do departamento selecionado
    $sql_faixa_etaria = "
        SELECT f.dataNasc
        FROM funcionarios f
        WHERE f.departamento_id = ?"; // Filtra pelo departamento
    $stmt = $conn->prepare($sql_faixa_etaria);
    $stmt->bind_param("i", $departamento_id);
} else {
    // Considerar todos os funcionários
    $sql_faixa_etaria = "
        SELECT f.dataNasc
        FROM funcionarios f";
    $stmt = $conn->prepare($sql_faixa_etaria);
}

// Executa a consulta
$stmt->execute();
$result = $stmt->get_result();

// Calcular a idade e contar por faixa etária
while ($row = $result->fetch_assoc()) {
    if (isset($row['dataNasc']) && !empty($row['dataNasc'])) {
        $ano_nascimento = date("Y", strtotime($row['dataNasc']));
        $idade = $dataAno - $ano_nascimento;

        // Classificar a idade nas faixas etárias
        if ($idade >= 0 && $idade <= 17) {
            $ate_17_count++;
        } elseif ($idade >= 18 && $idade <= 30) {
            $ate_30_count++;
        } elseif ($idade >= 31 && $idade <= 40) {
            $ate_40_count++;
        } elseif ($idade >= 41 && $idade <= 60) { // Corrigido para 41 até 60
            $ate_60_count++;
        } elseif ($idade >= 61 && $idade <= 80) {
            $ate_80_count++;
        }
    }
}

// Fechar a consulta
$stmt->close();

// Dados em formato JSON para passar ao JavaScript
$faixaEtariaData = [
    'Até 17' => $ate_17_count,
    'Dos 18 até 30' => $ate_30_count,
    'Dos 31 até 40' => $ate_40_count,
    'Dos 41 até 60' => $ate_60_count, // Corrigido aqui também
    'Dos 61 até 80' => $ate_80_count,
];
$jsonData = json_encode($faixaEtariaData);

// Agora obter a lista de departamentos
$sql_departamentos = "SELECT id, nome FROM departamentos";
$stmt = $conn->prepare($sql_departamentos);
$stmt->execute();
$result = $stmt->get_result();

// Não feche a conexão ainda, pois precisamos dos departamentos

?>

<div>
    <form method="GET" action="">
        <div class="form-group">
            <label for="departamento_id">Selecione o Departamento:</label>
            <select name="departamento_id" id="departamento_id" class="form-control" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php
                // Gerar as opções com base nos departamentos do banco
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); // Proteção contra XSS
                    // Manter a seleção atual ao recarregar a página
                    $selected = (isset($_GET['departamento_id']) && $_GET['departamento_id'] == $id) ? 'selected' : '';
                    echo "<option value=\"$id\" $selected>$nome</option>";
                }
                ?>
            </select>
        </div>
    </form>
    <canvas id="faixaEtaria" style="max-height: 360px; min-width: 560px;"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const faixaEtariaData = <?php echo $jsonData; ?>;
        const ctx5 = document.getElementById('faixaEtaria');

        const labels = Object.keys(faixaEtariaData);
        const dataValues = Object.values(faixaEtariaData);

        new Chart(ctx5, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total de Funcionários por Faixa Etária',
                    data: dataValues,
                    borderWidth: 1,
                    backgroundColor: '#4BC0C0',
                    borderColor: '#4BC0C0'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Total de Funcionários por Faixa Etária'
                    }
                }
            }
        });
    });
</script>

<?php
// Fechar a conexão ao final
$conn->close();
