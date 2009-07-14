<div id="divContent"><div class="divContainer">

<form method="post" action="<?php echo site_url("recall/search"); ?>">
	<h3>Search <span id="minisearch"><input id="txtSearch" name="txtSearch" type="text" class="textbox" value="" /> <input type="submit" class="button" value="Go" /></span></h3>
</form>

<p>There was a problem with the value you entered:</p>
<?php echo validation_errors('<div class="error">','</div>'); ?>


<br class="clear"/>
</div></div>