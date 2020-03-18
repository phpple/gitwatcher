<?php
/**
 * bootstrap for phpunit
 * @author: ronnie
 * @since: 2020/2/22 11:53 pm
 * @copyright: 2020@100tal.com
 * @filesource: bootstrap.php
 */
define('SITE_ROOT', dirname(__DIR__));
define('REPO_ROOT', SITE_ROOT.'/example/git/');

require_once SITE_ROOT.'/vendor/autoload.php';