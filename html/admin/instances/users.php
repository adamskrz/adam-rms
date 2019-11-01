<?php
require_once __DIR__ . '/../common/headSecure.php';

$PAGEDATA['pageConfig'] = ["TITLE" => "Users in Business", "BREADCRUMB" => false];

if (!$AUTH->instancePermissionCheck(2)) die("Sorry - you can't access this page");

if (isset($_GET['q'])) $PAGEDATA['search'] = $bCMS->sanitizeString($_GET['q']);
else $PAGEDATA['search'] = null;

if (isset($_GET['page'])) $page = $bCMS->sanitizeString($_GET['page']);
else $page = 1;
$DBLIB->pageLimit = 20; //Users per page
$DBLIB->orderBy("users.users_name1", "ASC");
$DBLIB->orderBy("users.users_name2", "ASC");
$DBLIB->orderBy("users.users_created", "ASC");
$DBLIB->where("users_deleted", 0);
if (strlen($PAGEDATA['search']) > 0) {
	//Search
	$DBLIB->where("(
		users_username LIKE '%" . $PAGEDATA['search'] . "%'
		OR users_name1 LIKE '%" . $PAGEDATA['search'] . "%'
		OR users_name2 LIKE '%" . $PAGEDATA['search'] . "%'
		OR users_email LIKE '%" . $PAGEDATA['search'] . "%'
    )");
}
//if (!isset($_GET['suspended'])) $DBLIB->where ("users.users_suspended", "0");

$DBLIB->join("userInstances", "users.users_userid=userInstances.users_userid","LEFT");
$DBLIB->join("instancePositions", "userInstances.instancePositions_id=instancePositions.instancePositions_id","LEFT");
$DBLIB->where("instances_id",  $AUTH->data['instance']['instances_id']);
$DBLIB->where("userInstances.userInstances_deleted",  0);
$users = $DBLIB->arraybuilder()->paginate('users', $page, ["users.users_username", "users.users_name1", "users.users_name2", "users.users_userid", "users.users_email", "users.users_emailVerified", "users.users_suspended","users.users_suspended", "instancePositions.instancePositions_displayName","userInstances.userInstances_label", "userInstances.userInstances_id", "userInstances.instancePositions_id"]);
$PAGEDATA['pagination'] = ["page" => $page, "total" => $DBLIB->totalPages];
foreach ($users as $user) {
	$PAGEDATA["users"][] = $user;
}


$DBLIB->orderBy("instancePositions_rank", "ASC");
$DBLIB->where("instances_id", $AUTH->data['instance']['instances_id']);
$PAGEDATA['positions'] = $DBLIB->get("instancePositions", null, ["instancePositions_id", "instancePositions_displayName"]);



echo $TWIG->render('instances/users.twig', $PAGEDATA);
?>
