<?php
defined('_VALID_MOS') or die('Restricted access');

if(!isset($_SESSION["uActive"])){
	// check if exist cookie
	if (isset($_COOKIE['uActive'])) {
		echo "<h1>reasignado session</h1>";
		$_SESSION["uId"]              = $_COOKIE['uId'] ?? null;
		$_SESSION["uName"]            = $_COOKIE['uName'] ?? null;
		$_SESSION["uLocation"]        = $_COOKIE['uLocation'] ?? null;
		$_SESSION["uLocationDefault"] = $_COOKIE['uLocationDefault'] ?? null;
		$_SESSION["uActive"]          = $_COOKIE['uActive'] ?? null;
		$_SESSION["uMarker"]          = $_COOKIE['uMarker'] ?? null;
		$_SESSION["uIdCatParcel"]     = $_COOKIE['uIdCatParcel'] ?? null;
		$_SESSION["session_token"]    = $_COOKIE['session_token'] ?? null;
		echo "de la cookie a la session";
	} else {
		echo "cerrar session1";
		header('Location: '.BASE_URL.'/login.php');
		die();
	}
}
$sql     = "SELECT session_token FROM users WHERE id = ".$_SESSION["uId"];
$dtstkn  = $db->select($sql);
$tokenDB = $dtstkn[0]['session_token'];

if (empty($_SESSION['session_token']) || $_SESSION['session_token'] !== $tokenDB) {
	/*session_unset();
	session_destroy();
	//destro cookies
	setcookie('uId', '', time() - 3600, '/');
	setcookie('uName', '', time() - 3600, '/');
	setcookie('uLocationDefault', '', time() - 3600, '/');
	setcookie('uLocation', '', time() - 3600, '/');
	setcookie('uActive', '', time() - 3600, '/');
	setcookie('uMarker', '', time() - 3600, '/');
	setcookie('session_token', '', time() - 3600, '/');
	echo "cerrar session2";

	header('Location: '.BASE_URL.'/login.php');
	die();
	*/
}

if(isset($_SESSION['uLocation'])){
	$_SESSION['uLocation'] = $_SESSION['uLocation'];
}else{
	$_SESSION['uLocation'] = $_SESSION['uLocationDefault'];
}
$desc_loc = ($_SESSION['uLocation']==1)? 'Tlaquiltenango':' Zacatepec';

//var_dump($_SESSION);

#contador de mensajes
$sqlm="SELECT 
    COUNT(DISTINCT sender_phone) AS total_chats 
FROM 
    waba_callbacks 
WHERE 
    is_read = 0
	AND id_location IN (".$_SESSION["uId"].")" ;
$rsttmsl  = $db->select($sqlm);
$totalMensajeSinLeer = $rsttmsl[0]['total_chats'];
