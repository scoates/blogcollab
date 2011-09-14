<?php
/**
 * Really simple blog collaboration system.
 * The code is nothing special (I whipped it up in an hour or so, one day, and
 * spent a whole bunch more time making it ready for public consumption), but
 * the app is really useful for collaboration/preview.
 *
 * Put user templates in ../templates/{username}
 * Put content in ../content/{username}/{Title Goes Here}.html
 */

/**
 * Authentication constants
 */
define('AUTH_USER', 'collab'); // set to false to skip auth check
define('AUTH_PASS', 'd5029374377771fd628239fd1f4e9d02'); // md5('collab');

$AUTHENTICATED = false;

/**
 * Library code
 */
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'blogcollab.php';

/**
 * Check auth
 */
if (AUTH_USER) {
	if (
		isset($_SERVER['PHP_AUTH_USER'])
		&& AUTH_USER == $_SERVER['PHP_AUTH_USER']
		&& isset($_SERVER['PHP_AUTH_PW'])
		&& AUTH_PASS == md5($_SERVER['PHP_AUTH_PW'])
	) {
		$AUTHENTICATED = true;
	} else {
		$AUTHENTICATED = false;
		if (isset($_GET['dologin']) && $_GET['dologin']) {
			header('WWW-Authenticate: Basic realm="Blogcollab"');
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}
} else {
	// AUTH_USER is false, pretend we're authenticated
	$AUTHENTICATED = true;
}

/**
 * Ensure we're in UTF-8.. Unicode is haaaaard.
 */
header('Content-type: text/html;charset=UTF-8');

/**
 * When not authenticated, show the instructions
 */
if (!$AUTHENTICATED) {
	render('_instructions', array());
	exit();
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
	// apache
	$parts = explode('/', preg_replace('/\?.*$/', '', $_SERVER['PATH_INFO']));
} elseif (isset($_SERVER['REQUEST_URI'])) {
	// nginx (orchestra.io)
	// massage the script name's directory out of the path
	var_dump($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']); // debugging orchestra
	$requestUri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
	$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
	if (isset($_SERVER['SCRIPT_NAME']) && 0 === strpos($requestUri, $scriptDir)) {
		$requestUri = substr($requestUri, strlen($scriptDir));
	}
	$parts = explode('/', urldecode($requestUri));
	var_dump($parts);
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

