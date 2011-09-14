<?php
/**
 * Blogcollab functions/library code
 */

/**
 * Helper constants
 */
define('USERDIR', __DIR__ . DIRECTORY_SEPARATOR . 'content');
define('TEMPLATEDIR', __DIR__ . DIRECTORY_SEPARATOR . 'templates');

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
