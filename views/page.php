<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $this->title; ?></title>
	<base href="<?php print Config::SERVICE_URL; ?>">
	<link rel="stylesheet" href="./stuff/styles.css" media="all" />
</head>
<body>
<?php print $this->body; ?>
<hr />
<div style="float:left;">&copy; 2009 Валера Леонтьев a.k.a feedbee. habrometr.ru<br /><span style="font-size:80%;color:#333333">Время считается по UTC+0300 (Московское)</span></div>
<div style="float:right;">
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t44.1;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border=0 width=31 height=31><\/a>")//--></script><!--/LiveInternet-->

</div>
<!-- <?php print $_SERVER['REQUEST_URI']; ?> -->

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-7166216-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>