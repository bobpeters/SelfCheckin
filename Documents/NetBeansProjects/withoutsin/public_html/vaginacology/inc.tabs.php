<?php
include_once('inc.config.php');
//include_once LIB.'class.partialcache.php';

$cacheId = '';
$cacheName = '';
$cacheContent = '';
$cacheMakeTime = '';
$cacheTime = time() - (60*25);

$current_time = time(); 
$expire_time =  60 * 25; 

	//decisions, decisions
	//if(file_exists($file) && ($current_time - $expire_time < $file_time)) 

$cacheRes = $db->query("select * from vag_PartialCache where cacheName = 'tabs' limit 1");

$cachedCount = $db->num_rows($cacheRes);
if($cachedCount > 0){
    list($cacheId, $cacheName, $cacheContent, $cacheMakeTime) = $db->fetch_row($cacheRes);
}
$cacheExp = (int)$cacheMakeTime+$expire_time;
if($cachedCount <= 0 || ( $cacheMakeTime != '' && $cacheExp < $current_time )){

    //mail('bobpeters@gmail.com',"making cache at".time()," cache expired as a less than time result. PFC  :: $cacheExp :: $cacheTime ::  ".$current_time);

 ob_start();   

//$ocache = new partialcache('/home4/whtnght/public_html/cache/partial/','tabs');
$popSql = 'SELECT COUNT( commentId ) AS biggy, c.imgId, v.hallOfFame, v.date
FROM  `vag_comments` c
left join vag_pictures v using (imgId)
WHERE 1 
GROUP BY c.imgId
ORDER BY biggy DESC 
LIMIT 10';
//$popRes = $db->query($popSql);

//files 
$fileSql = "SELECT imgId 
FROM  `vag_dl_files` 
WHERE 1 
ORDER BY  `vag_dl_files`.`fileId` DESC 
LIMIT 10";
$fileRes = $db->query($fileSql) or die(mysql_error());

// newest comments
$comSql = 'SELECT c.imgId, c.displayName, c.comment, c.comDate, v.hallOfFame, v.date
FROM  `vag_comments` c
LEFT JOIN vag_pictures v
USING ( imgId ) 
WHERE 1 
ORDER BY c.commentId DESC 
LIMIT 10';
$comRes = $db->query($comSql);
?>
<div class="tab_widget">
			
				<ul class="tabs"> 
					<li class="active"><em title="tab1">More Sites</em></li>
					<li><em title="tab2">New Downloads</em></li>
					<li><em title="tab3">Comments</em></li>
				</ul>	

				<div class="tab_container"> 

					 <div id="tab1" class="tab_content">
                                             
                                             
                                            <div class="tab_article_preview">
                                                 
						
							<div class="tab_article_preview_th"><a href="http://myasiantoy.com/" target="_blank" title="Free Asian Sex Video Website, Very Unique Videos"><img style="max-width: 50px;" src="/img/sites/XXX-logo.jpg" alt="Logo for MyAsianSexToy.com" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://myasiantoy.com/" target="_blank" title="Free Asian Sex Video Website, Very Unique Videos">Free Asian Sex Video Website, Very Unique Videos </a> </h3>
								<p>Posted on 05-19-2014  </p>
								
							</div>
						
						
						
							
						
						</div>

                                            <div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://redheadnextdoor.com/" target="_blank" title="Amazing, amateur, redheaded Girls from Next Door"><img style="max-width: 50px;" src="/img/sites/redheadnextdoor-red.png" alt="Logo for Redheadnextdoor.com" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://redheadnextdoor.com/" target="_blank" title="Amazing, amateur, redheaded Girls from Next Door">Redhead Next Door </a> - Amateur Redheads </h3>
								<p>Posted on 12-18-2013 |  <a href="https://twitter.com/RedheadNextPics" target="_blank" title="Follow Redheadnextdoor On Twitter">Follow on Twitter</a> </p>
								
							</div>
						
						</div>
                                             <div class="tab_article_preview">
                                                 
						
							<div class="tab_article_preview_th">
                                                            <a href="http://www.xxxizleyin.com/" target="_blank" title="porno izle"><img style="max-width: 50px;" src="/img/sites/siyahlogo.png" alt="porno izle" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://www.xxxizleyin.com/" target="_blank" title="porno izle">Porno Izle</a> </h3>
                                                                <p>Posted on 03-27-2014 |   </p>
								
							</div>
						
						</div>
                                             
                                             <div class="tab_article_preview">
						
							<div class="tab_article_preview_">
                                                            <a href="http://adult.wicked-butterfly.com" target="_blank" title="Sex Toys and adult novelties"><img style="max-width: 50px;" src="/img/sites/XXX-logo.jpg" alt="Sex toys and adult novelties" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://adult.wicked-butterfly.com" target="_blank" title="Sex Toys and adult novelties">Wicked Butterfly</a> </h3>
                                                                <p>Posted on 03-24-2014 |   <a href="https://twitter.com/wicked_gateway" target="_blank">Twitter</a> | <a href="http://www.facebook.com/wickedgateway" target="_blank">Facebook</a></p>
								
							</div>
						
						</div>
                                             <div class="tab_article_preview">
						
							<div class="tab_article_preview_th">
                                                            <a href="http://britishsexcontacts.com/" target="_blank" title="Sex Contacts"><img style="max-width: 50px;" src="/img/sites/britishsexcontacts.png" alt="Sex Contacts" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://britishsexcontacts.com/" target="_blank" title="Sex Contacts">Sex Contacts</a> </h3>
								<p>Posted on 03-07-2014 |   </p>
								
							</div>
						
						</div>
                                             <div class="tab_article_preview">
						
							<div class="tab_article_preview_th">
                                                            <a href="http://www.ladyboyescortsbangkok.com/" target="_blank" title="Ladyboy Escorts in Bangkok"><img style="max-width: 50px;" src="/img/sites/XXX-logo.jpg" alt="XXX Stuff here" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://www.ladyboyescortsbangkok.com/" target="_blank" title="Ladyboy Escorts in Bangkok">Ladyboy Escorts</a> </h3>
								<p>Posted on 03-02-2014 |   </p>
								
							</div>
						
						</div>
                                                <div class="tab_article_preview">
						
							<div class="tab_article_preview_th">
                                                            <a href="http://porn-xxxvideo.com/" target="_blank" title="Free porn xxx videos"><img style="max-width: 50px;" src="/img/sites/XXX-logo.jpg" alt="Logo for XXX porn Videos" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://porn-xxxvideo.com/" target="_blank" title="Free porn xxx videos">Free porn xxx videos</a> </h3>
								<p>Posted on 02-19-2014 |   </p>
								
							</div>
						
						</div>
                                                <div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://affiliate.cshtracker.com/rd/r.php?sid=585&pub=303641&c1=&c2=&c3=" target="_blank" title="Get ready for wild dating with naughty cuties and hunks now!"><img style="max-width: 50px;" src="/img/sites/logo-wildbuddies.png" alt="Logo for Wild Buddies" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://affiliate.cshtracker.com/rd/r.php?sid=585&pub=303641&c1=&c2=&c3=" target="_blank" title="Get ready for wild dating with naughty cuties and hunks now!">Get ready for wild dating with naughty cuties and hunks now!</a> </h3>
								<p>Posted on 02-13-2014 |   </p>
								
							</div>
						
						</div>
                                                <div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://SolelySeductive.com" target="_blank" title="Seductive Womens Clothes, Boots & Adult Toys"><img style="max-width: 50px;" src="/img/sites/Watermark1-solelyseductive.png" alt="Logo for Solely Seductive" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://SolelySeductive.com" target="_blank" title="Seductive Womens Clothes, Boots & Adult Toys">Seductive Womens Clothes, Boots & Adult Toys</a> </h3>
								<p>Posted on 01-03-2014 |   </p>
								
							</div>
						
						</div>
                                             <div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://www.shareasale.com/r.cfm?B=300756&U=482884&M=32100&urllink=" target="_blank" title="Beautiful Women Want You - 1st Date is Guaranteed"><img style="max-width: 50px;" src="/img/sites/first_date.jpg" alt="Logo for First Date" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://www.shareasale.com/r.cfm?B=300756&U=482884&M=32100&urllink=" target="_blank" title="Beautiful Women Want You - 1st Date is Guaranteed">Beautiful Women Want You - 1st Date is Guaranteed</a> </h3>
								<p>Posted on 01-03-2014 |   </p>
								
							</div>
						
						</div>
						<div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://gaytwinklove.com/" target="_blank" title="Gay Twink Videos"><img style="max-width: 50px;" src="/img/sites/gay-twink-love-logo22.png" alt="Logo for Gay Twink Videos" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://gaytwinklove.com/" target="_blank" title="Amazing, amateur, redheaded Girls from Next Door">Gay Twink Videos</a> </h3>
								<p>Posted on 01-02-2014 |   </p>
								
							</div>
						
						</div>
                                             
                                             
                                             <div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="http://fiverr.com/analogknight/add-your-website-link-to-my-adult-website" target="_blank" title="Add your link here"><!--<img style="max-width: 50px;" src="" alt="Logo for Redheadnextdoor.com" />--></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="http://fiverr.com/analogknight/add-your-website-link-to-my-adult-website" target="_blank" title="Your link Here">Add Your Link Here - $5</h3>
								<p>Posted on 12-18-2013 |  <a href="https://twitter.com/vaginacology" target="_blank" title="Follow Redheadnextdoor On Twitter">Follow on Twitter</a> </p>
								
							</div>
						
						</div>
                                               
						

					 </div><!-- #tab1 -->
					 
					 <div id="tab2" class="tab_content"> 
                                               <?
                                               while($row = $db->fetch_assoc($fileRes)){
                                               ?>
						<div class="tab_article_preview">
						
							<div class="tab_article_preview_th"><a href="/<?=$row['imgId']?>.htm" title="Guess her muff post #<?=$row['imgId']?>"><img style="max-width: 50px;" src="/vagina/<?=$row['imgId']?>/<?=$row['imgId']?>_teaser_thumb.jpg" alt="Guess Her Muff Post <?=$row['imgId']?>" /></a></div>
							
							<div class="tab_article_preview_content">
							
								<h3 class="dotted"><a href="/<?=$row['imgId']?>.htm" title="">Guess her muff Girl #<?=$row['imgId']?></a></h3>
                                                                <p> <?=getDownloadable($db, $row['imgId'])?> </p>
								
							</div>
						
						</div>
                                                <?
                                               }
                                               ?>
						

					 </div><!-- #tab2 -->
					 
                                         <div id="tab3" class="tab_content"> 
                                             <?
                                               while($row = $db->fetch_assoc($comRes)){
                                               ?>
						<div class="tab_last_comments">
						
							<div class="tab_last_comments_th"><a href="/<?=$row['imgId']?>.htm" title="Guess her muff post #<?=$row['imgId']?>"><img src="http://cdn.vaginacology.com/images/avatar.png" alt="avatar" /></a></div>
							
							<div class="tab_last_comments_content">
							
                                                            <p><span class="author"><a href="/<?=$row['imgId']?>.htm#comments" title="<?= stripslashes($row['displayName'])?> said"><?=stripslashes($row['displayName'])?></a></span> commented on <a href="/<?=$row['imgId']?>.htm"><?=$row['imgId']?></a> - <?=date('m-d-Y H:i',strtotime($row['comDate']))?></p>
                                                            <p class="italic"><a href="/<?=$row['imgId']?>.htm#comments" title="comment buy user"><?=substr(strip_tags(stripslashes($row['comment'])),0, 140)?> ...</a></p>
								
							</div>
						
						</div>
                                              <?
                                               }
                                               ?>
						

					 </div><!-- #tab3 -->
					 
				 </div> <!-- .tab_container --> 
				<!-- PFC  :: <?=$cacheMakeTime?> :: <?=$cacheTime?> ::  -->
			</div>
<?
// get contents and add to db
$data = ob_get_clean();
echo $data;
$data = mysql_real_escape_string($data);
$db->query("insert into vag_PartialCache values ('','tabs', '".$data."',".time().")
    ON DUPLICATE KEY UPDATE cacheContent='".$data."', cacheMakeTime = ".time());

}else{
    echo stripcslashes($cacheContent);
    echo "<!-- FCC :: ".$cacheMakeTime." :: -->";
}
?>