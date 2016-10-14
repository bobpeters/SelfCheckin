<?php
$patterns = array(
    '@\-{2,}@is',
    '@\:{2,}@is',
    '@\\\\{1,}@is',
    '@\/{1,}@is',
    '@\s{2,}@is',
    '@(\d{4})[\D](\d{2})[\D](\d{2})[\D](\d{1,2})[\D](\d{2})[\D]?(am|pm|a|p|a\.m\.|p\.m\.)?@is'
);
$replacements = array(
    '-',
    ':',
    '-',
    '-',
    '\s',
    '$1-$2-$3 $4:$5 $6'
);
$cdIn = '2016-10-15 2:00 pm';
$cdOut = '2016-11-27T10:00:10';//'2016-10-15 5:00 pm';


//$timePattern = '@(\d{4})[\D](\d{2})[\D](\d{2})[\D](\d{1,2})[\D](\d{2})[\D](am|pm|\s)@is';
//$timeReplace = '$1-$2-$3 $4:$5 $6';
//$matches = preg_replace($timePattern, $timeReplace, $cdOut);
//print_r($matches);



$cleanCheckinDate = preg_replace($patterns, $replacements, $cdIn);
$cleanCheckoutDate = preg_replace($patterns, $replacements, $cdOut);

try {
    $cIn = new DateTime($cleanCheckinDate);
} catch (Exception $e) {
    echo 'Shit went pear shaped';
    print_r($cleanCheckinDate);
}

try {
    $cOut = new DateTime($cleanCheckoutDate);
} catch (Exception $e) {
    echo 'No, shit went oval';
    print_r($cleanCheckoutDate);
}

$interval = $cIn->diff($cOut, 1);
$days = $interval->format('%a');
//bastard patch
$cInx = strtotime($cleanCheckinDate);
$cOutx = strtotime($cleanCheckoutDate);
// Actual
$intervalx = $cOutx - $cInx;
$dayst = ceil($intervalx / 86400);
//end nullshit patch
//modified DT 
$seconds = $interval->format('%d');
$dtDays = ceil($seconds/86400);


echo '<br>Days original DT:'.$days.'<br>';
echo 'Days modified traditional:'.$dayst.'<br>';
echo 'Days modified DT:'.$seconds.' / '.$dtDays.'<br>';
print_r($interval);