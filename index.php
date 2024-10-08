<?php

include("app/Database/connect.php");

//OBTÉM A ROTA DA URL
function obterRota()
{
  $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $rota = explode("/", trim($url, '/'));

  //DEFINE A ROTA PADRÃO CASO ESTIVER VAZIA
  if (empty($rota[0])) {
    $rota[0] = 'pagina-inicial';
  }

  //RETORNA A ROTA
  return $rota[0];
}

//INCLUI AS PÁGINAS RETORNADAS PELO OBTERROTA()
function incluirPagina($pagina)
{

  //DEFINE O CAMINHO DO ARQUIVO DA PÁGINA A SER RENDERIZADA
  $arquivoPagina = "app/resources/{$pagina}.php";

  //VERIFICA SE O ARQUIVO DA PÁGINA NÃO EXISTE
  if (!file_exists($arquivoPagina)) {
    echo "Página não encontrada.";
    return;
  }

  //RENDERIZA A PÁGINA
  include $arquivoPagina;
}

//EXECUTA A FUNÇÃO OBTERROTA()
$rota = obterRota();

//DEFINE O TITULO DA PÁGINA
$tituloPagina = ucfirst(str_replace('-', ' ', $rota));
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./app/resources/css/style.css">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
  <title><?php $tituloPagina ?></title>
</head>

<body>
  <aside>
    <nav class="navbar">
      <ul class="nav-menu itens-menu">
        <li class="nav-item">
          <a href="/" class="nav-link"><span class="material-symbols-outlined">
              home
            </span>Pagina Inicial</a>
        </li>
        <li class="nav-item">
          <a href="/doe" class="nav-link"><span class="material-symbols-outlined">
              dashboard
            </span>Dashboard</a>
        </li>
        </form>
      </ul>
    </nav>
  </aside>
  <main>

    <?php
    //INCLUI AS PÁGINAS RETORNADAS PELO OBTER ROTA()
    incluirPagina($rota);
    ?>
  </main>
</body>

</html>