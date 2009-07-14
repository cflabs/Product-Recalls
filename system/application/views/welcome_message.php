<div id="divHomeForms">
	<div class="divContainer">
		
		<div id="divForm">
			<form method="post" action="<?php echo site_url('signup'); ?>">
				<h3 class="signup">Sign Up</h3>
				<ul class="form">
					<li>
						<label for="txtEmail">Email address</label>
						<input id="txtEmail" name="txtEmail" type="text" class="textbox" value="" /><br class="clear" />
					</li>
					<li>
						<label for="ddlCat">Which category?</label>
						<? $catdropdown = 'class="large"';?>
						<?php echo form_dropdown('ddlCat',$categories,array(),$catdropdown); ?>
						<br class="clear" />
					</li>
					<li>
						<label for="ddlFrequency">How often?</label>
						<select id="ddlFrequency" name="ddlFrequency" class="small">
							<option value="w" selected="selected">Weekly</option>
							<option value="m">Monthly</option>
						</select>
						<br class="clear" />
					</li>
					<li>
						<input type="submit" class="button" value="Create Alert &raquo;" />
					</li>
					<li class="examplelink">See an <a href="<?php echo site_url("about/example"); ?>" title="example of the product recall notices">example</a> email notice</li>
				</ul>
			</form>
		</div>
		
		<div id="divSearch">
			<form method="post" action="<?php echo site_url("search"); ?>">
				<h3 class="search">Search</h3>
				<ul class="form">
					<li>
						<input id="txtSearch" name="txtSearch" type="text" class="textbox" value="" />

						
					</li>
					<li>
						<?php echo form_dropdown('ddlCategory',$categories_slug)?>
						<input type="submit" class="button" value="Go" />
					</li>
					<li>e.g. <a href="<?php echo site_url(array('search','all', 'travel_adaptor')); ?>" title="product recall search for 'travel adaptor'">travel adaptor</a>, <a href="<?php echo site_url(array('search','other', 'mouth_wash')); ?>" title="product recall search for 'mouth wash'">mouth wash</a> or <a href="<?php echo site_url(array('search','motor-vehicles', 'peugeot')); ?>" title="product recall search for 'peugeot'">peugeot</a></li>
				</ul>
			</form>
		</div>
		<br class="clear"/>
	</div>
</div>
<div id="divLatest">
	<div class="divContainer">
		<span class="rssfeed"><a class="rssfeed" href="<?php echo site_url('feed'); ?>" title="Latest product recalls RSS feed">RSS Feed</a></span>
		<h3>Latest Recalls </h3>
<?php foreach($recalls as $recall):?>		
		<div class="item">
			<h4><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="Read full alert for <?php echo $recall['product_name'];?>"><?php echo $recall['product_name'];?></a><?php if ($recall['status']=="removed") : ?> (Removed)<?php endif; ?><?php if ($recall['status']=="updated") : ?> (Updated)<?php endif; ?></h4>
			<p class="itemdescription"><?php echo nl2br_except_pre(word_limiter($recall['description'],20));?></p>
			<p class="itemlink"><a href="<?php echo site_url("recall/view/".$recall['internal_url']); ?>" title="Read full details for <?php echo $recall['product_name'];?>">Full details &raquo;</a></p>
		</div>
<?php endforeach;?>
		<div class="item">
			<a href="<?php echo site_url("recall"); ?>" title="view all recalls">View all recalls &raquo;</a>
		</div>
	</div>
</div>
<script type="text/javascript" defer="defer">setFocus("txtEmail");</script>
