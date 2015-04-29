<?php
require '../Slim/Slim.php';
include "../inc/database.php";

$app = new Slim();

$app->get('/', 'index' );
$app->get('/carrier/:carrier', 'get_carrier_content' );
$app->post('/carrier/dispose', 'dispose' );
$app->post('/saveFailData', 'saveFailData');
$app->get('/insp/:carrier/:tech', 'get_4x25CarrierContents');


function get_carrier_content($carrier='')
{
    if ($carrier == '') {
        throw new Exception("No se paso un numero de carrier", 1);
    }
    // echo $carrier;
    $DB = new MxOptix();
    $DB->setQuery("SELECT carrier_serial_num, carrier_site, serial_num, status FROM phase2.carrier_site@mxoptix WHERE carrier_serial_num = ':carrier'");
    $DB->bind_vars(':carrier', $carrier);
    $DB->exec();
    $json = $DB->json();
    
    // Regresa los datos al navegador
    echo "$json";
    
    $DB->close();
}
function saveFailData()
{
    global $app;
    // Primero a declarar todas la variables que necesito
    $post = $app->request()->post();
    $components = $post['components'];
    $user = $post['user'];

    print_r($components);
    // Query que inserta en los componentes
$insert_fail_mode = <<<QUERY1
INSERT INTO fallas_lr4 (
  serial_num,status,carrier_serial_num,carrier_site,user_id,comments,component,failmode
) values
(':serial_num',':status',':carrier_serial_num',':carrier_site',':user_id',':comments',':component',':failmode')
QUERY1;


    $DB = new MxApps();
    foreach ($components as $key => $value) {
        $DB->setQuery($insert_fail_mode);
        $DB->bind_vars(':serial_num',$value['SERIAL_NUM']);
        $DB->bind_vars(':status',$value['STATUS']=='true'?'PASS':'FAIL');
        $DB->bind_vars(':carrier_serial_num',$value['CARRIER_SERIAL_NUM']);
        $DB->bind_vars(':carrier_site',$value['CARRIER_SITE']);
        $DB->bind_vars(':user_id',$user);
        $DB->bind_vars(':comments',$value['COMMENTS']);
        $DB->bind_vars(':component',$value['COMPONENT']);
        $DB->bind_vars(':failmode',$value['FAILMODE']);
        
        echo $DB->query . PHP_EOL;
        $DB->insert();
    }
    $DB->close();
}


function index()
{
    include "start.php";
}

function all_epoxys(){
    
    $query = "SELECT lot_number, comments, to_char(expire_date,'yyyymmddhh24mi') expire_date FROM epoxy WHERE expire_date > SYSDATE";
    $DB = new MxOptix();
    $DB->setQuery($query);
    $DB->exec();
    $json = $DB->json();
    
    echo "$json";
    
    $DB->close();
}

function dispose()
{
    global $app;
    $comment = $app->request()->post('comment');
    $lot = $app->request()->post('lot');

    if ($comment == '') {
        throw new Exception("'Comment' cant be empty", 1);
    }
    if ($lot == '') {
        throw new Exception("'Lot' cant be empty", 1);
    }

    $query = "UPDATE epoxy Set expire_date = SYSDATE, comments = '{$comment}' WHERE lot_number = '{$lot}'";
    $DB = new MxOptix();
    $DB->update($query);
    // $DB->exec();
    // $json = $DB->json();
    
    echo "success";
    
    $DB->close();
}

$app->run();



function get_4x25CarrierContents($carrier, $tech){
    
$query = <<<QUERY1
SELECT CARRIER_SERIAL_NUM, CARRIER_SITE, SERIAL_NUM, 'P' AS Fail_Pass
  , ':tech' Technician
  , To_Char(sysdate,'yyyy-mm-dd hh24:mi') upd_date
FROM carrier_site
WHERE carrier_serial_num = ':carrier' ORDER BY Carrier_site
QUERY1;

    $DB = new MxOptix();
    $DB->setQuery($query);
    $DB->bind_vars(':carrier', $carrier);
    $DB->bind_vars(':tech', $tech);
    oci_execute($DB->statement);
    $results = null;
    oci_fetch_all($DB->statement, $results,0,-1,OCI_FETCHSTATEMENT_BY_ROW);
    
    // print_r($results);

    $ans = array();

    foreach ($results as $key => $value) {
        array_push($ans, implode(',', $value));
    }
    // // Regresa los datos al navegador
    echo(implode('|', $ans));
    

    // $DB->setQuery($query);
    // $json = $DB->json();
    







    $DB->close();

}