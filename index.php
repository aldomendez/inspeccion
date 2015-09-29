<?php
require '../Slim/Slim.php';
include "../inc/database.php";

$app = new Slim();

$app->get('/', 'index' );
$app->get('/getCarrierSerials/:carrier', 'get_carrier_content' );
$app->get('/osfm/:serials', 'get_osfm_job_data_for_carrier' );
$app->get('/flag/:carrier', 'report_a_carrier' );
$app->get('/markUsed/:carrier', 'get_osfm_job_data_for_carrier' );
// $app->post('/carrier/dispose', 'dispose' );
// $app->post('/saveFailData', 'saveFailData');
// $app->get('/insp/:carrier/:tech', 'get_4x25CarrierContents');




function get_carrier_content($carrier='')
{
    if ($carrier == '') {
        throw new Exception("No se paso un numero de carrier", 1);
    }
    // Busca si tiene registros previos
    $DB = new MxApps();
    $query = file_get_contents("sql/select_inventario.sql");
    $DB->setQuery($query);
    $DB->bind_vars(':carrier_serial_num', $carrier);
    $DB->exec();
    $json = $DB->json();
    if (sizeof($DB->results) > 0) {
        // Regresa los datos al navegador
        echo "$json";
    } else {
    
        $query = file_get_contents("sql/select_serials_in_pack.sql");
        $DB->setQuery($query);
        $DB->bind_vars(':carrier', $carrier);
        $DB->exec();
        
        $query = file_get_contents("sql/insert_inventario.sql");
        foreach ($DB->results as $key => $value) {
            // print_r($value);
            $DB->setQuery($query);
            $DB->bind_vars(':carrier_site',$value['CARRIER_SITE']);
            $DB->bind_vars(':serial_num',$value['SERIAL_NUM']);
            $DB->bind_vars(':carrier_serial_num',$value['CARRIER_SERIAL_NUM']);
            $DB->bind_vars(':status','noOsfmData');
            $DB->bind_vars(':osfm_location','');
            $DB->bind_vars(':db_status',$value['STATUS']);
            // echo $DB->query;
            $DB->insert();
        }

        // Return results
        $json = $DB->json();
        echo "$json";
    }
    $DB->close();
}

function get_osfm_job_data_for_carrier($serials='')
{
    if ($serials == '') {
        throw new Exception("No se paso uno o varios NUMEROS DE SERIE entre comillas sencillas, separados por comas", 1);
    }
    // echo $serials;
    $DB = new MxApps();

    $query = file_get_contents("sql/select_serials_in_osfm.sql");
    $DB->setQuery($query);
    $DB->bind_vars(':serials', $serials);
    $DB->exec();
    $json = $DB->json();
    // echo "$json";

    if (sizeof($DB->results) > 0) {

        $query = file_get_contents("sql/update_inventario.sql");
        foreach ($DB->results as $key => $value) {
            // print_r($value);
            $DB->setQuery($query);
            $DB->bind_vars(':date_received',$value['DATE_RECEIVED']);
            $DB->bind_vars(':osfm_item',$value['ITEM']);
            $DB->bind_vars(':comments','');
            $DB->bind_vars(':status','inReview');
            $DB->bind_vars(':osfm_location',$value['SUBINVENTORY_CODE']);
            $DB->bind_vars(':serial_num',$value['JOB']);
            // echo $DB->query;
            $DB->insert();
        }
    }

    // Regresa los datos al navegador
    // $json = $DB->json();
    echo "$json";
    $DB->close();
}

function index()
{
    include "start.php";
}

function report_a_carrier($carrier)
{
    $DB = new MxApps();
    $query = file_get_contents("sql/update_inventario_flaged.sql");
    $DB->setQuery($query);
    $DB->bind_vars(':actual_status', 'REJECTED');
    $DB->bind_vars(':carrier', $carrier);
    echo $DB->query;
    $DB->exec();
    echo "[true]";
    $DB->close();
}
// function saveFailData()
// {
//     global $app;
//     // Primero a declarar todas la variables que necesito
//     $post = $app->request()->post();
//     $components = $post['components'];
//     $user = $post['user'];

