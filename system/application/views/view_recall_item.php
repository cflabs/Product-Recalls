<div id="divContent"><div class="divContainer">
<?php foreach($recalls as $recall):?>
  <h3><?php echo $recall['product_name'];?> <?php if ($recall['status']=="removed") :?>(Removed)<?php endif; ?><?php if ($recall['status']=="updated") :?>(Updated)<?php endif; ?><br/><span><?php echo date("d-M-Y",mysql_to_unix($recall['date_scraped'])); ?></span></h3>
  
  <h4>Details</h4>
  <div class="divItemInfo"><?php echo nl2br($recall['description']);?></div>
  <h4>Danger</h4>
  <div class="divItemInfo"><?php echo nl2br($recall['danger']);?></div>
  <h4>Action Taken</h4>
  <div class="divItemInfo"><?php echo nl2br($recall['measures_taken']);?></div>


  <?php if ($recall['status']=="removed") :?>
  	<h4>Recall Removed</h4>
  	<div class="divItemInfo">This recall was removed by the source on <?php echo date("d-M-Y",mysql_to_unix($recall['status_updated'])); ?>.
  	<?php if ($recall['status_text'] != "") : ?><br/><strong>Reason:</strong> <?php echo $recall['status_text']; ?>.<?php else : ?><br/>No reason for this removal was provided<?php endif; ?>
  	</div>
  <?php endif; ?>

  <?php if ($recall['status']=="updated") :?>
  	<h4>Recall Updated</h4>
  	<div class="divItemInfo">This recall was updated by the source on <?php echo date("d-M-Y",mysql_to_unix($recall['status_updated'])); ?>.
  	</div>
  <?php endif; ?>
  
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style" style="width:200px;float:right;padding-top:30px;">
<a href="http://www.addthis.com/bookmark.php?v=250&pub=xa-4a5370fb4218dcc3" class="addthis_button_compact">Share</a>
<span class="addthis_separator">|</span>
<a class="addthis_button_facebook"></a>
<a class="addthis_button_myspace"></a>
<a class="addthis_button_google"></a>
<a class="addthis_button_twitter"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a5370fb4218dcc3"></script>
<!-- AddThis Button END -->

  
  <p><strong>Source:</strong> <a href="<?php echo $recall['external_url']; ?>" title="View original source for <?php echo $recall['product_name'];?> recall"><?php echo $recall['source_name']; ?> (<?php echo $recall['source_id']; ?>)</a>
  <br/><strong>Category:</strong> <a href="<?php echo site_url(array('category',$recall['category_slug'])); ?>" title="view all recalls of <?php echo $recall['category_name']; ?>"><?php echo $recall['category_name']; ?></a>
  <br/><strong>On:</strong> <?php echo date("d-M-Y",mysql_to_unix($recall['date_scraped'])); ?></p>
<?php endforeach;?>

</div></div>