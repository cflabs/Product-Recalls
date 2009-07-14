<div id="divContent">
	<div class="divContainer">
		<h3>Recall Categories</h3>

		<table width="100%" cellspacing="0" border="0">
		<thead>
			<tr>
				<th>Category</th>
				<th>Recalls</th>
				<th>Last Update</th>
				<th class="link">More Details</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($categories as $cat) :?>
			<tr>
				<td><a href="<?php echo site_url(array("category",$cat['slug']));?>" title="view all recalls from the <?php echo $cat['name']; ?> category"><?php echo $cat['name']; ?></a></td>
				<td><?php echo $cat['recalls']; ?></td>
				<td><?php echo date("d M Y",mysql_to_unix($cat['last_updated'])); ?></td>
				<td class="link"><a href="<?php echo site_url(array("category",$cat['slug']));?>" title="View all <?php echo $cat['name']; ?> recalls">View &raquo;</a> &nbsp;&nbsp;&nbsp;<a class="rssfeed" href="<?php echo site_url(array("feed","category",$cat['slug']));?>" title="subscribe to <?php echo $cat['name']; ?> rss feed"> </a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>


	</div>
</div>
