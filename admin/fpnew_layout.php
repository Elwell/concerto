<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo join(' - ',array('Concerto Panel', $this->getTitle()));?></title>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/admin_new.css" />
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/fp_new.css" />
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/ui.lightbox.css" />
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/ui.tablesort.css" />
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/ui.jquery.css" />

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/ieonly.css" />
<![endif]-->

<!--[if lt IE 7.]>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_BASE_URL?>css/ie6.css" />
<script defer type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/pngfix.js"></script>
<![endif]-->

<script type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/ui.lightbox.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/ui.tablesort.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/ui.jquery.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_BASE_URL?>js/fpnew.js"></script>

<meta name="generator" content="Concerto <?php echo CONCERTO_VERSION ?>">
<meta name="application-name" content="Concerto"/>
<meta name="description" content="RPI Digital Signage for Everyone."/>
<meta name="application-url" content="http://<?php echo $_SERVER['SERVER_NAME'] . ADMIN_URL?>"/>
<link rel="icon" href="<?php echo ADMIN_BASE_URL?>images/concerto_32x32.png" sizes="32x32"/>
<link rel="icon" href="<?php echo ADMIN_BASE_URL?>images/concerto_48x48.png" sizes="48x48"/>

<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<link rel="alternate" href="" type="application/rss+xml" title="" id="gallery" />
       
<?php //renderHeadExtras() ?>
</head>

<body>

<div id="header"<?php if (!isLoggedIn()) { ?> class="header-login"<?php } ?>>
  <div id="header_padding">
    <?php include("includes/menu_tabs.php"); ?>
  </div>
</div>

<div id="main">
	<?php renderMessages() ?>
	<?php $this->render();//renderAction() ?>
</div>

<!-- BEGIN Sidebar -->
<?php include("includes/left_menu.php"); ?>
<!-- END Sidebar -->

<div id="footer_gutter">&nbsp;</div>
<div id="footer">
  <div id="footer_padding">
    <p>Copyright &copy; 2009 Rensselaer Polytechnic Institute (<a href="http://webtech.union.rpi.edu">Web Technologies Group</a>)</p>
    <p><a href="<?php echo ADMIN_URL ?>/pages/show/docs/">Support Center</a> | <a href="http://webtech.union.rpi.edu/ticket">Submit Help Ticket</a> | <a href="mailto:<?php echo SYSTEM_EMAIL ?>">Contact Us</a></p>
  </div>
</div>
<?php if(defined('GA_TRACKING') && GA_TRACKING) { ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("<?php echo GA_TRACKING ?>");
pageTracker._trackPageview();
</script>
<?php } ?>
</body>
</html>

<?php
function renderMessage($type, $msg)
{
   switch($type)
   {
      case "error": $col='red'; break;
      case "warn": $col='yellow'; break;
      case "stat": $col='green'; break;
      case "info": default: $col='#069';$text='white'; break;
   }
   return '<div class="alertmess ' . $type . '"><p>'.
      $msg."</p></div>\n";
}
?>
