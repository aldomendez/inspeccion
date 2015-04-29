<?php
require '../Slim/Slim.php';
include "../inc/database.php";

$app = new Slim();

$app->get('/', 'index' );
$app->get('/carrier/:carrier', 'get_carrier_content' );
$app->post('/carrier/dispose', 'dispose' );
$app->post('/carrier/register', 'register');
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
function register()
{
    global $app;
    // Primero a declarar todas la variables que necesito
    $epoxy = array();
    $epoxy['type'] = $app->request()->post('type');
    $epoxy['lot'] = $app->request()->post('lot');
    $epoxy['operator'] = $app->request()->post('operator');
    $epoxy['expiration'] = $app->request()->post('expiration');
  
    // Query que inserta en los componentes
$queryEpoxyInsert = <<<QUERY1
INSERT INTO fallas_lr4 (
  serial_num,carrier_serial_num,carrier_site,user_id,comments,componente,fail_mode
) values
(:serial_num,:carrier_serial_num,:carrier_site,:user_id,:comments,:componente,:fail_mode)
QUERY1;
// Query que inserta en apogee
$queryInsertEpoxyLog = <<<QUERY2
INSERT INTO lr4_epoxy_log
(NUM_SAP,TIPO_EPOXY,LOTE,FECHA_CAD_PROV,FECHA_REGISTRO,FECHA_CADUCIDAD,JERINGA,EMPLEADO,PROCESO,STATE,COMENTARIOS)
Values 
(':sap',':type',':lot',To_Date(':expiration','YYYY-MM-DD'),SYSDATE,SYSDATE + :timeAlive,':num',':operator',':proceso','C',':comentario')
QUERY2;
    

    $DB = new MxOptix();
    $DB->setQuery($queryCount);
    $DB->exec();

    // Obtenemos el numero de la geringa:
    $epoxy['num'] = substr(str_pad($DB->results[0]['QTY'] + 1,10,'0',STR_PAD_LEFT), -3);
    $DB->setQuery($queryEpoxyInsert);
    $DB->bind_vars(':type',$epoxy['type']);
    $DB->bind_vars(':lot',$epoxy['lot']);
    $DB->bind_vars(':num',$epoxy['num']);
    $DB->bind_vars(':pot_life',$epoxy['pot_life']);
    // echo $DB->query . PHP_EOL;
    $DB->insert();
    $DB->close();

    $MO = new MxApps();
    $MO->setQuery($queryInsertEpoxyLog);
    $MO->bind_vars(':sap',$epoxy['sap']);
    $MO->bind_vars(':type',$epoxy['type']);
    $MO->bind_vars(':lot',$epoxy['lot']);
    $MO->bind_vars(':expiration',$epoxy['expiration']);
    $MO->bind_vars(':timeAlive',$epoxy['timeAlive']);
    $MO->bind_vars(':num',$epoxy['num']);
    $MO->bind_vars(':operator',$epoxy['operator']);
    $MO->bind_vars(':proceso',$epoxy['proceso']);
    $MO->bind_vars(':comentario',$epoxy['comentario']);
    // echo $MO->query . PHP_EOL;
    $MO->insert();
    
    $MO->close();
    // print_r($epoxy);


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