//     print_r($components);
//     // Query que inserta en los componentes


// $insert_fail_mode = <<<QUERY1
// INSERT INTO fallas_lr4 (
//   serial_num,status,carrier_serial_num,carrier_site,user_id,comments,component,failmode
// ) select ':serial_num',':status',':carrier_serial_num',':carrier_site',':user_id',':comments',':component',':failmode' 
// FROM dual
// WHERE NOT EXISTS (SELECT 1 FROM fallas_lr4 WHERE serial_num = ':serial_num')
// QUERY1;

// $update_fail_mode = <<<QUERY1
// UPDATE fallas_lr4 
// SET
//     user_id = ':user_id',
//     status = ':status',
//     component = ':component',
//     failmode = ':failmode',
//     comments = ':comments'
// Where serial_num = ':serial_num'
// QUERY1;

// $update_part_info = <<<Query2
// UPDATE part_info
// SET part_status = ':status',
// pass_fail = ':pass_fail'
// WHERE serial_num = ':serial_num'
// Query2;

//     $DB = new MxApps();
//     $P2 = new MxOptix();

//     foreach ($components as $key => $value) {
//         if ($value['exists'] == 'false') {
//             $DB->setQuery($insert_fail_mode);
//             $DB->bind_vars(':serial_num',$value['SERIAL_NUM']);
//             $DB->bind_vars(':status',$value['STATUS']=='true'?'P':'F');
//             $DB->bind_vars(':carrier_serial_num',$value['CARRIER_SERIAL_NUM']);
//             $DB->bind_vars(':carrier_site',$value['CARRIER_SITE']);
//             $DB->bind_vars(':user_id',$user);
//             $DB->bind_vars(':comments',$value['COMMENTS']);
//             $DB->bind_vars(':component',$value['COMPONENT']);
//             $DB->bind_vars(':failmode',$value['FAILMODE']);
            
//             echo $DB->query . PHP_EOL;
//             $DB->insert();
//             oci_free_statement($DB->statement);

//             $P2->setQuery($update_part_info);
//             $P2->bind_vars(':serial_num',$value['SERIAL_NUM']);
//             $P2->bind_vars(':status',$value['STATUS']=='true'?'PASS/POST-PURGE':'FAIL/VISUAL_INSPECTION');
//             $P2->bind_vars(':pass_fail',$value['STATUS']=='true'?'P':'F');
            
//             echo $P2->query . PHP_EOL;
//             $P2->insert();
//             oci_free_statement($P2->statement);
//         } else {
//             $DB->setQuery($update_fail_mode);
//             $DB->bind_vars(':serial_num',$value['SERIAL_NUM']);
//             $DB->bind_vars(':status',$value['STATUS']=='true'?'P':'F');
//             $DB->bind_vars(':user_id',$user);
//             $DB->bind_vars(':comments',$value['COMMENTS']);
//             $DB->bind_vars(':component',$value['COMPONENT']);
//             $DB->bind_vars(':failmode',$value['FAILMODE']);
            
//             echo $DB->query . PHP_EOL;
//             $DB->insert();
//             oci_free_statement($DB->statement);

            
//             $P2->setQuery($update_part_info);
//             $P2->bind_vars(':serial_num',$value['SERIAL_NUM']);
//             $P2->bind_vars(':status',$value['STATUS']=='true'?'PASS/POST-PURGE':'FAIL/VISUAL_INSPECTION');
//             $P2->bind_vars(':pass_fail',$value['STATUS']=='true'?'P':'F');
            
//             echo $P2->query . PHP_EOL;
//             $P2->insert();
//             oci_free_statement($P2->statement);
//         }
//     }
//     oci_close($DB->conn);
//     oci_close($P2->conn);
// }


// function all_epoxys(){
    
//     $query = "SELECT lot_number, comments, to_char(expire_date,'yyyymmddhh24mi') expire_date FROM epoxy WHERE expire_date > SYSDATE";
//     $DB = new MxOptix();
//     $DB->setQuery($query);
//     $DB->exec();
//     $json = $DB->json();
    
//     echo "$json";
    
//     $DB->close();
// }


$app->run();