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
$return_type = (isset($_REQUEST['return_type'])) ? trim($_REQUEST['return_type']):'array';
$response = $remote->getResponse($return_type);
include_once 'inc.header.php';
?>

<div class="container">
    <div class="row">
        <?php
        $nr = 1;
        foreach($response['items'] as $image){
            $img = (array) $image['display_sizes'][0];
            $dim = (array) $image['max_dimensions'];
            $uriParts = explode('?', $img['uri'], 2);
            $imgURI = $uriParts[0];
            //print_r($image);
        ?>
        <div class="col-md-3 col-sm-2">
            <div class="img-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="img-align">
                            <a href="<?=$imgURI?>" rel="group1" title="<?=addslashes($image['title'])?>" class="getty-gallery" data-fancybox="gallery">
                                <img class="img-responsive" src="<?=$imgURI?>" alt="<?=addslashes($image['title'])?>">
                            </a>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><?=$image['title']?></h5>
                        </div>
                    </div>
                    <div class="row">  
                        <div class="col-md-12">
                            <h5><?=$image['caption']?></h5>
                        </div>
                    </div>
                    <div class="row">    
                        <div class="col-md-6">
                            id: <?=$image['id']?>
                        </div>
                        <div class="col-md-6">
                            <span class="desc-text"><?=$dim['width']?> x <?=$dim['height']?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="ajax.php" method="post" id="img<?=$image['id']?>">
                                <input type="hidden" name="fileId" value="<?=$image['id']?>">
                                <input type="hidden" name="width" value="<?=$dim['width']?>">
                                <input type="hidden" name="height" value="<?=$dim['height']?>">
                                <input type="hidden" name="private_key" value="<?=VOODOO_API_KEY?>">
                                <input type="hidden" name="fileSaveAs" value="<?=stripslashes(trim($_REQUEST['words']))?>">
                                <input type="hidden" name="action" value="proxy">
                                <input type="hidden" name="type" value="purchase">
                                <input type="submit" class="btn btn-lrg btn-success" name="purchase" value="Purchase" >
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        
        <?php
            if($nr % 4 == 0){
                echo "</div><div class=\"row\">";
            }
        $nr++;
        }
        ?>
    
</div>
    

<?php
        include_once 'inc.footer.php';

ob_end_flush();