<?php
defined('_VALID_MOS') or die('Restricted access');
date_default_timezone_set('America/Mexico_City');

$userRoot = '';
$docRoot  = $_SERVER['SERVER_NAME'];
#define('BASE_URL','https://'.$docRoot."/".$userRoot);
define('BASE_URL','https://'.$docRoot);
#var_dump(BASE_URL);

define('PAGE_TITLE','PLP - Local');

//---------------------------------------------------

//PROD-HOSTINGER
/*
define('HOST','127.0.0.1');
define('USERNAME','u369447447_sysadminbd');
define('PASSWD','b9@RlsPuso8ofA');
define('DBNAME','u369447447_plp');
define('PORT','3306');
define('SOCKET','null');
//define('NODE_PATH_FILE','C:/laragon/www/jt/nodejs/'); //Local
define('NODE_PATH_FILE','D:/Programs/laragon/www/jt/nodejs/'); //ENZ
define('NAME_HOST_REMOTE','paqueterialospinos.com');
define('NAME_HOST_LOCAL','plp.test');
define('LARGO',1024); //1024
define('ALTO',768); //768
*/

/*
640x480 (VGA)
800x600 (SVGA)
1024x768 (XGA)
1280x720 (HD)
1280x800 (WXGA)
1280x1024 (SXGA)
1366x768 (HD+)
1440x900 (WXGA+)
1600x900 (HD+)
1680x1050 (WSXGA+)
*/
//---------------------------------------------------
//LOCALHOST
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
//---------------------------------------------------
#REMOTE
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
//---------------------------------------------------