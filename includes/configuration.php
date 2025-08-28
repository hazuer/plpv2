<?php
defined('_VALID_MOS') or die('Restricted access');
date_default_timezone_set('America/Mexico_City');

$userRoot = '';
$docRoot  = $_SERVER['SERVER_NAME'];
#define('BASE_URL','https://'.$docRoot."/".$userRoot);
define('BASE_URL','https://'.$docRoot);
#var_dump(BASE_URL);

define('PAGE_TITLE','PLP - Local');

//RESP
/*
define('HOST','localhost');
define('USERNAME','root');
define('PASSWD','');
define('DBNAME','u369447447_plp');
define('PORT','3306');
define('SOCKET','null');
define('NODE_PATH_FILE','C:/laragon/www/plp/nodejs/'); //ENZ
define('NAME_HOST_REMOTE','paqueterialospinos.com');
define('NAME_HOST_LOCAL','plp.test');
define('LARGO',1024); //1024
define('ALTO',768); //768
*/


define('HOST','srv1441.hstgr.io');
define('USERNAME','u369447447_sysadminbd');
define('PASSWD','b9@RlsPuso8ofA');
define('DBNAME','u369447447_plp');
define('PORT','3306');
define('SOCKET','null');
//define('NODE_PATH_FILE','C:/laragon/www/plp/nodejs/'); //Local
define('NODE_PATH_FILE','D:/Programs/laragon/www/plp/nodejs/'); //ENZ
define('NAME_HOST_REMOTE','paqueterialospinos.com');
define('NAME_HOST_LOCAL','plp.test');
define('LARGO',1024); //with
define('ALTO',768);



//PROD-HOSTINGER
/*
define('HOST','127.0.0.1');
define('USERNAME','u611824705_admin');
define('PASSWD','FJ4t82*i');
define('DBNAME','u611824705_jt');
define('PORT','3306');
define('SOCKET','null');
define('NODE_PATH_FILE','files/public_html/jt/nodejs/'); // PROD

*/