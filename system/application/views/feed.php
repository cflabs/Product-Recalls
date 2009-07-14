<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">
    
    <channel>
    	<title><?php echo $feed_name; ?></title>
    	<link><?php echo $feed_url; ?></link>
    	<description><?php echo $page_description; ?></description>
    	<dc:language><?php echo $page_language; ?></dc:language>
    	<dc:creator><?php echo $creator_email; ?></dc:creator>

    	<admin:generatorAgent rdf:resource="http://www.codeigniter.com/" />
    	
    	<?php foreach($posts as $entry): ?>
    	
    	<item>
    		<title><?php echo xml_convert($entry['product_name']); ?></title>
    		<link><?php echo site_url('recall/view/' . $entry['internal_url']) ?></link>
    		<guid><?php echo site_url('recall/view/' . $entry['internal_url']) ?></guid>
    		<description><![CDATA[<?= nl2br("<h2>Description</h2><p>".$entry['description']."</p><h2>Danger</h2><p>".$entry['danger']."</p><h2>Actions Taken</h2><p>".$entry['measures_taken']."</p>"); ?>]]></description>
    		<pubDate><?php echo date ('r',mysql_to_unix($entry['date_scraped'])); ?></pubDate>
    	</item>
    	
    	<?php endforeach ?>
    </channel>
</rss>