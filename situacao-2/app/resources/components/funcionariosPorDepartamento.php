<?php
include("app/Database/connect.php");

// Inicializar um array para armazenar os totais de funcionários por departamento
$totalFuncionarios = [];

// Consulta para contar funcionários por departamento, limitando a 5 departamentos
$query = "SELECT 
              d.nome AS departamento, 
              COUNT(f.id) AS total_funcionarios 
          FROM 
              departamentos d 
          LEFT JOIN 
              funcionarios f ON d.id = f.departamento_id 
          GROUP BY 
              d.id 
          LIMIT 5"; // Limitar a 5 departamentos

// Prepare a consulta
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Preencher o array de totais com os resultados
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departamento = $row['departamento'];
        $totalFuncionarios[$departamento] = $row['total_funcionarios']; // Armazena o total de funcionários por departamento
    }
}

// Conversão para JSON
$jsonData = json_encode(array_values($totalFuncionarios));
$departamentoLabels = json_encode(array_keys($totalFuncionarios)); // Obter os nomes dos departamentos
?>

<div>
    <canvas id="funcionariosPorDepartamento" style="min-height: 360px; max-width: 360px;"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById("funcionariosPorDepartamento");

        if (ctx) {
            new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: <?php echo $departamentoLabels; ?>, // Nomes dos departamentos
                    datasets: [{
                        label: "Total de Funcionários por Departamento",
                        data: <?php echo $jsonData; ?>, // Total de funcionários por departamento
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'], // Cores para os setores
                        borderWidth: 1,
                    }],
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Total de Funcionários por Departamento'
                        }
                    }
                }
            });
        }
    });
</script>
