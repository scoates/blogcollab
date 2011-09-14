<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Sean Coates blogs: <?php echo $params['title']?></title>
		<meta charset="UTF-8" content="text/html; charset=UTF-8" http-equiv="content-type">
		<link href="http://seancoates.com/css/reset.css" media="screen" rel="stylesheet" type="text/css">
		<link href="http://seancoates.com/css/layout.css" media="screen" rel="stylesheet" type="text/css">

		<link rel="alternate" type="application/atom+xml" title="Atom" href="http://seancoates.com/blogs/php-53-on-snow-leopard/atom">

		<script type="text/javascript" src="http://seancoates.com/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="http://seancoates.com/js/hashgrid.js"></script>
		<script type="text/javascript" src="http://seancoates.com/js/blogs.js"></script>

	</head>

	<body id="blogs">

		<div id="container">

			<div id="header">
				<h1>Sean Coates</h1>
				<ul>
					<li class="blogs"><a href="http://seancoates.com/blogs">blogs</a></li>
					<li class="brews"><a href="http://seancoates.com/brews">brews</a></li>
					<li class="shares"><a href="http://seancoates.com/shares">shares</a></li>
					<li class="codes"><a href="http://seancoates.com/codes">codes</a></li>

					<li class="is"><a href="http://seancoates.com/is">is</a></li>
				</ul>
				<h2>
					<span>about</span>
					<a class="inactive" href="http://seancoates.com/blogs/about-php">PHP</a>,
					<a class="inactive" href="http://seancoates.com/blogs/about-beer">Beer</a> &amp;
					<a class="inactive" href="http://seancoates.com/blogs/about-the-web">the Web</a>.</h2>

			</div><!-- /header -->



	<ol id="posts">
		<li class="post" id="post-116">
			<h3><a href="http://seancoates.com/blogs/#unpublished"><?php echo $params['title']; ?></a></h3>

<div class="post_meta">
	<ul>
		<li class="active"><a href="http://seancoates.com/blogs/about-php">PHP</a></li>

		<li><a href="http://seancoates.com/blogs/about-beer">Beer</a></li>
		<li><a href="http://seancoates.com/blogs/about-the-web">Web</a></li>
	</ul>

	<p class="date">2010 Jul 04</p>
	<p class="comments"><a href="#comments" title="Read Comments">0 Comments</a></p>
</div>

<div class="post_content">
 <?php /* poor man's highlighting (-: */ echo preg_replace_callback('/<!\[CDATA\[(.*?)\]\]>/sm', function($m) { return escape($m[1]); }, $params['content']); ?>
</div>
		</li>

	</ol>

			</div><!-- /main_content -->
		</div><!-- /container -->
	</body>
</html>
