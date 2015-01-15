<?
ob_start();
session_start();
//start indeed tracking codes
if(isset($_GET['idtrack']) && $_GET['idtrack'] != ''){
        $_SESSION['indeed'] = stripslashes(trim($_GET['idtrack']));
}
/* 
	Site wide variabel & global connectivity for database
	$connection = mysql_connect("localhost", "whtnght_rob", "3n1r0py")
*/

$db_host 		= 'localhost';
$db_username 	= 'whtnght_rob';
$db_password 	= '3n1r0py';
$db_name 		= 'whtnght_search';

define("DB_SERVER", $db_host);
define("DB_USER", $db_username);
define("DB_PASS", $db_password);
define("DB_NAME", $db_name);

//define("BASEPATH", '/www/htdocs/cesfl.com/');
define("BASEPATH", '/www/htdocs/robp/');
define('LIB','include/');
define('SITEURL','http://cesfl.com/');
define('SITE', 'Criterion Executive Search');

$titleMessage = "Tampa, Florida";
$pageTitle = SITE;

include_once(LIB.'class.database.php');
include_once(LIB.'inc.utilities.php');
//geta handle
$db = new db($db_host, $db_username, $db_password, $db_name);

$pageTitle = 'Criterion Executive Search in Tampa, FL';
?>
