<h1>Select a user</h1>
<ul>
 <?php
	foreach ($params['users'] as $user):
		$safeuser = escape($user);
		echo "<li><a href='{$safeuser}/'>{$safeuser}</li>\n";
	endforeach;
 ?>
</ul>
