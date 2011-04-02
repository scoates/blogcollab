<?php

header('Content-type: text/html;charset=UTF-8');

define('USERDIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'content');
define('TEMPLATEDIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');

function do_404()
{
	if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
		$prot = $_SERVER['SERVER_PROTOCOL'];
	} else {
		$prot = 'HTTP/1.0';
	}
	header("{$prot} 404 Not Found");
}

function render($template, array $params, $useDefault = true)
{
	if (null !== $template) {
		ob_start();
		include TEMPLATEDIR . DIRECTORY_SEPARATOR . $template . '.html.php';
		$params['content'] = ob_get_clean();
	} 
	if ($useDefault) {
		include TEMPLATEDIR . DIRECTORY_SEPARATOR . '_default.html.php';
	} else {
		echo $params['content'];
	}
	exit;
}

function escape($str)
{
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

$users = array();
foreach (new DirectoryIterator(USERDIR) as $fi) {
	if (!$fi->isDot()) {
		$users[] = $fi->getFilename();
	}
}

$who = null;
$entry = null;
if (isset($_SERVER['PATH_INFO'])) {
	$parts = explode('/', $_SERVER['PATH_INFO']);
} elseif (isset($_SERVER['REQUEST_URI'])) {
	$parts = explode('/', urldecode($_SERVER['REQUEST_URI']));
} else {
	$parts = array();
}
$size = count($parts);
if ($size > 1) {
	$who = $parts[1];
}
if ($size > 2) {
	$entry = $parts[2];
}

if ($who && !in_array($who, $users)) {
	do_404();
	$content = 'invalid user <a href="' . escape($_SERVER['SCRIPT_NAME']) . '">try again?</a>';
	$title = 'Invalid user';
	render(null, compact('content', 'title'));
}

// TODO: invalid entry

// no user or entry
if (!$who) {
	$title = 'Select a user';
	render('_users', compact('title', 'users'));
}

// has user but no entry
$contentdir = USERDIR . DIRECTORY_SEPARATOR . $who;
if (!$entry) {
	if (!file_exists($contentdir)) {
		$content = "User " . escape($who) . " has no content directory.";
		$title = 'Error';
		render(null, compact('title', 'content'));
	}
	$entries = array();
	foreach (new DirectoryIterator($contentdir) as $fi) {
		if (!$fi->isDot()) {
			$fName = $fi->getFilename();
			if ($fName[0] != '.') { // ignore hidden files
				$entries[] = $fName;
			}
		}
	}
	$title = 'Select an entry for ' . escape($who);
	$user = $who;
	render('_entries', compact('title', 'entries', 'user'));
}

$templates = array();
foreach (new DirectoryIterator(TEMPLATEDIR) as $fi) {
	if (!$fi->isDot()) {
		$name = $fi->getFilename();
		if ($name[0] != '_') {
			$templates[] = $name;
		}
	}
}
$displayTemplate = $who;
if (!in_array($displayTemplate . '.html.php', $templates)) {
	$displayTemplate = null;
}

$contentFile = $contentdir . DIRECTORY_SEPARATOR . $entry;
if (!is_readable($contentFile)) {
	do_404();
	$title = 'Entry not found';
	$content = $title;
	render(null, compact('title', 'content'));
}

$content = file_get_contents($contentFile);
$title = escape(substr($entry, 0, strrpos($entry, '.')));

render($displayTemplate, compact('title', 'content'), false);

