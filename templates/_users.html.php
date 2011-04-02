<h1>Blogcollab</h1>
<h2>Select a user</h2>
<ul>
 <?php
	foreach ($params['users'] as $user):
		$safeuser = escape($user);
		echo "<li><a href='{$safeuser}/'>{$safeuser}</li>\n";
	endforeach;
 ?>
</ul>
