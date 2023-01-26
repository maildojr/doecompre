<?php
include('session.php');

$ses_sql = mysqli_query($db, "SELECT doador, produto, valor, reserva FROM `doecompre` WHERE reserva != '-' and status = 1 order by 4, 2;");

$msg = "*LISTA DE COMPRADORES - VALORES*<br>";
$comprador = "";
$valor_total = 0;
while ($row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC)) {
    $produto = $row['produto'];
    $doador = $row['doador'];
    $valor = $row['valor'];
    if ($comprador != $row['reserva']){
        $msg = str_replace("[valor_total]",$valor_total,$msg);
        $comprador = $row['reserva'];
        $valor_total = $valor;
        $msg .= "<br>*".mb_strtoupper($comprador, 'UTF-8')."* - Valor Total R$ [valor_total]<br>".$produto." - ".$doador." - R$ ".$valor."<br>";
    } else {
        $msg .= $produto." - ".$doador." - R$ ".$valor."<br>";
        $valor_total += $valor;
    }
}
$msg = str_replace("[valor_total]",$valor_total,$msg);
$urlmsg = str_replace("%3Cbr%3E","%0A",urlencode($msg));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Compradores - Valores</title>
</head>
<body>
    <a href='https://api.whatsapp.com/send?phone=5521987712125&text=<?php echo $urlmsg ?>'>Enviar Whatsapp</a>
    <p><?php echo $msg ?></p>
</body>
</html>