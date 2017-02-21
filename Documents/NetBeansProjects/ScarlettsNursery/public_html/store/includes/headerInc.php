<?php if (!isset($_COOKIE["cookieid"])) {
        setcookie("cookieid", gen_id(20), time() + 60 * 60 * 24 * 30, '/');
    }  #30 days to expire/delete user shopping basket
    
    include BASE_PATH.'inc.internal-header.php';
    include_once BASE_PATH.'inc.subheader.php';
    include_once BASE_PATH.'inc.header-nav.php';
$addonCSS = '<link href="'.SITE_URL . CSS_FOLDER.'swipebox.css" rel="stylesheet">'
        . '<link href="'.SITE_URL . CSS_FOLDER.'font-awesome.css" rel="stylesheet">'
        . '<link href="'.SITE_URL . CSS_FOLDER.'style.css" rel="stylesheet">'
        . '<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="'.SITE_URL . CSS_FOLDER.'carousel.css" rel="stylesheet">'
        . '<style>
            span.cur_priceid {
                display: none;
            }

            .panel-title a, .btn-lg {
                font-size: 18px;
            }
        </style>';
        if(trim(RECAPTCHA_SITEKEY)!=null) {
	if(basename($_SERVER['PHP_SELF'])=='contactus.php') {
		echo "<script src='https://www.google.com/recaptcha/api.js?hl=".RECAPTCHA_LAN."'></script>";
		}
	}

$basketItems = basket_summary(get_basket_items($conn, 1, 0));
print_r($basketItems);
if($basketItems !== false && $basketItems >= 1){
$hasBasket =  
    '<div id="basket-total" class="input-group pull-left" style="margin:5px 0 0 10px">
                    <a href="store/cart" class="btn btn-primary active">
                        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                        <span class="cart_summary">'.$basketItems.'</span>
                    </a>
                </div>';
}