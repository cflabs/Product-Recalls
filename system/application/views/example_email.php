<div id="divContent">
	<div class="divContainer">
		<h3>About <?php echo SITE_NAME; ?></h3>
		<h4>Example Email</h4>

<p><strong>To:</strong> you@youremail.com
<br/><strong>From:</strong> <?php echo EMAILER_ADDRESS; ?>
<br/><strong>Subject:</strong> <?php echo str_replace("%n",1,str_replace("%t","Lloytron Carbon Monoxide Alarm",EMAILER_SUBJECT)); ?></p>

<div class="example">
<pre>
Product recall notices issued since 30-Apr-2009

------------------------------------------------------------

Wooden Christmas tree and Apple tree

DETAILS:
Brand: FLORABASE
 Type/number of model: Wooden Christmas tree (Sapin en Bois): ref. 259273,
Apple tree (Arbre POM): ref. 259283

 Description: Christmas tree: height 1.40 m, consisting of pieces of wood
nailed to a trunk representing branches. Apple tree: imitation apple tree
with twigs nailed together as branches. 

 Country of origin: Netherlands

DANGER:
Cuts 
 The product poses a risk of cuts because of several sharp protruding
points.

ACTION TAKEN:
Voluntary withdrawal from the market by the importer.

More Info:
<?php echo site_url(array("recalls","view","florabase-wooden-christmas-tree-and-apple-tree")); ?>


------------------------------------------------------------

This email has been sent to you because you subscribed to the service from Product 
Recalls.
If you do not wish to receive any future emails, please unsubscribe by visiting 
this address: 
<?php echo site_url(array("signup","unsubscribe","6cf169335eeec4a4b768")); ?>
</pre>
</div>

	</div>
</div>

