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
  
<!-- ShareThis Button BEGIN -->
<div style="width:150px; float: right; padding-top: 30px;"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=c1c9b7ca-2eef-40d3-beac-bab0387dec1a&amp;type=website&amp;buttonText=Share%20This%20Recall"></script></div>
<!-- ShareThis Button END -->

  
  <p><strong>Source:</strong> <a href="<?php echo $recall['external_url']; ?>" title="View original source for <?php echo $recall['product_name'];?> recall"><?php echo $recall['source_name']; ?> (<?php echo $recall['source_id']; ?>)</a>
  <br/><strong>Category:</strong> <a href="<?php echo site_url(array('category',$recall['category_slug'])); ?>" title="view all recalls of <?php echo $recall['category_name']; ?>"><?php echo $recall['category_name']; ?></a>
  <br/><strong>On:</strong> <?php echo date("d-M-Y",mysql_to_unix($recall['date_scraped'])); ?></p>
<?php endforeach;?>

</div></div>