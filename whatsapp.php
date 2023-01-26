<?php
include('session.php');

$ses_sql = mysqli_query($db, "SELECT doador, produto, valor, reserva FROM `doecompre` WHERE reserva != '-' and status = 1 order by 1, 2, 4;");

$msg = "*LISTA DE COMPRADORES*<br>";
$doador = "";
while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) {
    $produto = $row['produto'];
    $reserva = $row['reserva'];
    if ($doador != $row['doador']){
        $doador = $row['doador'];    
        $msg .= "<br>*".mb_strtoupper($doador, 'UTF-8')."*<br>".$produto." - ".$reserva."<br>";
    } else {
        $produto = $row['produto'];
        $msg .= $produto." - ".$reserva."<br>";
    }
    $urlmsg = str_replace("%3Cbr%3E","%0A",urlencode($msg));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Compradores</title>
</head>
<body>
    <a href='https://api.whatsapp.com/send?phone=5521987712125&text=<?php echo $urlmsg ?>'>Enviar Whatsapp</a>
    <!--<p><?php echo $urlmsg ?></p>-->
    <p><?php echo $msg ?></p>
</body>
</html>