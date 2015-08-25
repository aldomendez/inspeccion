<?php
require '../Slim/Slim.php';
include "../inc/database.php";

$app = new Slim();

$app->get('/', 'index' );
$app->get('/getCarrierSerials/:carrier', 'get_carrier_content' );
$app->get('/osfm/:carriers', 'get_osfm_job_data_for_carrier' );
// $app->post('/carrier/dispose', 'dispose' );
// $app->post('/saveFailData', 'saveFailData');
// $app->get('/insp/:carrier/:tech', 'get_4x25CarrierContents');




function get_carrier_content($carrier='')
{
    if ($carrier == '') {
        throw new Exception("No se paso un numero de carrier", 1);
    }
    // echo $carrier;
    $DB = new MxOptix();
    $query = file_get_contents("sql/select_serials_in_pack.sql");
    $DB->setQuery($query);
    $DB->bind_vars(':carrier', $carrier);
    $DB->exec();
    $json = $DB->json();
    
    // Regresa los datos al navegador
    echo "$json";
    
    $DB->close();
}

function get_osfm_job_data_for_carrier($carrier='')
{
    if ($carrier == '') {
        throw new Exception("No se paso un numero de carrier", 1);
    }
    // echo $carrier;
    $DB = new MxOptix();
    $query = file_get_contents("sql/select_serials_in_osfm.sql");
    $DB->setQuery($query);
    $DB->bind_vars(':serials', $carrier);
    $DB->exec();
    $json = $DB->json();
    
    // Regresa los datos al navegador
    echo "$json";
    
    $DB->close();
}

function index()
{
    include "start.php";
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