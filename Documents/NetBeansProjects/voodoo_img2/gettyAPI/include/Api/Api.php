<?php
namespace Api;
use DB\DB;
use Curl\Curl;
class Api{
    private $private_key = 'N2#D{51;RC@F417Uy#kWzTbK7P"(iR';
    
    private $api_client = '';
    private $user_name = "donnysimonton";
    private $user_pass = "Keypath01";
    private $api_session = '';

    private $download_path = '/www/fotolia/downloads/';
    private $image_backup = '/www/fotolia/imageBackup/';
    private $web_path = '../fotolia/downloads/';

    private $language_id = 2; // US English
    private $minPerPage = 50;

    private $cropSizes = array(array(385,261),array(250,251));
    
    private $search_params = array(
        'action'=>'false',
        'words'=>'jpg'
    );
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
    private $db;
    private $sizeMatrix = array();
    /* istock specific vars */
    private $istock_order = array('BestMatch', 'Age', 'Contributor', 'Rating', 'Downloads', 'Title', 'Size');
    private $istock_perPage = array(20, 25, 30, 50,100);

    public $response = array();
    
    public function __construct() {
        // check the private key
        if(!$this->checkPrivateKey()){
            //$this->errorHandler('Invalid Private Key');
            //return false;
        }
        $this->db = new db(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
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
    
    
    /**
     *  Check if sesion is valid
     */
    
    private function validateSession(){
        if((time() - $_SESSION['created']) > $_SESSION['expires']) {
            // session started more than 30 minutes ago
            $this->getLoginHandle();   // change session ID for the current session and invalidate old session ID
            
        }
        return true;
    }

    /**
     * requests login handle and stores access token 
     */
    
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
            die('Server Returned Status Code '.$curl->httpStatusCode );
        }else{
            $_SESSION['access_token'] = $curl->response->access_token;
            $_SESSION['expires'] = $curl->response->expires_in;
            $_SESSION['created'] = time();
            $this->messageHandler('meta', 'sessionId',$curl->response->access_token);
        }
        $curl->close();
    }
    
    /**
     * Get search results
     */
    public function getSearchResults(){
        // ummm. validate our session
        $this->validateSession();
        $endpoint = API_SECURE_URL.'search/images/creative';
        $params = array();
        $params['phrase'] = trim($_REQUEST['words']);
        $this->messageHandler('meta', 'words', $params['text']);
        $params['file_types'] = 'jpg';
        $params['graphical_styles'] = 'photography';
        $params['minimum_size'] = 'large';
        $params['orientations'] = 'horizontal';
        $params['sort_order'] = 'best_match';
        $params['page'] = 1;
        $params['page_size'] = $this->minPerPage;
        if(isset($_REQUEST['order'])){
                $params['sort_order'] = $_REQUEST['order'];
                $this->messageHandler('meta', 'order', $params['order']);
        }
        if(isset($_REQUEST['perPage'])){
                $params['page_size'] = $_REQUEST['perPage'];
                $this->messageHandler('meta', 'perPage', $params['perPage']);
        }else{
                $params['page_size'] = $this->minPerPage;
                $this->messageHandler('meta', 'perPage', $params['perPage']);
        }
        
        if(isset($_REQUEST['offset']) ){
                $params['page'] = isset($params['page_size']) ? ($_REQUEST['offset']/$params['page_size'])+1: ($_REQUEST['offset'] / $this->minPerPage) +1 ;
                $this->messageHandler('meta', 'page', $params['page']);
        }
        
        $this->messageHandler('meta','sessionId',$params['sessionid']);
        // Make the call
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_HTTPHEADER, array(
           'Api-Key: '.API_KEY,
            'Authorization: Bearer '.$_SESSION['access_token']
        ));
        $curl->get($endpoint, $params);
        
        if($curl->httpStatusCode != '200'){
            $this->errorHandler('Shit is Fucked here');
            print_r($curl);
            die();
        }
        $result = $curl->response;
        $curl->close();
       
       // print_r($result); //exit();
        
        //trigger_error("<pre>".print_r($result,1)."</pre>");
        // search for the existence of the keyword / image

        $ires = $this->db->query('SELECT * 
                                  FROM  `FotoliaImages` 
                                  WHERE originalTerm = "'.$params['text'].'" limit 1');
        $icnt = $this->db->num_rows($ires);
        $this->messageHandler('meta','exists', 'false');
        if($icnt > 0){
            $this->messageHandler('meta','exists', str_replace(' ','_',  urldecode($params['text'])));
        }
        
        if (is_object($result) && isset($result->faultCode) && $result->faultCode <> '' ) {

            trigger_error($result["faultCode"].': '.$result['faultString']);
            return false;
        } else {
                if(isset($result->result_count) && $result->result_count > 0 ){
                    $imageListPerPage = $this->minPerPage;
                    $imageListTotalItems = $result->result_count;
                    $totalPages = ceil($imageListTotalItems/$imageListPerPage);
                    
                    $this->messageHandler('meta','pageNum',$params['page']);
                    $this->messageHandler('meta','perPage',$this->minPerPage);
                    $this->messageHandler('meta','totalItems',$result->result_count);
                    $this->messageHandler('meta','totalPages',$totalPages);
                    //get a real image list
                    $imageList = $this->normalizeImageList($result->images);
                    //$this->responseHandler($imageList);
                    $paginated =  $this->getSearchPagination($imageListTotalItems, $imageListPerPage); 
                    $this->messageHandler('meta', 'pagination', $paginated);
                    
                }else{
                    $this->errorHandler('Search Results Returned 0 Results');
                }
        }
     }
    
    
    /*
     * Utilities
     */
    private function normalizeImageList($imageList = array()){
        
            //build a list
            if(!is_array($imageList)){
                $this->errorHandler('Error: 101. Returned 0 Results');
                return true;
            }
            foreach($imageList as $image){
                    if($image != ''){ 
                            $img = (array) $image;
                        $this->responseHandler($img);
                    }	
            }
            return true ; //$images;
    }
    
