<?php
require_once __DIR__ . '/../../apiHeadSecure.php';
require_once __DIR__ . '/data.php';

if (!$AUTH->instancePermissionCheck(134)) die("404");

if (!isset($_POST['table_name'])) finish(false, ["code" => "PARAM-ERROR", "message"=> "No data for action"]);

finish(true, null, $columns[$_POST['table_name']]);
?>