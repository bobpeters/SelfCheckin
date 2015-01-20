<!DOCTYPE html>
<!--[if lt IE 7]> <html class="front ie lt-ie9 lt-ie8 lt-ie7 fluid top-full menuh-top"> <![endif]-->
<!--[if IE 7]>    <html class="front ie lt-ie9 lt-ie8 fluid top-full menuh-top sticky-top"> <![endif]-->
<!--[if IE 8]>    <html class="front ie lt-ie9 fluid top-full menuh-top sticky-top"> <![endif]-->
<!--[if gt IE 8]> <html class="animations front ie gt-ie8 fluid top-full menuh-top sticky-top"> <![endif]-->
<!--[if !IE]><!--><html class="animations front fluid top-full menuh-top sticky-top"><!-- <![endif]-->
<head>
	<title><?=$pageTitle?> </title>
	
	<!-- Meta -->
	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta content="<?=$metaContent?>" name="description" />
        <meta content="<?=$metaContent?>" name="abstract" />
        <?=$lastModified?>
        
	
	

		<!--[if lt IE 9]><link rel="stylesheet" href="/components/library/bootstrap/css/bootstrap.min.css" /><![endif]-->
                <?=$addonCSS?>
                <link rel="stylesheet" href="/css/front/<?=$pageCSS?>" />
                
                <style>
                    html.front.top-full .navbar.main .btn-navbar{
                       background-color: #000;
                    }
                    @media (max-width: 767px){ 
                        html.front.top-full .navbar.main .appbrand {
                            font-size: 1.5em;
                        }
                    }
                </style>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

	<script src="/components/library/jquery/jquery.min.js?v=v2.3.0"></script>
        <script src="/components/library/jquery/jquery-migrate.min.js?v=v2.3.0"></script>
        <script src="/components/library/modernizr/modernizr.js?v=v2.3.0"></script>
        
        <script src="/components/modules/admin/charts/flot/assets/lib/excanvas.js?v=v2.3.0"></script>
        <script src="/components/plugins/browser/ie/ie.prototype.polyfill.js?v=v2.3.0"></script>	<script>if (/*@cc_on!@*/false && document.documentMode === 10) { document.documentElement.className+=' ie ie10'; }</script>

</head>
<body>
	
	<!-- Main Container Fluid -->
	<div class="container-fluid menu-hidden">
		
		<!-- Content -->
		<div id="content">
		
		<?php
                    include_once 'inc.topnavbar.php';
                ?>
		
                