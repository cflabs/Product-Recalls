<!DOCTYPE
 html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
	<title><?php echo $page_title ?> (BETA)</title>
	<link rel="stylesheet" media="all" href="/css/cflabs.css" />
	<link rel="stylesheet" media="all" href="/css/main.css" />
	
	<link rel="alternate" type="application/rss+xml" title="<?php echo FEED_NAME ?>" href="<?php echo site_url("feed"); ?>" />
	<?php foreach($feeds as $feeditem):?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo $feeditem['feed_name']; ?>" href="<?php echo $feeditem['feed_url']; ?>" />
	<?php endforeach;?>
	<script type="text/javascript" src="/javascript/main.js"></script>
	
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<div id="divMenu">
	<div class="divContainer">
		<ul class="collapse">
			<li class="recentUpdates"><?php echo $recent_recalls; ?> updates this month</li>
			<li <?php if ($menu == 'feedback') :?>class="active"<?php endif; ?>><a href="<?php echo site_url("about/feedback");?>" title="Send us your feedback">Feedback</a></li>
			<li <?php if ($menu == 'about') :?>class="active"<?php endif; ?>><a href="<?php echo site_url("about");?>" title="About <?php echo SITE_NAME ?>">About</a></li>
			<li <?php if ($menu == 'categories') :?>class="active"<?php endif; ?>><a href="<?php echo site_url("categories");?>" title="View recall categories">Categories</a></li>
			<li <?php if ($menu == 'default') :?>class="active"<?php endif; ?>><a href="<?php echo site_url("");?>" title="Signup for email alerts and search the database">Signup &amp; Search</a></li>
		</ul>
		<br class="clear" />
	</div>
</div>
<div id="divHead">
	<div class="divContainer">
		
		<h1><a href="<?php echo site_url("");?>" title="<?php echo SITE_NAME?> homepage"><?php echo SITE_NAME?></a><span class="beta">beta (test mode)</span></h1>
		<h2><?php echo SITE_TAGLINE?></h2>

	</div>
</div>
