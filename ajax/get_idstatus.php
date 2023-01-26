<?php
include "../session.php";


$json = $_POST['json'];

//var_dump($_POST);

if(isset($_POST['json'])){

    $idlist = "";
    $error_json = 0;
    
    foreach($json as $value){
        $idlist .= $value["value"] . ",";
        if ( is_numeric($value["value"]) != true){
            $error_json = 1;
        }
    }
    
    
    if($error_json == 0){
    
        $query = "SELECT id, produto, valor, tipo, reserva FROM doecompre WHERE id in (".substr($idlist,0,strlen($idlist)-1).");";
        
        $result = mysqli_query($db,$query);
        
        $return_arr = array();
        
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
            $id = $row['id'];
            $reserva = $row['reserva'];
        
            $return_arr[] = array("id" => $id,
                "reserva" => $reserva
            );
        }
        
        //Encoding array in JSON
        echo json_encode($return_arr);
    
    } else {
        header('HTTP/1.1 403 FORBIDDEN');
        header('Status: 403 You Do Not Have Access To This Page');
    }

} else {
    //echo "ERROR";
    header('HTTP/1.1 403 FORBIDDEN');
    header('Status: 403 You Do Not Have Access To This Page');
}

/*
$json = filter_input(INPUT_POST, 'json');
$decoded_json = json_decode($json);
$keyword = $decoded_json->keyword;

#$keyword = preg_replace('/[^[:alpha:]_]/', '',strtoupper($keyword));
#$keyword = strtoupper($keyword);

$query = "SELECT id, produto, valor, tipo, reserva FROM doecompre WHERE id = ".$keyword.";";

$result = mysqli_query($db,$query);

$count = mysqli_num_rows($result);

$row = mysqli_fetch_array($result,MYSQLI_ASSOC);

if($count > 0) {
    echo $row['reserva'];
} else {
    echo 0;
}*/

?>