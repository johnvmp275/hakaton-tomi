<?php
//INCLUI A CONEXÃO COM O BANCO DE DADOS 
include("app/Database/connect.php"); 

//OBTÉM O VALOR DO PARÂMETRO $local_id
$local_id = $_GET['local_atendimento'] ?? null;

//VALIDA O PARAMETRO $local_id
if (is_numeric($local_id)) {
    //CONUSLTA TODOS OS ATENDIMENTOS RELACIOANDOS AO $local_id
    $sql_count = "
        SELECT 
            acoes.id_acao,
            acoes.nome_acao AS acao_nome_acao,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM acoes
        INNER JOIN atendimentos ON acoes.id_acao = atendimentos.id_acao
        INNER JOIN locais_atendimento ON atendimentos.localAtendimento = locais_atendimento.id_local
        WHERE locais_atendimento.id_local = ?
        GROUP BY acoes.id_acao, acoes.nome_acao";
    $stmt = $conn->prepare($sql_count);
    $stmt->bind_param("i", $local_id);
} else {
    //CONSUTA O ATENDIMENTO DE TODOS OS LOCAIS SE O PARAMETRO $local_id NÃO FOR VALIDO
    $sql_count = "
        SELECT 
            acoes.id_acao,
            acoes.nome_acao AS acao_nome_acao,
            COUNT(atendimentos.idAtendimento) AS total_atendimentos
        FROM acoes
        INNER JOIN atendimentos ON acoes.id_acao = atendimentos.id_acao
        GROUP BY acoes.id_acao, acoes.nome_acao";
    $stmt = $conn->prepare($sql_count);
}

$stmt->execute();
$result_count = $stmt->get_result();
$counts = $result_count->fetch_all(MYSQLI_ASSOC);

$acoes_data = [];
foreach ($counts as $row) {
    $acoes_data[] = [
        'nome_acao' => $row['acao_nome_acao'],
        'total' => $row['total_atendimentos'],
    ];
}

$stmt->close();
$conn->close();
?>

<div class="form-group" style=" max-width: 360px;">
    <label for="dataAno" class="subtitle" style="width: 480px;">Atendimentos por Ação:</label>
    <canvas id="acoesGrafico"></canvas>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx3 = document.getElementById("acoesGrafico");


        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        if (ctx3) {
            const labels = <?= json_encode(array_column($acoes_data, 'nome_acao')) ?>;
            const data = <?= json_encode(array_column($acoes_data, 'total')) ?>;

            new Chart(ctx3, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Atendimentos por Ação",
                        data: data,
                        backgroundColor: labels.map(() => getRandomColor()), // Gera cores aleatórias para cada ação
                        borderWidth: 1,
                    }],
                },
            });
        }

    });
</script>
