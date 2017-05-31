<?php
namespace Api;
use DB\DB;
use Curl\Curl;
class Api{
    private $private_key = 'N2#D{51;RC@F417Uy#kWzTbK7P"(iR';
    
    private $api_client = '';
    private $user_name = "donnysimonton";
    private $user_pass = "Keypath01";
    private $api_url = 'http://api.istockphoto.com'; 
    private $api_secureUrl = array('https://secure-api.istockphoto.com','443');
    private $api_key = 'e66ab4ff93a262474e54cfab'; 
    private $api_session = '';

    private $download_path = '/www/fotolia/downloads/';
    private $image_backup = '/www/fotolia/imageBackup/';
    private $web_path = '../fotolia/downloads/';

    private $language_id = 2; // US English
    private $minPerPage = 50;

    private $cropSizes = array(array(385,261),array(250,251));

    /* Sizes for voodoo */
    private $newTemplateSizes = array(
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

    private $sizeMatrix = array();
    /* istock specific vars */
    private $istock_order = array('BestMatch', 'Age', 'Contributor', 'Rating', 'Downloads', 'Title', 'Size');
    private $istock_perPage = array(20, 25, 30, 50,100);

    private $response = array('meta'=>array(),'items'=>array(), 'error'=>array());
    
    public function __construct() {
        // check the private key
        if(!$this->checkPrivateKey()){
            //$this->errorHandler('Invalid Private Key');
            //return false;
        }

        $this->sizeMatrix = array(
           'newTemplates'=>$this->newTemplateSizes
        );
        if(!isset($_SESSION['access_token'])){
            $this->getLoginHandle();
        }else{
            $this->messageHandler('meta','sessionId',$_REQUEST['sessionId']);
        } 


    }
         
    private function checkPrivateKey(){
        $pk = urldecode(trim(stripslashes($_REQUEST['private_key'])));
        if($pk != $this->private_key){
            return false;
        }else{
            return true;
        }
    }
    
    
    
    
    public function getLoginHandle(){
        $endpoint = 'https://connect.gettyimages.com/';
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->post($endpoint.'oauth2/token/', array(
            'client_id' => API_KEY,
            'client_secret' =>API_SECRET,
            'grant_type' => 'client_credentials'
        ));
        if($curl->httpStatusCode != '200' ){
            die('Status: '.$curl->httpStatusCode );
        }else{
            //$_SESSION['access_token'] = $curl->response['access_token']; 
            print_r($curl->response->access_token);
        }
        $curl->close();
    }
    
    
    
    
    /*
     * Utilities
     */
    
    /*
     * 
     */
    private function messageHandler($type="meta", $key='', $message){
        if($key != ''){
            $this->response[$type][$key] = $message;
        }else{
            $this->response[$type][] = $message;
        }
    }
        
    private function errorHandler($message){
        $this->messageHandler("meta",'has_error', 'true');
        $this->messageHandler('error','error_message',$message);
    }
    private function responseHandler($message=array()){
        if(is_array($message)){
                $output = $message; //array_map("htmlspecialchars",$message);
                $this->messageHandler('items','',$output);


        }
    }
}