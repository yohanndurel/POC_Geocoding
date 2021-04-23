<?php
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Here\Here;
use Geocoder\Provider\OpenCage\OpenCage;
use Geocoder\Query\GeocodeQuery;
use parallel\Runtime;

require 'vendor/autoload.php';
include 'YoApi.php';



//$runtime1 = new Runtime();
//$runtime2 = new Runtime();
//$runtime3 = new Runtime();
//$runtime4 = new Runtime();
//echo '1.1';
//$adapter = new \Http\Adapter\Guzzle6\Client();
//$geocoder = new \Geocoder\ProviderAggregator();
//echo '1.2';
//$geocoder->registerProviders([
//    new GoogleMaps($adapter, 'France', 'AIzaSyAsTksU6sLAzTrR9mL4mXHigdSQ9qOYHok'),
//    new OpenCage($adapter,'bcb61a3d5eaf4c2885bbfb34eb2d39a4'),
//    Here::createUsingApiKey($adapter, 'DXtM2NwEly1PeRnUE2pRkkckkwhrK216KxOTAKjruBU'),
//    new Geocoder\Provider\Ipstack\Ipstack($adapter, '063cc25000acfc15262924377c8ababf'),
//    //new YoApi($adapter, 'France', 'AIzaSyAsTksU6sLAzTrR9mL4mXHigdSQ9qOYHok')
//]);
//$db = pg_connect("host=localhost port=5432 dbname=db-candidat user=db-candidat password=db-candidat");
//$requete = pg_query($db, "select id, adresse, code_postal, ville, pays_de_domicile_id, km_trajet_maximum, minutes_trajet_maximum from candidat_address where (((adresse IS NOT NULL AND trim (adresse) != '') OR (ville IS NOT NULL AND trim (ville) != '')) AND (latitude IS NULL AND longitude IS NULL)) limit 10");
//
//echo '1.4';
//$rows = pg_fetch_all($requete);
//echo $rows;
//
//
//$args = array();
//$args[0] = $rows;
//$args[1] = function ($adresse){
//    global $geocoder;
//    return $geocoder
//        ->using('google_maps') // google_maps ; opencage ; Here ; ipstack
//        ->geocode($adresse);
//};



//$future = $runtime1->run(function($rows, $test){
//    global $db;
//
//    echo $rows;
//
//
//
//    foreach ($rows as $row){
//        var_export($row);
//        $adresse = $row['adresse']." ".$row['code_postal']." ".$row['ville'];
//        $id = $row['id'];
//        $result = $test($adresse);
//
//
//        if($result->count() > 0) {
//            $latitude = $result->first()->getCoordinates()->getLatitude();
//            $longitude = $result->first()->getCoordinates()->getLongitude();
//            echo $adresse, "<br>", $latitude, " ", $longitude, "<br><br>", "      AAAAAAAAA     ";
//            //$update = pg_query($db, "UPDATE candidat_address SET latitude = $latitude, longitude = $longitude WHERE id = $id");
//        }
//        else {
//            echo 'Aucun resultat ';
//            echo '1.5';
//        }
//
//    }
//
//    return 'coucou';
//},
//$args
//);

//echo $future->value();

$adapter = new \Http\Adapter\Guzzle6\Client();
$geocoder = new \Geocoder\ProviderAggregator();

$erreurs = 0;
$geocoder->registerProviders([
    new GoogleMaps($adapter, 'France', 'AIzaSyAsTksU6sLAzTrR9mL4mXHigdSQ9qOYHok'),
    new OpenCage($adapter,'bcb61a3d5eaf4c2885bbfb34eb2d39a4'),
    Here::createUsingApiKey($adapter, 'DXtM2NwEly1PeRnUE2pRkkckkwhrK216KxOTAKjruBU'),
    new Geocoder\Provider\Ipstack\Ipstack($adapter, '063cc25000acfc15262924377c8ababf'),
    new Geocoder\Provider\ArcGISOnline\ArcGISOnline($adapter, 'France', 'AAPK4598e5678b1d4c56904f9f8fb47fb542tRbVZey2miNhS3M_hHuEwJBl2TEKkDC850pWPIa9cEZOmyZqdO8rzFqiQBUTGWKy'),
    new \Geocoder\Provider\BingMaps\BingMaps($adapter, 'AnKuQYA60681D63Pv8frFygCpVZRIBCtyIGHXYlMgEGXRtoK0qt817L0EbYLAQ9m'),
    new Geocoder\Provider\Mapbox\Mapbox($adapter, 'pk.eyJ1IjoieW9oYW5uZHVyZWwiLCJhIjoiY2trcDk2aWo4MDc4OTJvcWJ6OGZsbTV1NSJ9.fmXVR48GnQBlQknOZr5Zdg'),
    new Geocoder\Provider\GeocodeEarth\GeocodeEarth($adapter, 'ge-69e2d20b88124e10'),
    new Geocoder\Provider\Geonames\Geonames($adapter, 'TwixiZ'),
    new Geocoder\Provider\MapQuest\MapQuest($adapter, 'twL3Du3ZW3LATlljjGpkPk8F3iB2ci1E'),
    //new YoApi($adapter, 'France', 'AIzaSyAsTksU6sLAzTrR9mL4mXHigdSQ9qOYHok')
]);

$db = pg_connect("host=localhost port=5432 dbname=db-candidat user=db-candidat password=db-candidat");

$requete = pg_query($db, "select id, adresse, code_postal, ville, pays_de_domicile_id, km_trajet_maximum, minutes_trajet_maximum from candidat_address where (((adresse IS NOT NULL AND trim (adresse) != '') OR (ville IS NOT NULL AND trim (ville) != '')) AND (latitude IS NULL AND longitude IS NULL)) limit 1000");

$rows = pg_fetch_all($requete);
foreach ($rows as $row){
    $adresse = $row['adresse']." ".$row['code_postal']." ".$row['ville'];
    $id = $row['id'];
    $result = $geocoder
        ->using('map_quest') // google_maps ; opencage ; Here ; ipstack ; arcgis_online ; bing_maps ; mapbox ; geocode_earth ; geonames ; map_quest
        ->geocode($adresse);

    if($result->count() > 0) {
        $latitude = $result->first()->getCoordinates()->getLatitude();
        $longitude = $result->first()->getCoordinates()->getLongitude();
        echo $adresse, " ", $latitude, " ", $longitude, " ; ";
        $update = pg_query($db, "UPDATE candidat_address SET latitude = $latitude, longitude = $longitude WHERE id = $id");
    }
    else {
        echo $adresse, 'Aucun resultat ';
        $erreurs++;
    }

}
pg_close();
echo "Erreurs : ", $erreurs;



//$future = $runtime1->run(function(){
//    for ($i = 0; $i < 500; $i++)
//        echo "*";
//
//});
//$future2 = $runtime2->run(function(){
//    for ($i = 0; $i < 500; $i++)
//        echo "°";
//
//});
//$future3 = $runtime3->run(function(){
//    for ($i = 0; $i < 500; $i++)
//        echo "-";
//
//});
//$future4 = $runtime4->run(function (){
//    for ($i = 0; $i < 500; $i++) {
//        echo ".";
//    }
//});
//
//for ($i = 0; $i < 500; $i++) {
//    echo "/\\";
//}

