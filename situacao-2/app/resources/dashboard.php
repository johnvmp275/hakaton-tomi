<div class="container">
    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'graficos')">Gráficos</button>
        <button class="tablinks" onclick="openCity(event, 'funcionario')">Funcionário</button>
    </div>

    <div class="tabcontent" id="graficos">
        <div class="form-double">
            <?php
            include('app/resources/components/faixaEtariaGrafico.php');
            include('app/resources/components/funcionariosPorDepartamento.php');
            ?>
        </div>
    </div>

    <div class="tabcontent" id="funcionario">
        <div class="form-group">
            <div class="form-row">
                <form method="GET" action="" class="pesquisar">
                    <input type="text" name="funcionario_id" class="form-control" value="" placeholder="ID do funcionário" required>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </form>
            </div>
            <?php

            if (isset($_GET['funcionario_id'])) {
                include('app/resources/components/historicoAusencias.php');
            } else {
                echo "Nenhum parâmetro passado :(";
            }
            ?>
        </div>
    </div>
</div>