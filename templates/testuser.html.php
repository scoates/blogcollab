<html>
 <head>
  <title>Blogcollab – <?php echo escape($params['title']); ?></title>
 </head>
 <body>
  <h1>
   <a href="./">Test User</a>'s blog:
   <?php echo escape($params['title']);?>
  </h1>
<?php echo nl2br(escape($params['content'])); ?>
 </body>
</html>
