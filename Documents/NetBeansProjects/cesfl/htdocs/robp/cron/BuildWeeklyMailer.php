<?php
require_once './include/inc.config.php';
/*
 * Builds weekly mailer list in temp table to prevent recursion
 */

$sql = "INSERT INTO  `WeeklyMailer` ( 
SELECT  '', contactId, prefLocation
FROM siteContacts
WHERE isVerified =1 )";

$res = $db->query($sql);

if(!$res){
    mail('bobpeters@gmail.com', 'Problem building weekly mailer', mysql_error());
}
