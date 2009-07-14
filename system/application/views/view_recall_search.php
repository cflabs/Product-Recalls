<div id="divContent"><div class="divContainer">

<form method="post" action="<?php echo site_url("search"); ?>">
	<h3>We found <span><?php echo $result_count ?> recalls</span> for <span style="display:none;"><?echo $search_string ?></span><span id="minisearch"><input id="ddlCategory" name="ddlCategory" type="hidden" value="<?php echo $category->slug; ?>" /><input id="txtSearch" name="txtSearch" type="text" class="textbox" value="<?echo $search_string ?>" /> <input type="submit" class="button" value="Go" /></span> in <?php echo $category->name; ?></h3>
</form>

<p class="infobar"><a href="<?php echo site_url(array('search',$category->slug,$search_string)); ?>" title="permanent link for search '<?php echo $search_string ?>'">Permanent link</a> for this search. Subscribe to the <a class="rssfeed" href="<?php echo site_url(array('feed','search',$category->slug,$search_string)); ?>" title="rss feed for search '<?php echo $search_string ?>'">RSS feed</a> for these results.</p>

<?php foreach($recalls as $recall):?>
<div class="divItem">
	<h4><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="view full details for <?php echo $recall['product_name'];?>"><?php echo $recall['product_name'];?></a><?php if ($recall['status']=="removed") : ?> (Removed)<?php endif; ?><?php if ($recall['status']=="updated") : ?> (Updated)<?php endif; ?></h4>
	<p class="itemdescription"><?php echo nl2br_except_pre(word_limiter($recall['description'],20));?></p>
	<p class="itemlink"><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="Read full details for <?php echo $recall['product_name'];?>">Full details &raquo;</a></p>
</div>
<?php endforeach;?>

<br class="clear"/>

<?php if ($links !== "") : ?>
<p>Page: <?echo $links ?></p>
<?php endif; ?>


</div></div>