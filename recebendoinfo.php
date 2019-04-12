<?php
// notificações do mercadopago
// entra nesse link https://www.mercadopago.com/mlb/account/webhooks
// no Modo Sandbox voce dever colocar seu dominio e depois a pagina que vai receber o GET
// ex: meusite.com/receberinfo.php
// também serve pro Modo Produção 
// escolher evento de pagamentos


//depois de ter feito isso você puxar o arquivo com a conexão com o servidor.
include("conexao.php");

//aqui a pagina pega o GET que o mercadopago envia e manda para o seu banco de dados;
mysqli_query($conn,"INSERT INTO tabela (id_pagamentos,status_)values('".$_GET['data_id']."','verificar')");

?>