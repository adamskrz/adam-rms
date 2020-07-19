<?php
require_once __DIR__ . '/../apiHeadSecure.php';
header("Content-Type: text/plain");


if (!$AUTH->instancePermissionCheck(12) or !isset($_POST['position'])) die("404");

$DBLIB->where ('instancePositions_id', $bCMS->sanitizeString($_POST['position']));
$position = $DBLIB->getone("instancePositions");
$position['permissions'] = explode(",",$position['instancePositions_actions']);


if (isset($_POST['removepermission'])) {
	if(($key = array_search($_POST['removepermission'], $position['permissions'])) !== false) {
		unset($position['permissions'][$key]);
	} else die('2');
} elseif (isset($_POST['addpermission'])) {
	array_push($position['permissions'],$_POST['addpermission']);
}
asort($position['permissions']); //Prevents it being associative when downloaded
$DBLIB->where ('instancePositions_id', $bCMS->sanitizeString($_POST['position']));
if ($DBLIB->update ('instancePositions', ['instancePositions_actions' => implode(",",$position['permissions'])])) {
	$bCMS->auditLog("UPDATE", "instancePositions", $bCMS->sanitizeString($_POST['position']) . " - " . implode(",",$position['permissions']), $AUTH->data['users_userid']);
	die('1');
}
else die('2');
?>
