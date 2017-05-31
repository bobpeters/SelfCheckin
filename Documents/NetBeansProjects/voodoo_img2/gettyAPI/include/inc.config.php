<?php
/**
 * Getty API integartion for purchasing template images
 * RPeters <rob@voodoo.com>
 * Feb. 2017
 */
ini_set("session.gc_maxlifetime", "28400");
ob_start();
session_start();
include('class.database.php');
//autoload namespaces
define('ROOT_URI','/www/gettyAPI/include/');
spl_autoload_register(function ($class) {
    $file = ROOT_URI. str_replace('\\', '/', $class) .'.php';
    if (file_exists($file)) {
        require $file;
    }
});

$bypass = (!isset($bypass)) ? '':$_REQUEST['bypass'];
/* grab a db handle */
$dbname = 'ParkedImages';
$dbuser = 'root';
$dbpassword = '';
$dbhost = '127.0.0.1';
$db = new db($dbhost,$dbuser,$dbpassword,$dbname);

if(isset($_GET['action']) && $_GET['action'] == 'request_image'){
	include_once('ajax_handler.php');
	die;
}elseif(isset($_GET['action']) && $_GET['action'] == 'del_req'){
	@$db->query("DELETE FROM `ParkedImages`.`VoodooImageRequest` WHERE `VoodooImageRequest`.`request_id` =".$_REQUEST['id']);
	
}


/*
* API Specific
*/
define(API_VERSION, 'v3/');
define(API_URL, 'https://api.gettyimages.com/');
define(API_SECURE_URL, 'https://api.gettyimages.com/'.$apiVersion);
define(API_KEY, "w4cjb44up7qjwr7hyxg9kree");
define(API_SECRET, "A9WTANBw3Q5bZ8YXSgWHp7Rmq72NTUB2yxNuaTadT4z9b");


/* istock/getty username and password for authentification */
define(ISTOCK_USER, "donnysimonton");
define(ISTOCK_PASS, "Keypath01");

define(WEB_PATH, "/gettyAPI/");
/* these point to the original fotolia project location to keep it simple */
define(DOWNLOAD_PATH, '/www/fotolia/downloads/');
define(IMAGE_BACKUP, '/www/fotolia/imageBackup/');

$language_id = 2; // US English
$minPerPage = 50;

define(DB_NAME, 'ParkedImages');
define(DB_USERNAME, 'root');
define(DB_PASSWORD, '');
define(DB_HOST, '127.0.0.1');
define(DB, '');




//$words = isset($_REQUEST['words']) ? trim($_REQUEST['words']):'';
if(isset($_GET['words']) && $_GET['words'] != ''){
        $words = htmlentities($_GET['words']);
}elseif(isset($_GET['term']) && $_GET['term'] != ''){
        $words = htmlentities($term);
}else{
        $words = '';
}

if(isset($_REQUEST['fileSaveAs']) && !empty($_REQUEST['fileSaveAs'])){
        $term = trim($_REQUEST['fileSaveAs']);
}elseif(isset($words) && $words != ''){
        $term = $words;
}else{
        $term = '';
}

//      check to see if the term has been downloaded
//      previously via this system
$termError = '';
if($term != ''){
        $icSql = "SELECT *
        FROM `FotoliaImages`
        WHERE `term` = '".mysqli_real_escape_string(strtolower($term))."'";
        $icRes = $db->query($icSql);
        $tqr = $db->num_rows($icRes);
        if($tqr > 0){
                $termError = '<div id="term_error">
                        An image was previously downloaded for <strong>"'.strtoupper($term).'"</strong>, to review the image click <a href="reviewDownloads.php?q='.$term.'">HERE</a>.<br />
                        If you proceed to download, a back up of the existing image will be created.
                </div>';
        }
}
//parked Current image sizes for cropping
$cropSizes = array(array(385,261),array(250,251));
/*
landing/web_hosting_landing.jpg 		385 x 261
column/web_hosting_column.jpg           201 x 152
header/web_hosting_header.jpg           572 x 174
*/

$whyParkSizes = array(
        'landing'=>array(385,261),
        'column'=>array(201,152),
        'header'=>array(572,174)
);

$parkedSizes = array(
        'landing'=>array(385,261),
        'result'=>array(250,251)
);

/* Sizes for new parked template program */
$newTemplateSizes = array(
        '250_100'=>array(250,100),
        '250_180'=>array(250,180),
        '250_250'=>array(250,250),
        '250_350'=>array(250,350),
        '375_150'=>array(375,150),
        '375_250'=>array(375,250),
        '375_350'=>array(375,350),
		'461_250'=>array(461,250),
        '500_100'=>array(500,100),
        '500_200'=>array(500,200),
        '500_300'=>array(500,300),
        '750_150'=>array(750,150),
        '750_250'=>array(750,250)
);

$sizeMatrix = array(
        'parked'=>$parkedSizes,
        'whypark'=>$whyParkSizes,
        'newTemplates'=>$newTemplateSizes
);
/*
* Retrieve a full & nice xmlrpc request debug, useful when you start
* Uncomment XMLRPC_debug_print(); in footer.php to display the debug
*/
define("DEBUG", 1);

/* istock specific vars */
$istock_order = array('BestMatch', 'Age', 'Contributor', 'Rating', 'Downloads', 'Title', 'Size');
$istock_perPage = array(20, 25, 30, 50,100);

ini_set("DISPLAY_ERROR", 1);
error_reporting(E_ALL);


function is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

function handler(Throwable $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
    exit();
}


function getImageRequests($db,$type="request", $start=0, $max=50){
	
        $sql = "SELECT r . * , q.imageId AS existing
                FROM  `VoodooImageRequest` r
                LEFT JOIN FotoliaImages q ON r.keyword = q.originalTerm
                WHERE `request_type` = '".$type."'
                ORDER BY r.`date` DESC 
                LIMIT ".$start." , ".$max;
        
	$res = $db->query($sql);
	$cnt = $db->num_rows($res);
	if($cnt <= 0){
		return "<li class='list-head'>No ".ucwords($type)." at this time</li>";
	}else{
		$return = "<li class='list-head'>".$cnt." ".ucwords($type)." </li>";
		while($row=$db->fetch_assoc($res)){
                        $img_for = ($row['account_id'] != 0 && $row['username'] != '') ? ''.$row['username'].'('.$row['account_id'].') for ' : ' ';
			$exists = ($row['existing'] != '') ? ' | <a href="http://img2.voodoo.com/istockphoto/istock/reviewDownloads.php?q='.stripslashes($row['keyword']).'">Image Exists</a>':'';
                        $return .= '<li>
				<a href="'.addslashes($row['keyword']).'" class="request-images">'.stripslashes($row['keyword']).'</a> 
				<br /><span>requested by '.$img_for.$row['domain'].' on '.$row['date'].'
                                <br /><a href="index.php?action=del_req&id='.$row['request_id'].'">delete</a> '.$exists.'</span>
			</li>';
		}
	}
	return $return;
}
?>