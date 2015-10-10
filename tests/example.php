<?php

use Betteryourweb\Database\DB;

require_once __DIR__."/../bootstrap/autoload.php";

/**
 * The base directory for the project,
 * makes for a good starting point to
 * use absolute paths
 */

define('BASE_DIR', __DIR__.'/..');

/**
 * Definitions for the database
 */

define('DB_USER', 'root');
define('DB_PASS', 'devrootpass');
define('DB_HOST', 'localhost');
define('DB_DATABASE', 'josh_ubek');
define('DB_DRIVER', 'mysql');



$db = new DB();
$fname = "Jared";
$db->table = "customer";
$fields = "*";

dump( $db->query("SELECT $fields FROM $db->table ")->where('fname','=',$fname)->get() );