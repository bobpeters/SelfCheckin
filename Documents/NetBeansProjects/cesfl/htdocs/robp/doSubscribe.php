<?php
require_once './include/inc.config.php';
include_once './include/class.filter.php';

$rows = array();
$filter = new filter();
//get our job post details
$jobId = trim($_POST['jobId']);

if($jobId == ''){
    header('Location: http://cesfl.com/browse.htm');
    exit();
}
$sql = "SELECT job.jobId, job.jobTitle, job.jobLocation
	FROM `jobposts` job
	WHERE job.jobId = ".$jobId." 
	AND dateAdded <= now( )
	ORDER BY jobId DESC 
	LIMIT 1";
$res = $db->query($sql);
while($r = $db->fetch_assoc($res)){
	foreach($r as $k=>$v){
		$rows[$k] = stripslashes($v);
	}

}
//do bulk of the work here
$uid = 0;
$location = '';
$jobTitleSimple = $rows['jobTitle'];
$pattern = '/(\([a-z]+\))|(\;?\s?\(?([a-z\s\.\/]+,)+\s?[a-z]{2}\)?)/isx';
$ck = preg_match($pattern, $rows['jobTitle'],$matches);
if($ck !== false){
    $location = $matches[0];
    $jobTitleSimple = trim(str_replace($location, '', $rows['jobTitle']));
}
$name = mysql_real_escape_string(stripcslashes($_POST['name']));
$email = mysql_real_escape_string(stripcslashes($_POST['email']));
$phone = mysql_real_escape_string(stripcslashes($_POST['phone']));
$prefLocation = mysql_real_escape_string(stripcslashes($_POST['location']));

//generate hash for this user
$sessionCode = getToken(50);        
$uSql = "INSERT INTO siteContacts VALUES ('','$name','$email','$phone',$prefLocation,'$sessionCode',NOW()) "
        . "ON DUPLICATE KEY UPDATE "
        . "phone='$phone', "
        . "prefLocation= $prefLocation,"
        . "date = NOW()";
$res = $db->query($uSql) or die (mysql_error());
if($res){
    $uid = $db->insert_id();
}

$message = 'UID: '.$uid;

$pageTitle = 'Confirm subscription to Jobs Similar to:   '.$rows['jobTitle'];
$pageCSS = 'module.front.page.about.min.css';
$addonCSS = '<link rel="stylesheet" href="./css/admin/module.admin.page.form_elements.min.css" />';
$metaContent = "Confirm Subscribing to job listings similar to ".$rows['jobTitle'];
$metaAbstract = "Confirm Subscribing to job listings similar to ".$rows['jobTitle'];
$footerJS = '';
include_once './inc.header.php';
?>
<div class="container-960 innerT">
<h3 class="glyphicons circle_info margin-none"><i></i>Confirm Your Subscription<span></span></h3>
<div class="separator bottom"></div>
<div class="row">
    <div class="col-md-12" >
        <h1 ><?=$pageTitle?></h1>
        
        
        
        <div class="separator bottom"></div>
        <p><?=$message?></p>
    </div>
</div>
<div class="separator bottom"></div>
<a name="contactor"></a>

</div>
<div class="separator bottom"></div>
<div class="modal fade" id="modal-simple" aria-hidden="true" style="display: none;">
	
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal heading -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 class="modal-title">Exception</h3>
			</div>
			<!-- // Modal heading END -->
			
			<!-- Modal body -->
			<div class="modal-body">
				<p>All fields are required for Subscription.</p>
			</div>
			<!-- // Modal body END -->
			
			<!-- Modal footer -->
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">Close</a> 
				
			</div>
			<!-- // Modal footer END -->

		</div>
	</div>
	
</div>
<script type="text/javascript">
normalizePhone = function(pn){
    //validate first (US only)
    if(!/^1?[\-\. ]?\(?(\d{3})\)?[\-\. ]?\d{3}[\-\. ]?\d{4}$/.test(pn)) return false;
    //remove prefix 1
    if(pn[0]==1) pn = pn.slice(1);
    //remove any characters except for the digits
    var npn = pn.replace(/\D/g,'');
    //currently returns format: 8885550000
    //Any other formatting can be done here.
    return npn;
};
function validateForm(e) {
    e.preventDefault();
  var isValid = true;
  $('.form-control').each(function() {
    if ( $(this).val() === '' )
        isValid = false;
       $(this).parent().addClass('has-error');
       
  });
    if(isValid){
        $('#inline-contact').submit();
    }else{
        isValid = true;
    }
}
</script>
<?php 
include_once './inc.footer.php';

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}
?>
