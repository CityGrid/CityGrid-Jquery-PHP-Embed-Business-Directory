<?php
date_default_timezone_set("America/Los_Angeles");

$ThisPage = $_SERVER['PHP_SELF'];
$ThisURL = $_SERVER['REQUEST_URI'];
$ThisHost = $_SERVER['HTTP_HOST'];								

include "config.php";

include "/var/www/html/system/class-citygrid-places.php";
include "/var/www/html/system/class-citygrid-advertising.php";
include "/var/www/html/system/class-utility.php";
include "/var/www/html/system/class-flickr-photos.php";
include "/var/www/html/system/phpFlickr.php";

// Get All Business Categories from Database
//$utility = new utility($dbserver,$dbname,$dbuser,$dbpassword);
//$BusinessCategories = $utility->getBusinessCategories();

$myStore = "/var/www/html/system/business-categories-datastore.txt";
$fh = fopen($myStore, 'r');
//$BusinessCategories = fgets($Content);
$BusinessCategories = fread($fh, filesize($myStore));

$BusinessCategories = str_replace("\r","",$BusinessCategories);

$BusinessCategory = json_decode($BusinessCategories);
//  Set a random Business Category for Use Elsewhere
$FeatureCategory = $BusinessCategory[array_rand($BusinessCategory, 1)]->Name;
$what = $FeatureCategory;
$where = $Site_Where;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  
    <meta charset="utf-8">
    
    <title><?php echo $Site_Name;?></title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    
	<script type="text/javascript" src="http://static.citygridmedia.com/ads/scripts/v2/loader.js"></script>

    <link href="bootstrap.css" rel="stylesheet">
    
    <style type="text/css">
      body {
        padding: 25px;
      }
    </style>
    
    <link rel="stylesheet" href="/thickbox.css" type="text/css" media="screen" />
    
  </head>

  <body>   