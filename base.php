<?php
require '../Slim/Slim.php';
require_once 'Twig-1.18.0/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

if ( $_SERVER['REMOTE_ADDR'] != '192.168.1.147'){
    $local = false;
    include "../inc/database.php";
} else {
    $local = true;
}

$app = new Slim();

$app->get('/', 'all' );
$app->get('/:bu', 'byBU' );

function all(){
    $query = file_get_contents('sql/machines.semaforo.sql');
    semaforo($query);
}

function byBU($bu='*')
{
    $query = file_get_contents('sql/machines.semaforo.sql');
    $query.= " and bu = '" . $bu . "'";
    semaforo($query);
}

function semaforo($query){
    global $local;
    if($local){
        $json = file_get_contents('filecache.txt');
    } else {
        $DB = new MxApps();
        $DB->setQuery($query);
        $DB->exec();
        $json = $DB->json();
    }
    if ($json == "[]") {
        throw new Exception("No arrojo datos la base de datos", 1);
    } else {
        
        $objects = json_decode($json, true);
        $objects = setStatus($objects);
        $objects = grouper($objects);
        generatePage($objects);
    }
    if (!$local) {
        oci_free_statement($DB->statement);
        oci_close($DB->conn);
    }
}

function generatePage($objects){

    $content = '<div class="ui grid" id="container">:menu :body</div>';
    $_page = file_get_contents("simple.templates/page.php");

    $size = array( 1 => 'one', 2 => 'two',  3 => 'three',  4 => 'four',  5 => 'five',  6 =>'six' );

    $workingData = array('bu'=>$objects,'size'=>$size);

    $loader = new Twig_Loader_Array(array('index' => $_page));
    $twig = new Twig_Environment($loader);

    echo $twig->render('index', $workingData );
}

function setStatus($objects)
{
    $actual = strtotime('now');
    foreach ($objects as $key => $value) {
        $tick = strtotime($value['LASTTICK']);
        // Esta es solo pa prueba de que funciona
        // $objects[$key]['STATUS'] = ((($actual - $tick)/60)/60)/24;
        $secondsSinceLastDevice = ($actual - $tick);
        if (  $secondsSinceLastDevice < $value['CICLETIME']  ) {
            $status = 'green';
            $diff = 0;
        } elseif ( $secondsSinceLastDevice < 2*$value['CICLETIME']) {
            $status = 'yellow';
            $diff = 1;
        } else {
            $status = 'red';
            $diff = round($secondsSinceLastDevice / $value['CICLETIME'],0);
        }
        
        $objects[$key]['STATUS'] = $status;
        $objects[$key]['DIFF'] = $diff;
        $objects[$key]['CICLETIME_min'] = round($value['CICLETIME']/60,1);
    }
    return $objects;
}


function grouper($objects)
{
    // La intension de esta funcion es generar todos los agrupamientos necesarios
    // de una sola vez BU->AREA->PROCESS
    
    $group = array();
    foreach ($objects as $key => $value) {
        // Crea un arreglo tan profundo como se tenga que hacer
        if (!array_key_exists( $value['BU'] , $group)) {
            $group[$value['BU']]=array();
        } 
        if(!array_key_exists($value['AREA'] , $group[$value['BU']])){
            $group[$value['BU']][$value['AREA']]=array();
        }
        if(!array_key_exists($value['PROCESS'], $group[$value['BU']][$value['AREA']])){
            $group[$value['BU']][$value['AREA']][$value['PROCESS']]=array();
        }
        // Se le inserta el valor necesario hasta el final (con todas las llaves creadas)
        // muy eficiente!
        array_push($group[$value['BU']][$value['AREA']][$value['PROCESS']], $value);
    }

    return $group;
}

$app->run();
