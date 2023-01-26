<?php
include('session.php');
header("Content-type: text/html; charset=utf-8");

if($date_now > $ciclo){
    $active_app = 1;
} else {
    $active_app = 0;
}

if ($active_app == 0) {
    header("location: ./wait");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['reserva_name'])) {
        $_SESSION['reserva_name'] = mysqli_real_escape_string($db, $_POST['reserva_name']);
        $set_name = 1;

        // Register Log
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        $stmt = $conn->prepare("INSERT INTO doecompre_log (user, ip, agent) VALUES (?,?,?);");
        $stmt->bind_param("sss", $user, $ip, $agent);

        $user = $_SESSION['reserva_name'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $stmt->execute();

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
if (isset($_SESSION['reserva_name'])) {
    $ses_sql = mysqli_query($db, "SELECT reserva, REPLACE(sum(valor),'.',',') total FROM doecompre WHERE status = 1 and reserva = '" . $_SESSION['reserva_name'] . "'");
    $row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);
    $valor_total = $row['total'];
} else {
    $valor_total = 0;
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
    <meta http-equiv="refresh" content="45">

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

    <?php
    if ($valor_total > 0) { ?>
        <div class="navbar-fixed">
            <nav class="grey lighten-3" role="navigation">
                <div class="nav-wrapper container">
                    <div class="row center">
                        <div class="col s12">
                            <span class="flow-text grey-text text-darken-3 center"><b>Total Compras R$ <?php echo $valor_total; ?></b></span>
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
            <div class="row">
                <h5 class="header col s12 light">
                    <?php 
                    if(isset($_SESSION['reserva_name'])){ ?>
                        <b><?php echo $_SESSION['reserva_name']; ?></b>, selecione os produtos que deseja:
                    <?php } ?>
                </h5>
            </div>
            <div class="table row">
                <h4>Doces</h4>
                <table class="striped">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>VALOR</th>
                            <th>RESERVAR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Doces' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center" id="reserva-<?php echo ($row["id"]) ?>">
                                    <?php if ($row["reserva"] != '-') { ?>
                                        <b><?php echo ($row["reserva"]) ?></b>
                                    <?php } else { ?>
                                        <input type="hidden" id="idProduto" name="idProduto" value="<?php echo ($row["id"]) ?>">
                                        <button id="btn-reservar" class="waves-effect waves-light btn modal-trigger btn-reservar" onclick="confirm_reserva(this)" idprod="<?php echo ($row["id"]) ?>" descprod="<?php echo ($row["produto"]) ?>" priceprod="<?php echo ($row["valor"]) ?>">Reservar</button>
                                    <?php } ?>
                                </td>
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
                            <th>RESERVAR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Salgados' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center" id="reserva-<?php echo ($row["id"]) ?>">
                                    <?php if ($row["reserva"] != '-') { ?>
                                        <b><?php echo ($row["reserva"]) ?></b>
                                    <?php } else { ?>
                                        <input type="hidden" id="idProduto" name="idProduto" value="<?php echo ($row["id"]) ?>">
                                        <button id="btn-reservar" class="waves-effect waves-light btn modal-trigger btn-reservar" onclick="confirm_reserva(this)" idprod="<?php echo ($row["id"]) ?>" descprod="<?php echo ($row["produto"]) ?>" priceprod="<?php echo ($row["valor"]) ?>">Reservar</button>
                                    <?php } ?>
                                </td>
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
                            <th>RESERVAR</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Diversos' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center" id="reserva-<?php echo ($row["id"]) ?>">
                                    <?php if ($row["reserva"] != '-') { ?>
                                        <b><?php echo ($row["reserva"]) ?></b>
                                    <?php } else { ?>
                                        <input type="hidden" id="idProduto" name="idProduto" value="<?php echo ($row["id"]) ?>">
                                        <button id="btn-reservar" class="waves-effect waves-light btn modal-trigger btn-reservar" onclick="confirm_reserva(this)" idprod="<?php echo ($row["id"]) ?>" descprod="<?php echo ($row["produto"]) ?>" priceprod="<?php echo ($row["valor"]) ?>">Reservar</button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php
            if ($valor_total > 0) { ?>
                <div class="row">
                    <h5 class="header col s12 light"><b>Total Reserva:</b> R$ <?php echo $valor_total; ?></h5>
                </div>
            <?php } ?>
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
            if (!isset($_SESSION['reserva_name'])) { ?>
                <div id="modal1" class="modal">
                    <form action="./" method="POST" class="s12">
                        <div class="modal-content">
                            <h4>Qual o seu nome?</h4>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input placeholder="Seu Nome" id="reserva_name" name="reserva_name" type="text" class="validate" required>
                                    <label for="reserva_name">Nome</label>
                                </div>
                            </div>
                            <div class="row">
                                <button type="submit" class="waves-effect waves-light btn">SALVAR</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>

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
        $(".button-collapse").sideNav();
    </script>

    <script>
        $(document).ready(function() {
            $('.modal').modal();
            $('#modal1').modal('open');
            $('#modal2').modal('open');
            $('#modal').modal({
                dismissible: false
            });
        });
        $("#form_name #reserva_name").focus();
    </script>

    <script>
        // SCRIPT DE COPY TO CLIPBOARD

        function copypix() {
            /* Get the text field */
            var PIXChave = "00020126580014BR.GOV.BCB.PIX013636ae18c3-d872-4b7b-86aa-0be9f688fade5204000053039865802BR5925CENTRO ESPIRITA BENEFICEN6009SAO PAULO622605224MRwmcu9uH8NPNesWr5OaX6304490B";

            /* Copy the text inside the text field */
            await navigator.clipboard.writeText(PIXChave);

        }
    </script>

    <script>
        function confirm_reserva(item){
                var idprod = $(item).attr("idprod");
                var descricao = $(item).attr("descprod");
                var preco = $(item).attr("priceprod");
                $("#desc_produto").html(descricao);
                $("#price_produto").html("R$ " + preco);
                $("a.modal_submit").attr("href", "./?id="+idprod);
                $('#modalconfirm').modal('open');
        }
    </script>

    <script>
        function realtime_reserva() {
            var data = $("input").serializeArray();

            var status_id = id_status(data);
            var response_status = JSON.parse(status_id);
            for (var i = 0; i < response_status.length; i++) {
                if (response_status[i].reserva != '-') {
                    $("#reserva-" + response_status[i].id).html("<b>" + response_status[i].reserva + "</b>")
                    console.log("Atualizado.");
                }
            }
        };

        function id_status(produtos) {
            var _status;
            $.ajax({
                type: "POST",
                url: "./ajax/get_idstatus.php",
                async: false,
                data: {
                    json: produtos
                },
                success: function(data) {
                    _status = data;
                },
                error: function(xhr) {
                    console.log(xhr.statusText + xhr.responseText);
                    //alert(xhr.statusText + xhr.responseText);

                }

            });
            return _status;
        }


        <?php
        if (isset($_SESSION['reserva_name'])) {
        ?>
            setInterval(function() {
                realtime_reserva();
            }, 1000);
        <?php } ?>
    </script>

</body>

</html>