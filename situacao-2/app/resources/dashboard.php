<div class="container">
    <div class="form-double">
        <?php
        include('app/resources/components/faixaEtariaGrafico.php');
        include('app/resources/components/funcionariosPorDepartamento.php');
        ?>
    </div>

    <div class="form-group">
        <form method="GET" action="" class="pesquisar">
            <input type="text" name="funcionario_id" class="form-control" value="" placeholder="Digite o ID do funcionário" required>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
        <?php

        if (isset($_GET['funcionario_id'])) {
            include('app/resources/components/historicoAusencias.php');
        } else {
            echo "Nenhum parâmetro passado :(";
        }
        ?>
    </div>
</div>