<?php 
include "../include/connect_db.php";
include "../include/func.php";


$serv =  explode("/",$_SERVER['REQUEST_URI']);
// print_r($serv);
// echo sizeof($serv);
if(sizeof($serv)>2){
	$path = "../";
}else{
	$path = "./";
}
?>

  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Dashboard - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <meta name="msapplication-TileColor" content="#206bc4"/>
    <meta name="theme-color" content="#206bc4"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="robots" content="noindex,nofollow,noarchive"/>
    <link rel="icon" href="<?php echo $path;?>favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo $path;?>favicon.ico" type="image/x-icon"/>
    <!-- CSS files -->
    <link href="<?php echo $path;?>dist/libs/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
	<link href="<?php echo $path;?>dist/libs/selectize/dist/css/selectize.css" rel="stylesheet"/>
    <link href="<?php echo $path;?>dist/libs/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>
    <link href="<?php echo $path;?>dist/libs/nouislider/distribute/nouislider.min.css" rel="stylesheet"/>
    <link href="<?php echo $path;?>dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="<?php echo $path;?>dist/css/demo.min.css" rel="stylesheet"/>
    <style>
      body {
      	display: none;
      }
    </style>
  </head>
    <!--</div>-->
    
    <!-- Libs JS -->
    