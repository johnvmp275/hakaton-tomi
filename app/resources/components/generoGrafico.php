<?php
include("app/Database/connect.php"); // Inclui a conexão com o banco de dados

// Consulta para contar os atendimentos por gênero
if (is_numeric($local_id)) {
    // Se um local específico foi selecionado, contar apenas os atendimentos desse local
    $sql_count = "
        SELECT 
            pessoas.idPaciente,
            pessoas.genero,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        INNER JOIN locais_atendimento ON atendimentos.localAtendimento = locais_atendimento.id_local
        WHERE locais_atendimento.id_local = ?
        GROUP BY pessoas.idPaciente, pessoas.genero";
    $stmt = $conn->prepare($sql_count);
    $stmt->bind_param("i", $local_id);
} else {
    // Se nenhum local foi selecionado, contar atendimentos de todos os locais
    $sql_count = "
        SELECT 
            pessoas.idPaciente,
            pessoas.genero,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM pessoas
        INNER JOIN atendimentos ON pessoas.idPaciente = atendimentos.pessoa_id
        GROUP BY pessoas.idPaciente, pessoas.genero";
    $stmt = $conn->prepare($sql_count);
}

$stmt->execute();
$result_count = $stmt->get_result();
$counts = $result_count->fetch_all(MYSQLI_ASSOC);

// Inicializa contadores
$masculino_count = 0;
$feminino_count = 0;
$outros_count = 0;

// Contar os atendimentos por gênero
foreach ($counts as $row) {
    switch ($row['genero']) {
        case 'Masculino':
            $masculino_count += $row['total_atendimentos'];
            break;
        case 'Feminino':
            $feminino_count += $row['total_atendimentos'];
            break;
        case 'Outro':
            $outros_count += $row['total_atendimentos'];
            break;
    }
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>


    <div class="form-group" style="max-height: 360px; max-width: 450px; margin-bottom: 100px;">
        <label for="" class="subtitle">Atendimentos por Gênero:</label>
        <canvas id="generoGrafico" style="margin-left:40px"></canvas>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById("generoGrafico");

        if (ctx) {
            new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Feminino", "Masculino", "Outros"],
                    datasets: [{
                        label: "Atendimentos por Gênero",
                        data: [<?= $feminino_count ?>, <?= $masculino_count ?>, <?= $outros_count ?>],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                        borderWidth: 1,
                    }, ],
                },
            });
        }

    });
</script>