<?php
require_once '../include/inc.config.php';
include_once(BASEPATH.'mailer.class.php');

$daysPast = 10;
$uid = 6;
$limit = 10;
$mailList = array();

// $uSql = "SELECT * from WeeklyMailer where 1 limit ".$limit;
$uSql = "SELECT w.MailerId, w.contactId, c.email
FROM WeeklyMailer w
LEFT JOIN siteContacts c
USING ( contactId ) 
WHERE w.contactId =6
LIMIT ".$limit;

$uRes = $db->query($uSql);
$uCount = $db->num_rows($uRes);
if($uRes <= 0){
    exit();
}
while($row = $db->fetch_assoc($uRes)){
    $mailList[] = $row['MailerId'];
    $uid = $row['contactId'];
    $prefLocation = $row['prefLocation'];
    $optCode = stripslashes($row['sessionCode']);
    $email = stripslashes($row['email']);
    
    $jobs = getNewJobs($db, $uid, $prefLocation,$daysPast);
    
    //echo $jobs;
    if($jobs != false){
        $message = '<div style="color: #7c7c7c; font-size:13px; font-family: Roboto, sans-serif;">'
                . '<p>Hello, <br>'
                . 'You\'re receiving this email because you are subscribed to the '.SITE.' weekly job listing email.</p>'
                . '<p>'
                . 'Below are new positions, posted within the last '.$daysPast.' days, that we think you may find interesting. Just click the title to be taken'
                . 'to the full description of the job listing or to contact your specialized recruiter.'
                . '</p>'
                . '</div>';
        $message .= $jobs;
        //add unsubscribe footer
        $message .= '<div style="color: #7c7c7c; font-size:13px; font-family: Roboto, sans-serif;">'
                . 'If you have received this message in error, or no longer wish to subscribe to this service, please follw this link to <a href="http://cesfl.com/unsubscribe.php?optcode='.$optCode.'">unsubscribe</a>.'
                . '</div>';
        $my_mail = new attach_mailer('Criterion Executive Search', 'no-reply@cesfl.com', $email, $cc = "", $bcc = "", 'New Job Listings at '.SITE, stripslashes($message));
        $my_mail->process_mail();
    }
}

function getNewJobs($db, $uid, $prefLocation, $daysPast){
    $sqlLocation = '';
    if($prefLocation != 0){
        $lSql = "SELECT SUBSTRING(Location, 1, INSTR(Location, \", \")) as loc FROM `Locations` WHERE LocationId = ".$prefLocation." ";
        $lRes = $db->query($lSql);
        list($loc) = $db->fetch_row($lRes);
        $sqlLocation = "AND jobLocation like '%".$loc."%' ";
    }
    
    $sql = "SELECT jobId, jobTitle, SUBSTRING(jobDescription,1,240) as jobDescription, MATCH(jobDescription) AGAINST((SELECT SubscriptionText from JobSubscriptionData WHERE contactId = ".$uid." limit 1)) as Relevance 
    from jobposts
    WHERE MATCH(jobDescription) AGAINST((SELECT SubscriptionText from JobSubscriptionData WHERE contactId = ".$uid." limit 1) IN BOOLEAN MODE) 
    AND dateAdded >= DATE_ADD(CURDATE(), INTERVAL -".$daysPast." DAY)  
    ".$sqlLocation."     
    HAVING Relevance > 0.2 
    ORDER BY Relevance DESC";
    
    $res = $db->query($sql) or die($sql);
    $count = $db->num_rows($res);
    //no jobs, no continue
    if($count <= 0){
        return false;
    }
    // have jobs, build pretty display
    $return .= '<div class="job-container" style="color: #7c7c7c; font-size:13px; font-family: Roboto, sans-serif;">';
    while($row = $db->fetch_assoc($res)){
        $jobUrl = makePrettyUrl($row['jobId'], $row['jobTitle'], 'jobs');
        $return .= '<div class="job-listing" style="border-color: #EFEFEF; background: #FFF; margin-bottom: 15px;">'
                . '<h2 ><a href="'.$jobUrl.'" style="color:#496CAD; text-decoartion: none;">'.stripslashes($row['jobTitle']).'</a></h2>'
                . '<p>'.stripslashes(strip_tags($row['jobDescription'])).'</p>';
        
    }
    $return .= '</div>';
    return $return;
    
}

?>
