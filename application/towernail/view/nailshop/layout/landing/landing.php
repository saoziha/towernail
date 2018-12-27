<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1">
		<!-- SEO SECTION -->
		<meta name="title" content="Tower Nails &amp; Beauty">
		<meta name="description" content="Tower Nails and Beauty offer better care for your beauty. Phone (03)9725437 for booking at TowerBeauty. Tower Junction, Troup Drive, Addington, Christchurch, NZ.">

		<meta property="og:title" content="Tower Nails &amp; Beauty">
		<meta property="og:type" content="website">
		<meta property="og:url" content="http://towerbeauty.co.nz/">
		<meta property="og:description" content="Tower Nails and Beauty offer better care for your beauty. Phone (03)9725437 for booking at TowerBeauty. Tower Junction, Troup Drive, Addington, Christchurch, NZ.">
		<meta property="og:image" content="public/towernail/shop-7.jpg">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:url" content="http://towerbeauty.co.nz/">
		<meta name="twitter:title" content="Tower Nails &amp; Beauty">
		<meta name="twitter:description" content="Tower Nails and Beauty offer better care for your beauty. Phone (03)9725437 for booking at TowerBeauty. Tower Junction, Troup Drive, Addington, Christchurch, NZ.">
		<meta name="twitter:image" content="public/towernail/shop-7.jpg">
		<!-- END SEO SECTION -->

		<!-- favicon -->
		<link rel="shortcut icon" type="image/ico" href="public/images/favicon.ico"/>
		<!-- Loading third party fonts -->
		<link href="public/fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
		<link href="public/fonts/novecento-font/novecento-font.css" rel="stylesheet" type="text/css">

		<?php
			$this->css();
		?>
		<title>Tower Nails & Beauty</title>
	</head>


	<body class="homepage">
		<div id="site-content">
			<star name="header" wrap="false"></star>
			<?php $this->page(); ?>
			<star name="footer" wrap="false"></star>
		</div>
	</body>
</html>

<?php
	Mirana_Extension::getJs("jquery");
	$this->js();
?>
<script src="public/js/plugins.js"></script>
<script src="public/js/app.js"></script>
<!--[if lt IE 9]>
<script src="public/js/ie-support/html5.js"></script>
<script src="public/js/ie-support/respond.js"></script>
<![endif]-->
