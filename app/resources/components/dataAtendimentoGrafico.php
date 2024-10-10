<?php
include("app/Database/connect.php");

// Obter o ano da query string ou usar o ano atual como padrão
$dataAno = $_GET['dataAno'] ?? date('Y');

// Sanitização do input para evitar injeção de SQL
$dataAno = intval($dataAno); // Garantindo que seja um número inteiro

// Obter o local_id da query string
$local_id = $_GET['local_atendimento'] ?? null;

// Consulta para contar atendimentos por mês no ano especificado
if (is_numeric($local_id)) {
    // Se um local específico foi selecionado, contar apenas os atendimentos desse local
    $query = "SELECT 
                  MONTH(data_atendimento) AS mes, 
                  COUNT(*) AS total_atendimentos 
              FROM 
                  atendimentos 
              WHERE 
                  YEAR(data_atendimento) = ? 
                  AND localAtendimento = ? 
              GROUP BY 
                  mes 
              ORDER BY 
                  mes";

    // Prepare a consulta
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $dataAno, $local_id); // 'ii' para dois inteiros
} else {
    // Se nenhum local foi selecionado, contar atendimentos de todos os locais
    $query = "SELECT 
                  MONTH(data_atendimento) AS mes, 
                  COUNT(*) AS total_atendimentos 
              FROM 
                  atendimentos 
              WHERE 
                  YEAR(data_atendimento) = ? 
              GROUP BY 
                  mes 
              ORDER BY 
                  mes";

    // Prepare a consulta
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $dataAno); // 'i' para um inteiro
}

// Execute a consulta
$stmt->execute();
$result = $stmt->get_result();

// Inicializar um array para os dados do gráfico
$data = array_fill(1, 12, 0); // Preenche o array com zero para os 12 meses

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[$row['mes']] = $row['total_atendimentos']; // Armazena o total de atendimentos por mês
    }
}

// Conversão para JSON
$jsonData = json_encode(array_values($data));
?>

<div>
    <div class="form-group" style="max-width:200px">
        <input type="date" id="dataAno" name="dataAno" value="" onchange="enviarData()">
    </div>
    <canvas id="dataAtendimento" style="height: 360px; max-width: 100%;"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const atendimentosPorMes = <?php echo $jsonData; ?>;
        const ctx2 = document.getElementById('dataAtendimento');

        new Chart(ctx2, {
            type: 'bar', 
            data: {
                labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'], // Meses
                datasets: [{
                    label: 'Total de Atendimentos por Mês (<?php echo $dataAno; ?>)', 
                    data: atendimentosPorMes,
                    borderWidth: 1, 
                    backgroundColor: '#FF6384',
                    borderColor: '#FF6384' 
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
                        text: 'Total de Atendimentos por Mês (<?php echo $dataAno; ?>)' 
                    }
                }
            }
        });
    });
</script>
