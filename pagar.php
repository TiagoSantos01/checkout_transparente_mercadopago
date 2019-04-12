<?php
// Agora precissamos da SDKs do mercado ago
// composer require "mercadopago/dx-php:dev-master"  

//Agora precisamos importat a biblioteca da pasta Composer.
require __DIR__  . '/vendor/autoload.php';

  //copia a sua Access token: que está aqui https://www.mercadopago.com/mlb/account/credentials
  // e cole no access_token
    MercadoPago\SDK::setAccessToken("access_token");
  
   
    $payment = new MercadoPago\Payment();
    //valor a ser pago
    $payment->transaction_amount = 161;
    //token do cartão
    $payment->token = $_REQUEST['token'];

    //descrição dos item comprado e a informações do cliente
    $payment->metadata= array(
    
      ['{ 
          "id_cliente":"123",
          "endereco":"rua",
          "bairro":"bairro legal",
          "numero":"dfd",
          "cep":"dfdf",
          "complemento":"casa",
          "email":"fdsfds",
          "nome":"fdfdsf",
      }'],
      array('{
        "item_id":"12",
      "unidade":"1",
      "valor":"2,00",
  }',
  '{
      "item_id":"13",
      "unidade":"1",
      "valor":"4,00",  
            
      }')  
        );
    
    $payment->installments = 1;
    //email do cliente
    $payment->payer = array(
    "email" => "cliente@email.com"
    );
    
    $payment->save();

    // Print do status da compra
    //
    // Pagamento aprovado.
    if($payment->status=="approved"){

    }

    // Pagamento pendente.
    if($payment->status=="in_process"){

    }

    // Recusado, ligar para autorizar.
    // Recusado por saldo insuficiente.
    // Recusado por código de segurança.
    // Recusado por data de validade.
    // Recusado por erro no formulário.
    // Recusado geral.

 if($payment->status=="rejected"){

    }


?>