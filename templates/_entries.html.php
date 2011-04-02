<h1>Select an entry for <?php echo escape($params['user']); ?></h1>
<ul>
	<?php
	$path = escape(rtrim($_SERVER['REQUEST_URI'], '/'));
	foreach ($params['entries'] as $entry):
		$safeentry = escape($entry);
		$safeentryname = escape(substr($entry, 0, strrpos($entry, '.')));
		echo "<li><a href='{$path}/{$safeentry}'>{$safeentryname}</li>\n";
	endforeach;
	?>
</ul>
