<?php
include('session.php');
header("Content-type: text/html; charset=utf-8");


$sql_ciclo = mysqli_query($db, "SELECT id, DATE_FORMAT(ciclo,'%d/%m/%Y %H:%i') ciclo, status FROM doecompre_ciclo WHERE status = 1;");
            
$row = mysqli_fetch_array($sql_ciclo, MYSQLI_ASSOC);
$ciclo = $row['ciclo'];
$ciclo_id = $row['id'];

if(!isset($_SESSION['login']) or $_SESSION['login'] == 0){
    if(!empty($_POST["password"])){
        if($_POST["password"]== $senha){
            $login_ok = 1;
            $_SESSION['login'] = 1;
        } else {
            $_SESSION['login'] = 0;
            $_SESSION['tentativa'] += 1;
        }
    } else {
        $_SESSION['login'] = 0;
    }
}

if(!empty($_POST["ciclo_data"])){
    $ciclo_data = $_POST["ciclo_data"];
    $ciclo_hora = $_POST["ciclo_hora"] . ":00";
    $vdate = str_replace('/', '-', $ciclo_data) . " " . $ciclo_hora;
    $newCiclo = date("Y-m-d H:i:s", strtotime($vdate));

    //Desativa Produtos
    $sql_desactive = "UPDATE doecompre SET status = 0 WHERE status = 1;";
    mysqli_query($db, $sql_desactive);

    //Desativa Ciclos Ativos
    $sql_desactive2 = "UPDATE doecompre_ciclo SET status = 0 WHERE status = 1;";
    mysqli_query($db, $sql_desactive2);

    $sql_create = "INSERT INTO doecompre_ciclo (ciclo, status) VALUES ('".$newCiclo."',1)";
    mysqli_query($db, $sql_create);

    header("location: ./configurar.php");

}


if(!empty($_POST["produto_name"])){
    $produto_nome = $_POST["produto_name"];
    $produto_quantidade = intval($_POST["produto_quantidade"]);
    $produto_valor = str_replace(",",".",$_POST["produto_valor"]);
    $produto_tipo = $_POST["produto_tipo"];
    $produto_doador = $_POST["produto_doador"];

    for ($x = 1; $x <= $produto_quantidade; $x++){
        $sql_insert = "INSERT INTO doecompre (produto, doador, valor, tipo, reserva, status, ciclo) VALUES ('".$produto_nome."','".$produto_doador."',".$produto_valor.",'".$produto_tipo."','-',1,".$ciclo_id.");";
        mysqli_query($db, $sql_insert);
    }
}



