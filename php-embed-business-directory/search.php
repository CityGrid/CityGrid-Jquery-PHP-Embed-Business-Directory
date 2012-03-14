<?php
include "/var/www/html/system/common.php";
include "config.php";

//echo "HERE:" . isset($_POST['pubcode']) . "<br />"
if(isset($_POST['pubcode']))
	{
	
	$pubcode = $_POST['pubcode'];
	$encypubcode = encrypt($pubcode,$Salt);
	//echo $pubcode;
	$pubcode = $pubcode - 0;
	//echo is_numeric($pubcode);
	if(is_numeric($pubcode)) 
		{
	
		$authStore = "authlog.txt";
		$fh = fopen($authStore, 'r');
		$AccessList = fread($fh, filesize($authStore));
		$AccessList = str_replace("\r","",$AccessList);
		$AccessList = json_decode($AccessList);
		fclose ($fh);	
		
		$NewAuth = '{"IP":"' . $_SERVER['HTTP_REFERER'] . '","PubCode":"' . $encypubcode . '"}';	
		$AddAuth = json_decode($NewAuth);
		array_push($AccessList,$AddAuth);
		
		$NewAuthListJSON = json_encode($AccessList);
		
		$fh = fopen($authStore, "w");
		if($fh==false) { die("unable to create file"); }
		fputs($fh,$NewAuthListJSON,strlen($NewAuthListJSON));
		fclose ($fh);
		
		header ("Location:" . $_SERVER['HTTP_REFERER']);
		
		}
		
	}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$authStore = "authlog.txt";
$fh = fopen($authStore, 'r');
$AccessList = fread($fh, filesize($authStore));
$AccessList = str_replace("\r","",$AccessList);
$AccessList = json_decode($AccessList);
fclose ($fh);

$found = 0;
$pubcode = "test";

foreach ($AccessList as $key => $value) {
	//echo $value->IP . " = " . $_SERVER['HTTP_REFERER'] . "<br />";
    if ($value->IP == $_SERVER['HTTP_REFERER']) {
     	$found = 1;  
     	$pubcode = decrypt($value->PubCode,$Salt);
     	$publishercode = $pubcode;
    	}
	}
	
if($found!=1)
	{
	echo $_GET['jsoncallback'] . '({"auth":"0"})';
	}
else
	{

	date_default_timezone_set("America/Los_Angeles");
	
	$ThisPage = $_SERVER['PHP_SELF'];
	$ThisURL = $_SERVER['REQUEST_URI'];
	$ThisHost = $_SERVER['HTTP_HOST'];								
	
	include "config.php";
	
	include "/var/www/html/system/class-citygrid-places.php";
	include "/var/www/html/system/class-citygrid-advertising.php";
	include "/var/www/html/system/class-utility.php";
	
	// what
	if(isset($_REQUEST['what'])){ $what = $_REQUEST['what'];} elseif(isset($_POST['what'])){$what = $_POST['what']; } else { $what=''; }
	// type
	if(isset($_REQUEST['type'])){ $type = $_REQUEST['type'];} elseif(isset($_POST['type'])){$type = $_POST['type']; } else { $type=''; }
	// where
	if(isset($_REQUEST['where'])){ $where = $_REQUEST['where'];} elseif(isset($_POST['where'])){$where = $_POST['where']; } else { $where=$Site_Where; }
	
	// page
	if(isset($_REQUEST['page'])){ $page = $_REQUEST['page'];} elseif(isset($_POST['page'])){$page = $_POST['page']; } else { $page=1; }
	// rpp
	if(isset($_REQUEST['rpp'])){ $rpp = $_REQUEST['rpp'];} elseif(isset($_POST['rpp'])){$rpp = $_POST['rpp']; } else { $rpp=10; }
	// sort
	if(isset($_REQUEST['sort'])){ $sort = $_REQUEST['sort'];} elseif(isset($_POST['sort'])){$sort = $_POST['sort']; } else { $sort='dist'; }
	
	$max = 2;
	$format='json';
	
	$placement=null;
	$has_offers=false;
	$histograms=false;
	$i=null;
	$type=null;
	$format='json';
	
	//Get All Active APIs
	$citygrid = new citygridplaces($publishercode);
	$search = $citygrid->srch_places_where($what,$type,$where,$page,$rpp,$sort,$format,$placement,$has_offers,$histograms,$i);
	$search = json_encode($search);
	echo $_GET['jsoncallback'] . '(' . $search . ')';
	//echo $_GET['jsoncallback'] . '({"auth":"' . $_SERVER['HTTP_REFERER'] . '"})';
	}
?>