    private function getSearchPagination($nb_results, $nb_per_page=50, $overload = ''){
        $pagination = '<div class="pagination-wrapper">';

        //If no offset is set in the $_REQUEST array then it takes the value of 0
        if (!isset($_REQUEST['offset'])) {
            $offset = 0;
        } else {
            $offset = $_REQUEST['offset'];
        }

        $nb_pages = ceil($nb_results / $nb_per_page);
        $current_page_number = floor($offset  / $nb_per_page) + 1;
        $previous_link_qs = NULL;
        $next_link_qs = NULL;
        if ($current_page_number > 1) {
			 $previous_link_qs = 'offset=' . max((($current_page_number - 1) * $nb_per_page), 0);
        }

        if ($current_page_number < $nb_pages) {
            $next_link_qs = 'offset=' . min(($current_page_number * $nb_per_page), $nb_results - 1);
        }

        //The values in $_REQUEST are stored in this variable to be used later in links.
        $query_string = $this->buildSearchQueryString();
		//check the overload
		if(is_array($overload)){
			foreach($overload as $k=>$v){
				$query_string .= "&".$k."=".$v;
			}
		}
        //The "Previous" button
        if ($previous_link_qs != NULL) {
            $pagination .= '<a href="' . $previous_link_qs . $query_string .'" class="href_pagi"><div class="pagination">&#171; Previous</div></a>';
        }

        //Each page is then added to the list
        for ($page_number = 1; $page_number <= $nb_pages; $page_number++) {
                        if(
                                ($page_number > ($current_page_number-7) && $page_number < $current_page_number)
                                ||
                                ($page_number < ($current_page_number+7) && $page_number > $current_page_number)
                                ||
                                ($page_number == $current_page_number)
                        ){
                                $current_qs = ($page_number - 1) * $nb_per_page;
                                $pagination .= '<a href="/?offset=' . $current_qs . $query_string .'" class="href_pagi"><div class="';

                                if ($page_number == $current_page_number) {
                                        $pagination .= 'pagination_current';
                                }else{
                                        $pagination .= 'pagination';
                                }

                                $pagination .= '">' .$page_number . '</div></a>';
                        }
        }

        //The "Next" button
            if ($next_link_qs != NULL) {
                $pagination .= '<a href="' . $next_link_qs . $query_string .'" class="href_pagi"><div class="pagination">Next &#187;</div></a>';
            }
     return $pagination."</div>\r\n";

        }
    
    
    function buildSearchQueryString (){
        $query_string = '';

        foreach ($this->search_params as $value) {

            if ( isset($_REQUEST[$value]) && $_REQUEST[$value]<>'' ) {

                if ( is_array($_REQUEST[$value]) ) {

                    foreach ($_REQUEST[$value] as $filterName => $filterValue) {

                        $query_string .= '&filters[' . htmlentities($filterName) . ']=' . htmlentities($filterValue);

                    }

                } else {
                    //As "offset" will change in the URL of pagination buttons, every other value is stored to be added later in the URL
                    if ($value <> "offset") {
                        $query_string .= '&' . htmlentities($value) . '=' . $_REQUEST[$value];
                        
                    }

                }
	 } elseif ( isset($this->forced_params[$value]) ) {

                $query_string .= '&' . htmlentities($filterName) . '=' . htmlentities($filterValue);

            }

        }
                //modify for term setting
                if(isset($_REQUEST['term'])){
                        $query_string .= '&term='.htmlentities($_REQUEST['term']);
                }
                if(isset($_REQUEST['ref'])){
                        $query_string .= '&ref='.$_REQUEST['ref'].'&fastcrop=1';
                }
        return $query_string;

    }
    /*
     * 
     */
    private function messageHandler($type="meta", $key='', $message){
        if($key != ''){
            $this->response[$type][$key] = $message;
        }else{
            $this->response[$type][] = $message;
            //array_push($this->response[$type], $message);
            
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
    public function getResponse($type = 'array'){
        
        switch($type){
            case 'json':
                return json_encode($this->response,JSON_FORCE_OBJECT,512);
                break;
            case 'array':
            default:
                return print_r($this->response, true);
                break;
        }
    }
}