if (!empty($_GET["id"])) {
    $id_produto = $_GET["id"];

    if (is_numeric($id_produto) == true) {
        $sql_update = "DELETE FROM doecompre WHERE id = " . $id_produto . ";";
        mysqli_query($db, $sql_update);
        header("location: ./configurar.php");
    } else {
        header("location: ./configurar.php");
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
                    <li><a href="./"><i class="material-icons">local_grocery_store</i></a></li>
                </ul>

                <ul id="nav-mobile" class="sidenav">
                    <li><a href="./"><i class="material-icons">local_grocery_store</i></a></li>
                </ul>
                <a href="./" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">local_grocery_store</i></a>
            </div>
        </nav>
    </div>

    <div class="section no-pad-bot" id="index-banner">
        <div class="container">
            <?php //echo $_SESSION['reserva_name']; 
            ?>
            <p id="sql"></p>

            <?php
            
            if($_SESSION['login'] == 0){
            
            ?>

            <center>
            <div class="section"></div>

            <h5 class="green-text">Insira a Senha de Acesso</h5>
            <div class="section"></div>

            <div class="container">
            <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

                <form class="col s12" method="post" action="./configurar">
                <div class='row'>
                    <div class='col s12'>
                    </div>
                </div>

                <div class='row'>
                    <div class='input-field col s12'>
                    <input class='validate' type='password' name='password' id='password' />
                    <label for='password'>Senha</label>
                    </div>
                </div>

                <br />
                <center>
                    <div class='row'>
                    <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect green'>Acessar</button>
                    </div>
                </center>
                </form>
            </div>
            </div>
            </center>

            <?php } else { ?>

            <h2 class="header center green-text">Configurações</h2>

            <div class="row">
                <div class="col s12">
                    <div class="card section teal lighten-3 center white-text"><h5>Rodada Atual - <?php echo $ciclo?></h5></div>
                </div>
                <button id="btn-ciclonew" class="waves-effect waves-light btn modal-trigger btn-reservar">Nova Rodada</button>
            </div>

            <div class="row">
                <div class="card hide" id="card-ciclonew">
                    <div class="card-content">
                        <h5>Data Liberação Compras</h5>
                        <form action="./configurar.php" method="POST" id="create_ciclo">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="ciclo_data" name="ciclo_data" type="text" class="datepicker">
                                    <label for="produto_name">Data</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="ciclo_hora" name="ciclo_hora" type="text" class="timepicker" value="18:30">
                                    <label for="produto_doador">Horário</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-action right-align">
                        <a id="btn-cancelnewciclo" class="modal-close waves-effect waves-red btn-flat">Cancelar</a>
                        <button class="btn" onclick="document.getElementById('create_ciclo').submit();">Salvar</button>
                    </div>
                </div>
            </div>
            
            

            <hr>
            <h3>Produtos Cadastrados</h3>
            <?php
            if ($valor_total > 0) { ?>
                <div class="row">
                    <h5 class="header col s12 light"><b><?php echo $qtd_total; ?> Produtos Cadastrados - Total R$ <?php echo $valor_total; ?></b></h5>
                </div>
            <?php } ?>
            <br><button id="btn-produtonew" class="waves-effect waves-light btn modal-trigger btn-reservar" onclick="create_produto(this)">Cadastrar Produto</button>
            <div class="table row">
                <h4>Doces</h4>
                <table class="striped">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>VALOR</th>
                            <th>AÇÃO</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Doces' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center">
                                    <a id="btn-delete" href="./configurar.php?id=<?php echo ($row["id"]) ?>" class="red btn modal-trigger"><i class="material-icons">delete</i></a>
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
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Salgados' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center">
                                    <a id="btn-delete" href="./configurar.php?id=<?php echo ($row["id"]) ?>" class="red btn modal-trigger"><i class="material-icons">delete</i></a>
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
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $ses_sql = mysqli_query($db, "SELECT id, produto, doador, REPLACE(valor,'.',',') valor, tipo, reserva FROM doecompre WHERE tipo = 'Diversos' and status = 1 ORDER BY produto");

                        while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) { ?>
                            <tr>
                                <td><b><?php echo ($row["produto"]) ?></b><br><?php echo ($row["doador"]) ?></td>
                                <td class="right"><?php echo ($row["valor"]) ?></td>
                                <td class="center">
                                    <a id="btn-delete" href="./configurar.php?id=<?php echo ($row["id"]) ?>" class="red btn modal-trigger"><i class="material-icons">delete</i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            
            <br><br>

            
            

            <div id="modalproduto" class="modal">
                <div class="modal-content">
                    <h5>Cadastro Produto</h5>
                    <form action="./configurar" method="POST" id="create_produto">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="produto_name" name="produto_name" type="text" class="validate">
                                <label for="produto_name">Nome do Produto</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input id="produto_doador" name="produto_doador" type="text" class="validate">
                                <label for="produto_doador">Doador</label>
                            </div>
                            <div class="input-field col s6">
                                <input id="produto_valor" name="produto_valor" type="text" class="validate">
                                <label for="produto_valor">Valor Unitário (R$)</label>
                            </div>
                        </div>
                        <div class="row">
                        <div class="input-field col s6">
                            <select id="produto_tipo" name="produto_tipo">
                                <option value="" selected disabled>Selecione..</option>
                                <option value="Doces">Doces</option>
                                <option value="Salgados">Salgados</option>
                                <option value="Diversos">Diversos</option>
                            </select>
                            <label>Tipo</label>
                        </div>
                        <div class="input-field col s6">
                            <select id="produto_quantidade" name="produto_quantidade">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="20">21</option>
                                <option value="20">22</option>
                                <option value="20">23</option>
                                <option value="20">24</option>
                                <option value="20">25</option>
                                <option value="20">26</option>
                                <option value="20">27</option>
                                <option value="20">28</option>
                                <option value="20">29</option>
                                <option value="20">30</option>

                            </select>
                            <label>Quantidade</label>
                        </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="modal-close waves-effect waves-red btn-flat">Cancelar</a>
                    <button class="modal-close waves-effect waves-green btn-flat" onclick="document.getElementById('create_produto').submit();"><b>Salvar</b></button>
                </div>
            </div>


        </div>
    </div>


    <?php } ?>


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
    <script src="https://code.jquery.com/jquery-2.1.1.js"></script>
    <script src="../js/materialize.js"></script>
    <script src="../js/init.js"></script>
    
    <!-- Initialization Components -->
    <script>
        $(document).ready(function() {
            $('.modal').modal();
            $('.datepicker').datepicker({
                format:'dd/mm/yyyy',
                i18n:{
                months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabádo'],
                weekdaysAbbrev: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                today: 'Hoje',
                clear: 'Limpar',
                close: 'Pronto',
                labelMonthNext: 'Próximo mês',
                labelMonthPrev: 'Mês anterior',
                labelMonthSelect: 'Selecione um mês',
                labelYearSelect: 'Selecione um ano',
                selectMonths: true,
                selectYears: 15,
                cancel: 'Cancelar',
                clear: 'Limpar'
                }
                });

            $('.timepicker').timepicker({
                twelveHour: false
            });
            $('select').formSelect();
        });

        $("#btn-ciclonew").click(function(){
            $("#card-ciclonew").removeClass("hide");
        });
        $("#btn-cancelnewciclo").click(function(){
            $("#card-ciclonew").addClass("hide");
        });
        
    </script>

    <script>
        // SCRIPT DE COPY TO CLIPBOARD

        function copypix() {
            /* Get the text field */
            var PIXChave = "00020126580014BR.GOV.BCB.PIX013636ae18c3-d872-4b7b-86aa-0be9f688fade5204000053039865802BR5925CENTRO ESPIRITA BENEFICEN6009SAO PAULO622605224MRwmcu9uH8NPNesWr5OaX6304490B";

            /* Copy the text inside the text field */
            //await navigator.clipboard.writeText(PIXChave);
            navigator.clipboard.writeText(PIXChave);

        }

        function create_produto(item){
                $('#modalproduto').modal('open');
        }
    </script>


</body>

</html>