<!Doctype html>
<html>
    <head>
        <title><?= $title; ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="car parking, parking spaces, automobile parking, parking lot, paid parking, prepaid parking, parkings erservation, airport parking, event parking"/>
        <meta name="description" content="Looking for <?= $location." ".$parkingType ?> parking? Compare & Save when you make an easy online reservation today! Great rates on <?= $location?> parking. "/>
        <meta name="abstract" content="Looking for <?= $location." ".$parkingType ?> parking? Compare & Save when you make an easy online reservation today! Great rates on <?= $location?> parking. "/>
        <meta property="og:site_name" content="Parkway Parking"/>
        <meta property="og:url" content="<?= Router::reverse($this->request, true) ?>"/>
        <meta property="og:image" content=""/>
        <meta property="og:description" content="Parkway Parking is the premiere service for finding and reserving parking spaces at airports, cruise terminals and special events. With Parkway you're guranteed to find the closest, and least expensive, parking spaces available."/>
        <meta name="author" content="High Octane Brands <highoctanebrands.com>">
        <meta name="copyright" content="Parkway Parking <?=date('Y',time())?>" />
        <!-- Latest compiled and minified Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        
       
        

        <!-- Default theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<!-- custom css <link rel="stylesheet" href="css/custom.css" > -->
        <?= $this->Html->css('jquery.datetimepicker'); ?>
        <?= $this->Html->css('custom'); ?>
        
        
        
        <!-- frame buster -->
        <script>if ( top !== self ) top.location.replace( self.location.href );</script>
        
        <!-- icons and the like -->
        <link rel="shortcut icon" href="img/favicon.ico" />
        <link rel="apple-touch-icon" href="img/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png" />
    </head>
    <body>
        <?= $this->element('mainNavigation'); ?>
        <div id="scroll-animate">
            <div id="scroll-animate-main">
                <div class="wrapper-parallax">
                    <?php echo $this->Session->flash(); ?>
                    <?php echo $this->Session->flash('auth'); ?>
                    <?php echo $this->fetch('content'); ?>
                    
                    
                    
                        

                    <?= $this->element('footer'); ?>
                </div>
            </div>
        </div>
        
        <!-- custom load JS -->
        <!-- Latest compiled and minified Bootstrap JavaScript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
         
       
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        
        <!-- HTML 5 Shim for IE -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?= $this->Html->script('jquery.datetimepicker.full.min'); ?>
        <?= $this->Html->script('custom'); ?>
        <?= $this->element('ga'); ?>
        <!-- <script src="js/custom.js"></script> -->
    </body>
</html>
