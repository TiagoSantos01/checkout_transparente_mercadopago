
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>checkout transparente do mercadopago</title>
     <!-- Para pode usar a biblioteca do MercadoPago -->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

  </head>
  <body>
<h3>Formulário de pagamento</h3>

   <form action="pagar.php" method="post" id="pay" name="pay" >
    <fieldset>
        <ul>
            <li>
                <label for="email">E-mail:</label>
                <input id="email" name="email" value="comprador+272110086@mercadopago.com" type="email" placeholder="digite seu email"/>
            </li>
            <li>
                <label for="cardNumber">Número do cartão de crédito:</label>
                <input type="text" id="cardNumber" data-checkout="cardNumber" placeholder="4509 9535 6623 3704" value="4235647728025682" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off />
                <span id="bandeira"></span>
            </li>
            <li>
                <label for="securityCode">Dígito verificador de segurança:</label>
                <input type="text" id="securityCode" data-checkout="securityCode" placeholder="123" value="123" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off />
            </li>
            <li>
                <label for="cardExpirationMonth">Mês de expiração:</label>
                <input type="text" id="cardExpirationMonth" data-checkout="cardExpirationMonth" placeholder="12" value="12" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off />
            </li>
            <li>
                <label for="cardExpirationYear">Ano de expiração:</label>
                <input type="text" id="cardExpirationYear" data-checkout="cardExpirationYear" placeholder="2015" value="2022" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off />
            </li>
            <li>
                <label for="cardholderName">Nome do portador do cartão:</label>
                <input type="text" id="cardholderName" data-checkout="cardholderName" placeholder="APRO" value="APRO" />
            </li>
            <input data-checkout="docType" type="hidden" value="CPF"/>
            <input data-checkout="siteId" type="hidden" value="MLB"/>
            <li>
                <label for="docNumber">CPF:</label>
                <input type="text" id="docNumber" data-checkout="docNumber" placeholder="12345678" value="19119119100" />
            </li>
 
        </ul>
        <input type="hidden" name="amount" id="amount" value=""/>
        <input type="submit" value="Pagar!" />
    </fieldset>
</form>

  <br/>
  <br/>

  <script type="text/javascript">
  //copia a sua Public key que está aqui https://www.mercadopago.com/mlb/account/credentials
  // e cole no Public_key.
    Mercadopago.setPublishableKey("Public_key"); 


    function addEvent(el, eventName, handler){
    if (el.addEventListener) {
           el.addEventListener(eventName, handler);
    } else {
        el.attachEvent('on' + eventName, function(){
          handler.call(el);
        });
    }
  };
  //para retornar os 6 numero primeiro do cartão
    function getBin() {
        var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
        return ccNumber.value.replace(/[ .-]/g, '').slice(0, 6);
    };

    //Para verificar se tem mais de 6 numero no cartão
    function guessingPaymentMethod(event) {
        var bin = getBin();
    
        if (event.type == "keyup") {
            if (bin.length >= 6) {
                Mercadopago.getPaymentMethod({
                    "bin": bin
                }, setPaymentMethodInfo);
            }
        } else {
            setTimeout(function() {
                if (bin.length >= 6) {
                    Mercadopago.getPaymentMethod({
                        "bin": bin
                    }, setPaymentMethodInfo);
                }
            }, 100);
        }
    };
    
//aqui recebe o callback do status que indentificam o meio de pagamento e o banco emissor e a bandeira do cartão
    function setPaymentMethodInfo(status, response) {
        if (status == 200) {
           
            var form = document.querySelector('#pay');
    
            if (document.querySelector("input[name=paymentMethodId]") == null) {
                var paymentMethod = document.createElement('input');
                paymentMethod.setAttribute('name', "paymentMethodId");
                paymentMethod.setAttribute('type', "hidden");
                paymentMethod.setAttribute('value', response[0].id);
                form.appendChild(paymentMethod);
            } else {
                document.querySelector("input[name=paymentMethodId]").value = response[0].id;
            }
            //colocar a bandeira do cartão do lado dos numero do cartão
                var img = "<img src='" + response[0].thumbnail + "' align='center' style='margin-left:10px;' ' >";
                $("#bandeira").empty();
                $("#bandeira").append(img);
                
                
             
                
        }
    };
    
    addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'keyup', guessingPaymentMethod);
    addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'change', guessingPaymentMethod);
    
    doSubmit = false;
    addEvent(document.querySelector('#pay'),'submit',doPay);
    //para criar o card_token
    function doPay(event){
    event.preventDefault();
      if(!doSubmit){
          var $form = document.querySelector('#pay');
          
          Mercadopago.createToken($form, sdkResponseHandler); 
  
          return false;
      }
    };
    // verficar se o numero do cartão estão ok
    function sdkResponseHandler(status, response) {
    if (status != 200 && status != 201) {
        alert("verify filled data");
    }else{
       
        var form = document.querySelector('#pay');
        var card = document.createElement('input');
        card.setAttribute('name',"token");
        card.setAttribute('type',"hidden");
        card.setAttribute('value',response.id);
        form.appendChild(card);
        doSubmit=true;
        form.submit();
    }
  };
  </script>
  </body>
</html>