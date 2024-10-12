<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Obter o ano e o local_id da query string
$dataAno = $_GET['dataAnoAtendimento'] ?? date('Y'); // Se não fornecido, usar o ano atual
$local_id = $_GET['local_atendimento'] ?? null;

// Inicializa variáveis para contagem de faixas etárias
$ate_6_count = 0;
$ate_12_count = 0;
$ate_18_count = 0;
$ate_30_count = 0;
$ate_40_count = 0;
$ate_60_count = 0;
$ate_80_count = 0;
$acima_80_count = 0;

// Consulta para obter a data de nascimento e calcular a idade, filtrando pelo ano do atendimento
if (is_numeric($local_id)) {
    // Se um local específico foi selecionado, contar apenas os atendimentos desse local no ano escolhido
    $sql_faixa_etaria = "
        SELECT pessoas.dataNasc
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        INNER JOIN locais_atendimento ON atendimentos.localAtendimento = locais_atendimento.id_local
        WHERE locais_atendimento.id_local = ? 
          AND YEAR(atendimentos.data_atendimento) = ?"; // Filtrar pelo ano do atendimento
    $stmt = $conn->prepare($sql_faixa_etaria);
    $stmt->bind_param("ii", $local_id, $dataAno);
} else {
    // Se nenhum local foi selecionado, considerar todos os atendimentos do ano escolhido
    $sql_faixa_etaria = "
        SELECT pessoas.dataNasc
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        WHERE YEAR(atendimentos.data_atendimento) = ?"; // Filtrar pelo ano do atendimento
    $stmt = $conn->prepare($sql_faixa_etaria);
    $stmt->bind_param("i", $dataAno);
}

// Executa a consulta
$stmt->execute();
$result = $stmt->get_result();

// Calcular a idade e contar por faixa etária
while ($row = $result->fetch_assoc()) {
    // Verificar se 'dataNasc' está presente e calcular a idade
    if (isset($row['dataNasc']) && !empty($row['dataNasc'])) {
        $ano_nascimento = date("Y", strtotime($row['dataNasc']));
        $idade = $dataAno - $ano_nascimento; // Calcular a idade com base no ano de atendimento

        // Classificar a idade nas faixas etárias
        if ($idade >= 0 && $idade <= 6) {
            $ate_6_count++;
        } elseif ($idade >= 13 && $idade <= 12) {
            $ate_12_count++;
        } elseif ($idade >= 13 && $idade <= 18) {
            $ate_18_count++;
        } elseif ($idade >= 18 && $idade <= 30) {
            $ate_30_count++;
        } elseif ($idade >= 30 && $idade <= 40) {
            $ate_40_count++;
        } elseif ($idade >= 41 && $idade <= 60) {
            $ate_60_count++;
        } elseif ($idade >= 61 && $idade <= 80) {
            $ate_80_count++;
        } elseif ($idade > 80) {
            $acima_80_count++;
        }
    } else {
        // Se o campo 'dataNasc' estiver ausente ou vazio, ignorar esse registro
        continue;
    }
}

// Fechar a conexão
$stmt->close();
$conn->close();

// Dados em formato JSON para passar ao JavaScript
$faixaEtariaData = [
    'Até 6' => $ate_6_count,
    'Até 12' => $ate_12_count,
    'Até 18' => $ate_18_count,
    'Até 30' => $ate_30_count,
    'Até 40' => $ate_40_count,
    'Até 60' => $ate_60_count,
    'Até 80' => $ate_80_count,
    'Acima de 80' => $acima_80_count
];
$jsonData = json_encode($faixaEtariaData);
?>

<div>
    <div class="form-group" style="max-width:200px">
        <input type="date" id="dataAnoAtendimento" name="dataAnoAtendimento" value="" onchange="enviarDataAnoAtendimento()">
    </div>
    <canvas id="faixaEtaria" style="max-height: 360px; max-width: 1100px;"></canvas>
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
                    label: 'Total de Usuários por Faixa Etária',
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
                        text: 'Total de Usuários por Faixa Etária'
                    }
                }
            }
        });
    });
</script>