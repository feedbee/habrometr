<!DOCTYPE html>
<html>
<head>
	<title><?php print $this->title; ?></title>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?php print Config::SERVICE_URL; ?>/" />

	<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="./css/habrometr.css" media="all" />
	<style>
		body {
			padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
		}
    </style>
	<!-- IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body>

<div class="container">

	<?php print $this->body; ?>

</div>

<footer class="footer">
	<div class="container">
		<div style="float:left;">&copy; 2009—2012 Валера Леонтьев a.k.a feedbee. habrometr.ru<br />
			<span style="font-size:80%;color:#333333">Время считается по UTC+0300 (Московское)</span>
		</div>

		<div style="float:right;">
			<!--LiveInternet counter-->
			<script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img src='http://counter.yadro.ru/hit?t44.1;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";"+Math.random()+
			"' alt='' title='LiveInternet' "+
			"border=0 width=31 height=31><\/a>")//--></script>
			<!--/LiveInternet-->
		</div>
	</div>
</footer>

<!-- <?php print $_SERVER['REQUEST_URI']; ?> -->

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="./bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-7166216-1");
	pageTracker._trackPageview();
	} catch(err) {}
</script>