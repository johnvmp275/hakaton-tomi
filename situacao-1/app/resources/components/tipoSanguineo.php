<?php
include("app/Database/connect.php");

// Obter o local_id da query string
$local_id = $_GET['local_atendimento'] ?? null;

// Tipos sanguíneos
$tiposSanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

// Inicializar um array para armazenar os totais de atendimentos
$totalAtendimentos = array_fill_keys($tiposSanguineos, 0);

// Consulta para contar atendimentos por tipo sanguíneo
if (is_numeric($local_id)) {
    // Se um local específico foi selecionado, contar apenas os atendimentos desse local
    $query = "SELECT 
                  tipo_sanguineo, 
                  COUNT(*) AS total_atendimentos 
              FROM 
                  atendimentos 
              WHERE 
                  localAtendimento = ? 
              GROUP BY 
                  tipo_sanguineo";

    // Prepare a consulta
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $local_id); // 'i' para um inteiro
} else {
    // Se nenhum local foi selecionado, contar atendimentos de todos os locais
    $query = "SELECT 
                  tipo_sanguineo, 
                  COUNT(*) AS total_atendimentos 
              FROM 
                  atendimentos 
              GROUP BY 
                  tipo_sanguineo";

    // Prepare a consulta
    $stmt = $conn->prepare($query);
}

// Execute a consulta
$stmt->execute();
$result = $stmt->get_result();

// Preencher o array de totais com os resultados
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tipo = $row['tipo_sanguineo'];
        if (array_key_exists($tipo, $totalAtendimentos)) {
            $totalAtendimentos[$tipo] = $row['total_atendimentos']; // Armazena o total de atendimentos por tipo sanguíneo
        }
    }
}

// Conversão para JSON
$jsonData = json_encode(array_values($totalAtendimentos));
?>


<div>
    <canvas id="tipoSanguineo" style="height: 360px; max-width: 100%; margin: 50px 0 0 50px;"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const atendimentosPorTipoSanguineo = <?php echo $jsonData; ?>;
        const ctx4 = document.getElementById('tipoSanguineo');

        new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], // Tipos sanguíneos
                datasets: [{
                    label: 'Total de Atendimentos por Tipo Sanguíneo',
                    data: atendimentosPorTipoSanguineo,
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
                        text: 'Total de Atendimentos por Tipo Sanguíneo'
                    }
                }
            }
        });
    });
</script>