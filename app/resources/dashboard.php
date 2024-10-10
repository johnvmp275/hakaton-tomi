<?php
include("app/Database/connect.php");

// Consulta para trazer os locais de atendimento
$sql_locais = "SELECT id_local, nome_local FROM locais_atendimento";
$result_locais = $conn->query($sql_locais);

// Função para buscar atendimentos por local
function getAtendimentosPorLocal($conn, $id_local) {
    $sql = "SELECT 
                pessoas.nome_paciente AS nomePaciente, 
                atendimentos.data_atendimento, 
                atendimentos.atendente_nome, 
                pessoas.genero, 
                atendimentos.observacao 
            FROM atendimentos 
            JOIN pessoas ON atendimentos.pessoa_id = pessoas.idPaciente
            WHERE atendimentos.localAtendimento = ? 
            ORDER BY atendimentos.data_atendimento DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $id_local);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./app/resources/css/style-dashboard.css">
</head>

<body>

    <main>
        <div class="container">
            <h1>Dashboard de Atendimentos</h1>

            <?php
            // Verificar se há locais de atendimento
            if ($result_locais->num_rows > 0) {
                while ($local = $result_locais->fetch_assoc()) {
                    $id_local = $local['id_local'];  // Usando o ID do local
                    echo "<h2>Local de Atendimento: " . $local['nome_local'] . "</h2>";

                    // Buscar os atendimentos desse local
                    $atendimentos = getAtendimentosPorLocal($conn, $id_local);

                    if ($atendimentos && $atendimentos->num_rows > 0) {
                        echo "<table border='1'>
                                <thead>
                                    <tr>
                                        <th>Nome da Pessoa</th>
                                        <th>Data e Hora</th>
                                        <th>Atendente</th>
                                        <th>Sexo</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        while ($atendimento = $atendimentos->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $atendimento['nomePaciente'] . "</td>
                                    <td>" . $atendimento['data_atendimento'] . "</td>
                                    <td>" . $atendimento['atendente_nome'] . "</td>
                                    <td>" . $atendimento['genero'] . "</td>
                                    <td>" . $atendimento['observacao'] . "</td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>Não há atendimentos para este local.</p>";
                    }
                }
            } else {
                echo "<p>Nenhum local de atendimento encontrado.</p>";
            }

            // Fechar a conexão com o banco de dados
            $conn->close();
            ?>
        </div>
    </main>

</body>

</html>
