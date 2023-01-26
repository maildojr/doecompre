<?php
include('config.php');
header("Content-type: text/html; charset=utf-8");

if($active_app == 1){
    header("location: ./");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Doe&Compre</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
</head>

<body>
    <div class="navbar-fixed">
        <nav class="teal lighten-1" role="navigation">
            <div class="nav-wrapper container">
                <a id="logo-container" href="./" class="brand-logo">
                    <img src="logo.png" width="50px" height="50px">
                </a>

                <ul class="right hide-on-med-and-down">
                    <li><a href="./configurar"><i class="material-icons">settings</i></a></li>
                </ul>

                <ul id="nav-mobile" class="sidenav">
                    <li><a href="./configurar"><i class="material-icons">settings</i></a></li>
                </ul>
                <a href="./configurar" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">settings</i></a>
            </div>
        </nav>
    </div>
    
    <div class="section no-pad-bot" id="index-banner">
        <div class="container">
            <h2 class="header center green-text">Aguarde! Em breve o Menu será liberado...</h2>
            <div class="row">
                <h5 class="header col s12 light" id="countdown"></h5>
            </div>
        </div>
    </div>
    
    
    

    <div class="container">
        <div class="section">

            <div class="row">

            </div>

            <!--   Icon Section   -->
            <div class="row">
                <div class="col s12 m6">
                    <div class="icon-block center">
                        <img src="qrcode-pix.png" alt="PIX" width="40%" class="center">
                        <h5 class="center">PIX: Doe & Compre</h5>

                        <p class="light">Utilize o QR Code acima para efetuar o pagamento, em seguida encaminhe o
                            comprovate para o Tesoureiro, ou utilize a chave abaixo: <br><br>
                        <h5>36ae18c3-d872-4b7b-86aa-0be9f688fade</h5>
                        <a class="waves-effect waves-light btn" href="javascript:copypix()"><i class="material-icons left">content_copy</i>Copiar chave PIX</a>
                        </p>
                    </div>
                </div>

                <div class="col s12 m6">
                    <div class="icon-block center">
                        <a href="https://api.whatsapp.com/send?phone=5521981559898&text=Ol%C3%A1+Marcelo%2C%0D%0ASegue+Pagamento+Doe%26Compre.&source=&data="><img width="40%" src="whatsapp.png" alt="Whatsapp"></a>
                        <h5 class="center">Comunique seu pagamento</h5>

                        <p class="light">Clique na imagem acima para encaminhar o comprovante de pagamento para o Tesoureiro via WhatAapp.</p>
                    </div>
                </div>
            </div>

        </div>
        <br><br>
    </div>

    <footer class="page-footer orange">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">Doe & Compre</h5>
                    <p class="grey-text text-lighten-4">Seu apoio vale muito!</p>


                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                Criado por Maildo Junior
            </div>
        </div>
    </footer>


    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="js/materialize.js"></script>
    <script src="js/init.js"></script>

    <script>
        // SCRIPT DE COPY TO CLIPBOARD
        
    function copypix() {
        /* Get the text field */
        var PIXChave = "00020126580014BR.GOV.BCB.PIX013636ae18c3-d872-4b7b-86aa-0be9f688fade5204000053039865802BR5925CENTRO ESPIRITA BENEFICEN6009SAO PAULO622605224MRwmcu9uH8NPNesWr5OaX6304490B";

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(PIXChave);

    }
    </script>

<script>
// Set the date we're counting down to
var countDownDate = new Date("<?php echo $ciclo?>").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="demo"
  document.getElementById("countdown").innerHTML = "Faltam " + days + " dias " + hours + ":"
  + minutes + ":" + seconds + " para liberação para as compras...";

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("countdown").innerHTML = "Ativo";
    window.location.href = "http://doecompre.cooperativasolar.net.br/";
  }
}, 5000);
</script>

</body>

</html>