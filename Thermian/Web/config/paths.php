<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       MIT License (https://opensource.org/licenses/mit-license.php)
 */

/*
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/*
 * These define should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */

/*
 * The full path to the directory which holds "src", WITHOUT a trailing DS.
 */
define("ROOT", dirname(__DIR__));

/*
 * The actual directory name for the application directory. Normally
 * named 'src'.
 */
const APP_DIR = 'src';

/*
 * Path to the application's directory.
 */
const APP = ROOT . DS . APP_DIR . DS;

/*
 * Path to the config directory.
 */
const CONFIG = ROOT . DS . 'config' . DS;

/*
 * File path to the webroot directory.
 */
const WWW_ROOT = ROOT . DS . 'webroot' . DS;

/*
 * Path to the tests directory.
 */
const TESTS = ROOT . DS . 'tests' . DS;

/*
 * Path to the temporary files directory.
 */
const TMP = ROOT . DS . 'tmp' . DS;

/*
 * Path to the logs directory.
 */
const LOGS = ROOT . DS . 'logs' . DS;

/*
 * Path to the cache files directory. It can be shared between hosts in a multi-server setup.
 */
const CACHE = TMP . 'cache' . DS;

/*
 * Path to the resources directory.
 */
const RESOURCES = ROOT . DS . 'resources' . DS;

/*
 * Path to the plugins directory
 */
const PLUGINS = ROOT . DS . 'plugins' . DS;

/*
 * The absolute path to the project root directory.
 */
define("PROJECT_ROOT", dirname(__DIR__, 3));

/*
 * The absolute path to the .env file.
 */
const ENV_PATH = PROJECT_ROOT . DS . '.env';

/*
 * The absolute path to the vendor folder
 */
const VENDOR = PROJECT_ROOT . DS . 'vendor';

/*
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 */
const CAKE_CORE_INCLUDE_PATH = VENDOR . DS . 'cakephp' . DS . 'cakephp';

/*
 * Path to the cake directory.
 */
const CORE_PATH = CAKE_CORE_INCLUDE_PATH . DS;
const CAKE = CORE_PATH . 'src' . DS;
