<?php
require_once './include/inc.config.php';
include_once './include/class.filter.php';

$rows = array();
$filter = new filter();
//get our job post details
$jobId = trim($_GET['jobId']);
$sql = "SELECT job.jobId, 	job.jobTitle, 	job.salaryRange, 	job.jobDescription, 	job.jobLocation, 	job.dateAdded 	, pers.personnelId, 	CONCAT(firstName, ' ',lastName) as recName, pers.personnelId, dep.departmentId, dep.department
	FROM `jobposts` job
	JOIN departments dep
	USING ( departmentId )
	JOIN personnel pers
	USING ( personnelId )
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

$pageTitle = $rows['department'].' Job:  Detail for '.$rows['jobTitle'];


$pageCSS = 'module.front.page.about.min.css';
$addonCSS = '<link rel="stylesheet" href="./css/admin/module.admin.page.form_elements.min.css" />';
$metaContent = $rows['jobTitle']." job post in ".$rows['jobLocation']." available exclusively through ".SITE;
$metaAbstract = 'Details for the Job Post of '.$rows['jobTitle']. ' located in '.$rows['jobLocation'];
$footerJS = '<script src="./components/modules/front/assets/custom/js/contact.js?v=v2.3.0"></script><script src="./components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.3.0"></script>';
include_once './inc.header.php';
?>
<div class="container-960 innerT">
<h3 class="glyphicons circle_info margin-none"><i></i>Job Posting Detail<span></span></h3>
<div class="separator bottom"></div>
<div class="row">
    <div class="col-md-12" itemscope itemtype="http://schema.org/JobPosting">
        <h1 ><?=$pageTitle?></h1>
        <h3 itemprop="title"><?=$row['jobTitle']?></h3>
        <h5><strong>Posted</strong>: <span itemprop="datePosted"><?=date('F j, Y',strtotime($rows['dateAdded']))?> </span>in <a href="<?=makePrettyUrl($rows['departmentId'], $rows['department'],'jobs')?>" title="Link to department search results" ><span itemprop="industry"><?=$rows['department']?></a></a> <br>
        </h5>
        
        <div class="row">
            <div class="col-md-5">
                <strong>Location</strong>: <span itemprop="jobLocation"><?=$rows['jobLocation']?></span><br>
        <strong>Salary Range</strong>: <span itemprop="baseSalary"><?=$rows['salaryRange']?></span><br>
        <a href="#contactor" class="comments-link" title="<?=$rows['department']?> Recruiter">Contact <?=$rows['recName']?>, <?=$rows['department']?> Recruiter</a>
            </div>
            <div class="col-md-4">
                <?php
                    if(isset($_GET['testing'])){
                        ?>
                <strong>Receive Notices for New Jobs Like This</strong><br> 
                <form id="notice-form" name="notice-form" method="post" action="/subscribe.php" enctype="multipart/form-data">
                    <input type="hidden" name="jobId" value="<?=$rows['jobId']?>">
                    <button type="submit" class="btn btn-block btn-primary btn-icon glyphicons globe" name="submit" id="submit-subscribe"><i></i>Click Here</button>
                </form>
                <?php
                    }
                ?>
            </div>
            <div class="col-md-3"></div>
        </div>
        
        
        
        <div class="separator bottom"></div>
        <p itemprop="description"><?=stripslashes($filter->nl2br($rows['jobDescription']))?></p>
    </div>
</div>
<div class="separator bottom"></div>
<a name="contactor"></a>
<div class="row">
		<div class="col-md-7">
                    <form action="sendmail.php" method="post" id="inline-contact" name="inline-contact" enctype="multipart/form-data" class="row margin-none">
				<div class="row">
					<div class="col-md-6">
                                            <input type="text" disabled="disabled" class="form-control col-md-12" value="<?=$rows['recName']?>"/>
					</div>
					<div class="col-md-6"> 
                                            <input type="text" placeholder="YOUR NAME" name="name" id="name" value="" class="form-control col-md-12" />
					</div>
				</div>
                                <div class="separator bottom"></div>
                                <div class="row">
					<div class="col-md-6">
                                            <input type="text" placeholder="YOUR EMAIL ADDRESS" name="email" id="email" value="" class="form-control col-md-12" />
					</div>
					<div class="col-md-6"> 
                                            <input type="text" placeholder="YOUR PHONE NUMBER" name="phone" id="phone" value="" class="form-control col-md-12" />
					</div>
				</div>
				
				<div class="separator bottom"></div>

				<div class="row">
					<div class="col-md-12">
						<textarea name="message" id="message" class="form-control" rows="5" placeholder="YOUR MESSAGE"></textarea>
					</div>
				</div>
                                <div class="separator bottom"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="fileupload fileupload-new margin-none" data-provides="fileupload">
                                            <div class="input-group">
                                                <div class="form-control col-md-6">
                                                    <i class="fa fa-file fileupload-exists"></i> 
                                                    <span class="fileupload-preview"></span>
                                                </div>
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file">
                                                        <span class="fileupload-new">Select file</span>
                                                        <span class="fileupload-exists">Change</span>
                                                        <input name="fileatt" type="file" class="margin-none"></span>
                                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                </span>
                                            </div>
                                    </div>
                                    </div>
                                </div>
				<div class="separator bottom"></div>

				<div class="right">
                                    <input type="hidden" name="submitted" id="submitted" value="true" />
                                    <input type="hidden" name="pers_contact" id="pers_contact" value="<?=$rows['personnelId']?>" />
                                    <input type="hidden" name="jobId" id="jobId" value="<?=$rows['jobId']?>" />
                                    <button type="submit" class="btn btn-primary btn-icon glyphicons envelope"><i></i> Send message</button>    
				</div>
			</form>
		</div>
		<div class="col-md-5">
			<div class="well margin-none inverse">
				<address class="margin-none">
					<h2>Criterion Executive Search</h2>
                                        <strong>Contact <?=$rows['recName']?>, <?=$rows['department']?></strong> <br> 
					<strong><a href="#">Criterion Executive Search</a></strong><br>
                                        550 N. Reo Street<BR>
                                        Suite 101<br>
                                        Tampa, Florida 33609<br>
					<abbr title="Work email">e-mail:</abbr> <a href="mailto:<?=hideEmail('ces@cesfl.com')?>"><?=
hideEmail('ces@cesfl.com')?></a><br /> 
                                        <abbr title="Work Fax">fax:</abbr> 813-287-1660<br />
					<abbr title="Work Phone">phone:</abbr> 813.286.2000<br/>
					<div class="separator line"></div>
					
				</address>
			</div>
		</div>
	</div>
</div>
<div class="separator bottom"></div>
<?php 
include_once './inc.footer.php';
?>
