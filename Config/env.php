<?php 
$env = parse_ini_file(__DIR__ .'/../DadosOcultados.env');
foreach($env as $key => $value){
    $_ENV[$key] = $value;
}

?>