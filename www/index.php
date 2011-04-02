<?php
/**
 * Really simple blog collaboration system.
 * The code is nothing special (I whipped it up in an hour or so, one day), but
 * the app is really useful for collaboration/preview.
 *
 * Put user templates in ../templates/{username}
 * Put content in ../content/{username}/{Title Goes Here}.html
 */

/**
 * Helper constants
 */
define('USERDIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'content');
define('TEMPLATEDIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');

/**
 * Ensure we're in UTF-8.. Unicode is haaaaard.
 */
header('Content-type: text/html;charset=UTF-8');

/**
 * Simple "not found" helper
 */
function do_404()
{
	if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
		$prot = $_SERVER['SERVER_PROTOCOL'];
	} else {
		$prot = 'HTTP/1.0';
	}
	header("{$prot} 404 Not Found");
}

/**
 * Renders a template
 *
 * @param string $template the template name (path, but no suffix) to render
 * @param array $params parameters to pass into the template
 * @param bool $useDefault if true, wraps the $template in the default layout
 */
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

/**
 * Simple helper to escape output
 *
 * @param string $str the input string
 * @return string UTF-8, HTML-escaped string, ready for output
 */
function escape($str)
{
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Collect users
 */
$users = array();
foreach (new DirectoryIterator(USERDIR) as $fi) {
	if (!$fi->isDot()) {
		$users[] = $fi->getFilename();
	}
}

/**
 * $who will be the current user, if valid
 */
$who = null;

/**
 * $entry will be the currently-selected template, if valid and set
 */
$entry = null;

/**
 * Split out the path
 */
if (isset($_SERVER['PATH_INFO'])) {
	$parts = explode('/', $_SERVER['PATH_INFO']);
} elseif (isset($_SERVER['REQUEST_URI'])) {
	// massage the script name's directory out of the path
	$requestUri = $_SERVER['REQUEST_URI'];
	$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
	if (isset($_SERVER['SCRIPT_NAME']) && 0 === strpos($requestUri, $scriptDir)) {
		$requestUri = substr($requestUri, strlen($scriptDir) - 1);
	}
	$parts = explode('/', urldecode($requestUri));
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

/**
 * Ensure there are no path shenanigans
 */
if (false !== strpos($who, '../') || false !== strpos($entry, '../')) {
	do_404();
	$title = $content = 'Invalid path';
	render(null, compact('content', 'title'));
}

/**
 * Trap invalid users -> 404
 */
if ($who && !in_array($who, $users)) {
	do_404();
	$content = 'invalid user <a href="' . escape($_SERVER['SCRIPT_NAME']) . '">try again?</a>';
	$title = 'Invalid user';
	render(null, compact('content', 'title'));
}

/**
 * no user or entry; have the viewer select a user
 */
if (!$who) {
	$title = 'Select a user';
	render('_users', compact('title', 'users'));
}

/**
 * visitor has selected a user, but not an entry
 */
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

/**
 * collect templates
 */
$templates = array();
foreach (new DirectoryIterator(TEMPLATEDIR) as $fi) {
	if (!$fi->isDot()) {
		$name = $fi->getFilename();
		if ($name[0] != '_') {
			$templates[] = $name;
		}
	}
}
/**
 * determine display template
 */
$displayTemplate = $who;
if (!in_array($displayTemplate . '.html.php', $templates)) {
	$displayTemplate = null;
}

/**
 * validate requested content
 */
$contentFile = $contentdir . DIRECTORY_SEPARATOR . $entry;
if (!is_readable($contentFile)) {
	do_404();
	$title = 'Entry not found';
	$content = $title;
	render(null, compact('title', 'content'));
}

/**
 * actually load content from disk (the repository)
 *
 * Note: this content is often intentionally not escaped (depends on the
 * template), so it opens the door to XSS and other nastiness. The assumption
 * here is that authors (committers) are to be trusted; visitors are not
 */
$content = file_get_contents($contentFile);
/**
 * Set title from the file name
 */
$title = escape(substr($entry, 0, strrpos($entry, '.')));

/**
 * and finally, render the template
 */
render($displayTemplate, compact('title', 'content'), false);

