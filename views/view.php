<?php
include('session.php');
header("Content-type: text/html; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['reserva_name'])) {
        $_SESSION['reserva_name'] = mysqli_real_escape_string($db, $_POST['reserva_name']);
        $set_name = 1;
    } else {
        $set_name = 0;
    }
}

if (!empty($_GET["id"]) and isset($_SESSION['reserva_name'])) {
    $id_produto = $_GET["id"];

    if (is_numeric($id_produto) == true) {

        //Check availiable
        $ses_sql = mysqli_query($db, "SELECT id, produto, reserva FROM doecompre WHERE id = '" . $id_produto . "'");
        $row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);
        $reserva = $row['reserva'];

        if ($reserva == '-') {
            $sql_update = "UPDATE doecompre SET reserva = '" . $_SESSION['reserva_name'] . "' WHERE id = " . $id_produto . ";";
            mysqli_query($db, $sql_update);
            //echo $sql_update;
            header("location: ./");
        } else {
            $msg_status = "Produto já reservado.";
        }
    } else {
        header("location: ./");
    }
}

//Valor Total
$ses_sql = mysqli_query($db, "SELECT count(id) quant, REPLACE(sum(valor),'.',',') total FROM doecompre WHERE status = 1;");
$row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);
$valor_total = $row['total'];
$qtd_total = $row['quant'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Doe&Compre</title>
    <link rel="icon" type="image/x-icon" href="../img/logo.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--<meta http-equiv="refresh" content="30">-->

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="../css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
</head>

<body>
    <div class="navbar-fixed">
        <nav class="teal lighten-1" role="navigation">
            <div class="nav-wrapper container">
                <a id="logo-container" href="./" class="brand-logo">
                    <img src="../img/logo.png" width="50px" height="50px">
                </a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="#">Menu</a></li>
                </ul>

                <ul id="nav-mobile" class="sidenav">
                    <li><a href="#">Menu</a></li>
                </ul>
                <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            </div>
        </nav>
    </div>

    <?php
    if ($valor_total > 0) { ?>
        <div class="navbar-fixed">
            <nav class="grey lighten-3" role="navigation">
                <div class="nav-wrapper container">
                    <div class="row center">
                        <div class="col s12">
                            <span class="flow-text grey-text text-darken-3 center"><b><?php echo $qtd_total; ?> Produtos - Total R$ <?php echo $valor_total; ?></b></span>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    <?php } ?>

    <div class="section no-pad-bot" id="index-banner">
        <div class="container">
            <?php //echo $_SESSION['reserva_name']; 
            ?>
            <p id="sql"></p>
            <h2 class="header center green-text">Menu Apetitoso</h2>

            <div class="table row">
                <h4>Doces</h4>
                <table class="striped">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Doces' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
            <div class="table row">
                <h4>Salgados</h4>
                <table class="striped">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Salgados' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="table row">
                <h4>Diversos</h4>
                <table class="striped">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Diversos' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <br><br>

            <div id="modalconfirm" class="modal">
                <div class="modal-content">
                    <h5>Confirmar a reserva do produto?</h5>
                    <p>
                        <b>Produto:</b><br>
                        <span id="desc_produto"></span>
                        <br>
                        <b>Preço:</b><br>
                        <span id="price_produto"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <a class="modal-close waves-effect waves-red btn-flat">Não</a>
                    <a id="modal_submit" href="#!" class="modal-close waves-effect waves-green btn-flat modal_submit"><b>Sim</b></a>
                </div>
            </div>

            <?php
            if (isset($msg_status)) { ?>
                <div id="modal2" class="modal">
                    <div class="modal-content">
                        <h4>Aviso</h4>
                        <p><?php echo $msg_status; ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="./" class="modal-close waves-effect waves-green btn-flat">Ok</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>



    <div class="container">
        <div class="section">

            <!--   Icon Section   -->
            <div class="row">
                <div class="col s12 m6">
                    <div class="icon-block center">
                        <img src="../img/qrcode-pix.png" alt="PIX" width="40%" class="center">
                        <h5 class="center">PIX: Doe & Compre</h5>

                        <p class="light">Utilize o QR Code acima para efetuar o pagamento, em seguida encaminhe o
                            comprovate para o Tesoureiro, ou utilize a chave abaixo: <br><br>
                        <h5>36ae18c3-d872-4b7b-86aa-0be9f688fade</h5>
                        <button onclick="copypix()" class="waves-effect waves-light btn"><i class="material-icons left">content_copy</i>Copiar chave PIX</button>
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
    <script src="../js/materialize.js"></script>
    <script src="../js/init.js"></script>


    <script>
        // SCRIPT DE COPY TO CLIPBOARD

        function copypix() {
            /* Get the text field */
            var PIXChave = "00020126580014BR.GOV.BCB.PIX013636ae18c3-d872-4b7b-86aa-0be9f688fade5204000053039865802BR5925CENTRO ESPIRITA BENEFICEN6009SAO PAULO622605224MRwmcu9uH8NPNesWr5OaX6304490B";

            /* Copy the text inside the text field */
            //await navigator.clipboard.writeText(PIXChave);
            navigator.clipboard.writeText(PIXChave);

        }
    </script>


</body>

</html>