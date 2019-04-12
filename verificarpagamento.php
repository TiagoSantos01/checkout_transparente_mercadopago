<?php
//Agora vamos fazer o nosso servidor verificar o status do pagamento

//primeiro de tudo nos faz a conexao com o banco de dados

include("conexao.php");
// Carregar a pasta do composer
require 'vendor/autoload.php';

// para carregar a biblioteca do PHPMailer para nos usar pra enviar pra gene toda vez q um pagamneto for aprovado
USE PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//copia o acess_token https://www.mercadopago.com/mlb/account/credentials
//e cola no $token
$token="acess_token";

//agora vamo pega o id_pagamentos com o status verificar ou em_analise no banco de dados
$sql=mysqli_query($conn,"SELECT * From tabela where status_='verificar' or status_='em_analise'");
$result=mysqli_num_rows($sql);
if($result!=0){
    while($verificar=mysqli_fetch_array($sql)){
        //o id do pagamento que o mercado pago mando pra voce via get na pagina recebeinfo.php
        $id_pagamentos=$verificar['id_pagamentos'];
        //aqui ele vai pesquisa no site do mercado pago o status do pagamento
        $arquivo=file_get_contents("https://api.mercadopago.com/v1/payments/$id_pagamentos?access_token=$token");
        //aqui ele descodifica o json  que o $arquivo retorna
        $json=json_decode($arquivo);
        //aqui ele verifica se status do pagamento se ta aprovado, caso esteja aprovado  vai muda o status do pagamento para aprovado
        if($json->status=="approved"){
        mysqli_query($conn,"UPDATE tabela SET status_='aprovado' where id_pagamentos='$id_pagamentos'");

            //agora vamo pega a infomações do comprado e do items comprado para enviar para gente quando o pagamento for aprovado via email
            //infomação do cliente
            $infomacao=$json->metadata[0];
            //items comprado
            $item=$json->metadata[1];
            
            //agora vamo fazer a parte que enviar pra gente uma email com as infomações  do comprado e do items comprado
            //vou usar o PHPMailer

  //Primeiro vamo montar o html do email 
  $info="<table>
  <tr><td>ID do cliente :</td><td>".$infomacao[0]->id_cliente."</td></tr>
  <tr><td>Nome :</td><td>".$infomacao[0]->nome."</td></tr>
  <tr><td>Email :</td><td>".$infomacao[0]->email."</td></tr>
  <tr><td>Endereço :</td><td>".$infomacao[0]->endereco."</td></tr>
  <tr><td>Numero :</td><td>".$infomacao[0]->numero."</td></tr>
  <tr><td>Bairro :</td><td>".$infomacao[0]->bairro."</td></tr>
  <tr><td>Cep :</td><td>".$infomacao[0]->cep."</td></tr>
  <tr><td>Complemento :</td><td>".$infomacao[0]->complemento."</td></tr>
  <tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
  

//agora vamo fazer um count() para saber a quantidade items comprados e depois um for() para guarda todos os pedidos numa varival só;
$total=count($item);
$items="
<tr><td colspan='2'align=center>Item Comprados</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
";
for($i=0;$i<$total;$i++){
  $items="$items

<tr><td>ID do produto: </td><td>".$item[$i]->item_id."</td></tr>
<tr><td>Quantidade:</td><td> ".$item[$i]->unidade."</td></tr>
<tr><td>Valor:</td><td> ".$item[$i]->valor."</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
";
}    
$items="$items </table>";

//agora vamo junta infomação junto com items comprado

$mensagem="$info $items";


$mail = new PHPMailer(true);

try {
    //Configurações do servidor que vai enviar o email
    $mail->SMTPDebug = 2;                                       
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.seusite.com';
   // se o email vai ser autenticado
    $mail->SMTPAuth   = true;                                   
   //o email e a senha que vai autenticar seu email
    $mail->Username   = 'usuario@seusite.com';                     
    $mail->Password   = 'senha';                               
// o tipo de criptografia pode ser também ssl
    $mail->SMTPSecure = 'tls';                                  
   // a porta do seu servidor de saida
    $mail->Port= 587;   
    //tipo de conjuto de caracteres                                 
    $mail->CharSet="UTF-8";

    // o email que vai enviar a mensagem e o nome que vai aparece para intedificar voce
    $mail->setFrom('usuario@seusite.com', 'MinhaLoja');
    // o email que vai receber a mensagem
    $mail->addAddress('usuario@seusite.com'); 
    
    // para habilitar o html
    $mail->isHTML(true); 
    //o titulo do email                                 
    $mail->Subject = 'Nova compra!';
  // aqui vai a mensagem em html que nós fez
    $mail->Body= $mensagem;
// agora vamo enviar a mensagem
    $mail->send();
    echo 'Mensagem enviada com sucesso!';
} catch (Exception $e) {
    echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";


}         



}
        //aqui ele verifica se status do pagamento se ta em analise, caso esteja ainda em analise  vai muda o status do pagamento para em analise
        if($json->status=="in_process"){
            mysqli_query($conn,"UPDATE tabela SET status_='em_analise' where id_pagamentos='$id_pagamentos'");  
        }
        //aqui ele verifica se status do pagamento foi rejeitado, caso seja reprovado  vai muda o status do pagamento para rejeitado
        if($json->status=="rejected"){
            mysqli_query($conn,"UPDATE tabela SET status_='rejeitado' where id_pagamentos='$id_pagamentos'");            
        }
}
}

?>
