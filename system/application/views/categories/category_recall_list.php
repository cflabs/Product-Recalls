<div id="divContent"><div class="divContainer">

<h3>Recalls of <?php echo $category->name; ?> <?php if ($pagenum != "") :?><span><?php echo $pagenum ?></span><?php endif ?></h3>

<?php foreach($recalls as $recall):?>
<div class="divItem">
	<h4><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="view full details for <?php echo $recall['product_name'];?>"><?php echo $recall['product_name'];?></a><?php if ($recall['status']=="removed") : ?> (Removed)<?php endif; ?><?php if ($recall['status']=="updated") : ?> (Updated)<?php endif; ?></h4>
	<p class="itemdescription"><?php echo word_limiter($recall['description'],20);?></p>
	<p class="itemlink"><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="Read full details for <?php echo $recall['product_name'];?>">Full details &raquo;</a></p>
</div>
<?php endforeach;?>

<?php if ($links !== "") : ?>
<p>Page: <?echo $links ?></p>
<?php endif; ?>

</div></div>