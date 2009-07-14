
<div class="clear"></div>
<div id="divFooter"> 
	<div class="divContainer">
		<div id="divFooterMenu"> 
			<ul class="collapse">
				<li><a href="<?php echo site_url(""); ?>" title="the <?php SITE_NAME?> homepage">Home</a></li>
				<li><a href="<?php echo site_url("about"); ?>" title="About <?php SITE_NAME?>">About</a></li>
				<li><a href="<?php echo site_url("categories"); ?>" title="Categories of recalls">Categories</a></li>
				<li><a href="<?php echo site_url("about#contact"); ?>" title="Contact the developers">Contact</a></li>
				<li><a href="<?php echo site_url("about/feedback"); ?>" title="Send us your feedback">Feedback</a></li>
			</ul>
			<br class="clear" /> 
		</div> 
		<div id="divFooterCredit"> 
			<ul class="collapse"> 
				<li>built by <a href="http://www.consumerfocuslabs.org/" title="Consumer Focus Labs - building online tools to make consumer's lives easier">Consumer Focus Labs</a></li> 
			</ul> 
			<br class="clear" /> 
		</div>
		<br class="clear" />
		<div id="divLicense">Data sourced from <a href="http://ec.europa.eu/consumers/dyna/rapex/rapex_archives_en.cfm" title="European Commission RAPEX product recalls">European Commission</a> Consumer Affairs website.</div> 
	</div> 
</div>
<?php if (GA_CODE != "") : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo GA_CODE; ?>");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif; ?>
</body>
</html>