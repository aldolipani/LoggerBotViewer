<?php
	
	session_start(); // Εκκίνηση  του session
	require_once("lib/db.php"); // Φόρτωση βιβλιοθήκης χειρισμού βάσης 
	$dbConnection = connectToDB(); // Δημιουργία global αντικειμένου σύνδεσης στην βάση και σύνδεση με την function που βρίσκεται στην db.php

	require_once("lib/user.php");// Φόρτωση βιβλιοθήκης χειρισμού χρηστών  
	require_once("lib/product.php"); // Φόρτωση βιβλιοθήκης χειρισμού προϊόντων
	require_once("lib/category.php"); // Φόρτωση βιβλιοθήκης χειρισμού κατηγοριών προϊόντων
	require_once("lib/order.php");// Φόρτωση βιβλιοθήκης χειρισμού παραγγελιών 
	require_once("lib/cart.php");// Φόρτωση βιβλιοθήκης χειρισμού καλαθιού αγορών
?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<script src="./js/script.js"></script>
		<!-- Τo βασικό css του site -->
		<link rel="stylesheet" type="text/css" href="./css/style.css"/>

		<!-- Τα css τα πήρα από το www.free-css για να κάνω το top και το customer menu -->
		<link rel="stylesheet" type="text/css" href="./css/topmenu.css"/>
		<link rel="stylesheet" type="text/css" href="./css/leftmenu.css"/>
		<link rel="stylesheet" type="text/css" href="./css/bluedream.css"/>
		<style type="text/css">
			label {
				float: left;
				width: 80px;
				font-weight: bold;
			}

			input {
				width: 200px;
			}
		</style>
		<title>My ebookstore</title>
	</head>
	<body>
		<div id="pageContainer"><!-- O Βασικός container -->
			<div id="leftDiv"><!-- Αριστερή στήλη -->
				<div id="logo">
					<img alt="logo" height="130" src="images/logo.gif" width="159"/>
					<h4>My e-Book Store</h4>
				</div>
				<div class="leftDivMenu">
				<?php 
				    
					require('menus/categories.php'); // Φωρτώνει το μενού των κατηγοριών
					// Εάν έχει οριστεί η session variable userID τότε εμφάνισε το customer menu
					if(isset($_SESSION['userID'])) {
						if($_SESSION['userID'] == 1)
							require('menus/admin.php');
						else
							require('menus/customer.php');
					}
				?>
				</div>
			</div>
			<div id="topDiv">			
				<div class="bottomPosMenu">
				   <?php require('menus/top.php'); ?>
				</div>
			</div>	
			<div id="contentDiv"><!-- O container των "υποσελίδων" -->
			<?php 
			   // Σε αυτό το σημείο ελέγχεται αν υπάρχει η GET μεταβλητή p και αναλόγως ενσωματώνει την κατάλληλη υποσελίδα 
			   if (isset($_GET['p'])){
				  require('pages/'.$_GET['p'].'.php');
			   }
			   else 
				  require('pages/home.php'); 
			 ?>
			</div>
			<div id="footerDiv">
				<a style="color:silver" href="http://erodios.it.teithe.gr/websystems">Ανάπτυξη Διαδικτυακών Συστημάτων και Εφαρμογών ΑΤΕΙ Θεσσαλονίκης </a>
			</div>
		</div>
	</body>
</html>