<h1><a href="../">Blogcollab</a></h1>
<h2>Select an entry for <?php echo escape($params['user']); ?></h2>
<ul>
	<?php
	$path = escape(rtrim(preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']), '/'));
	foreach ($params['entries'] as $entry):
		$safeentry = escape($entry);
		$safeentryname = escape(substr($entry, 0, strrpos($entry, '.')));
		echo "<li><a href='{$path}/{$safeentry}'>{$safeentryname}</li>\n";
	endforeach;
	?>
</ul>
