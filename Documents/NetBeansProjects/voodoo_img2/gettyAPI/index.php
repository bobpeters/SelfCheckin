<?php
error_reporting(E_ALL);

require_once 'include/inc.config.php';
set_exception_handler('handler');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if ( is_session_started() === FALSE ){
    session_start();
}
use Api\Api;
use Curl\Curl;

$remote = new Api;

$action = isset($_REQUEST['action']) ? trim(stripslashes($_REQUEST['action'])):'';

switch($action){
    case 'get_image_requests':
        $remote->getImageRequests($_REQUEST['image_request_type']);
        break;
    case 'search':
        $remote->getSearchResults();
        break;
    case 'get_image_info':
        $remote->getImageInfo();
        break;
    case 'purchase_image':
        $remote->purchaseImage();
        break;
    case 'delete_req':
        $remote->deleteImageRequest();
        break;
    case 'do_crop':
        $remote->doCrop();
        break;
    case 'search_previous':
        $remote->searchPrevious();
        break;
    case 'crop_previous':
        $remote->cropPrevious();
        break;
    case 'login':
        $remote->getLoginHandle();
        break;
    default:
        break;
            
}

//header('Content-Type: application/json');
$return_type = (isset($_REQUEST['return_type'])) ? trim($_REQUEST['return_type']):'json';
print_r( $remote->getResponse($return_type));
