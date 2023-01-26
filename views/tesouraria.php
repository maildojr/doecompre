<?php
include('session.php');
header("Content-type: text/html; charset=utf-8");

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

    <div class="section no-pad-bot" id="index-banner">
        <div class="container">
            <?php //echo $_SESSION['reserva_name']; 
            ?>
            <p id="sql"></p>
            <h2 class="header center green-text">Tesouraria</h2>

            

            <?php
                $ses_sql = mysqli_query($db, "SELECT 
                    c.id, 
                    DATE_FORMAT(c.ciclo, '%d/%m/%Y') as data_ciclo, 
                    sum(p.valor) valor_ciclo
                    FROM doecompre_ciclo c JOIN doecompre p ON c.id = p.ciclo 
                    WHERE p.reserva != '-' 
                    GROUP BY c.ciclo 
                    HAVING sum(p.valor) > 0 
                    ORDER BY c.ciclo DESC;");

                while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                    <hr>
                    <div class="table row">
                        <h4>Rodada <?php echo ($row["data_ciclo"]) ?></h4><br><h5>Valor Total R$ <?php echo ($row["valor_ciclo"]) ?></h5>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $ses_sql2 = mysqli_query($db, "SELECT reserva, sum(valor) valor_total FROM doecompre WHERE ciclo = ".$row["id"]." and reserva != '-' GROUP BY reserva ORDER BY reserva;");

                                while ($row2 = mysqli_fetch_array($ses_sql2, MYSQLI_ASSOC)) { ?>
                                    <tr>
                                        <td><b><?php echo ($row2["reserva"]) ?></b></td>
                                        <td class="right"><?php echo ($row2["valor_total"]) ?></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
            <?php } ?>


            <br><br>

        </div>
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


</body>

</html>