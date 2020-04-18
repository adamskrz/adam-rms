<?php
require_once __DIR__ . '/../common/headSecure.php';

if (!$AUTH->instancePermissionCheck(20)) die("Sorry - you can't access this page");

$PAGEDATA['pageConfig'] = ["TITLE" => "Projects", "BREADCRUMB" => false];

if (isset($_GET['q'])) $PAGEDATA['search'] = $bCMS->sanitizeString($_GET['q']);
else $PAGEDATA['search'] = null;

if (isset($_GET['client'])) {
    $DBLIB->where("clients.clients_deleted", 0);
    $DBLIB->where("clients.instances_id", $AUTH->data['instance']['instances_id']);
    $DBLIB->where("clients.clients_id", $_GET['client']);
    $PAGEDATA['CLIENT'] = $DBLIB->getone("clients", ["clients_id", "clients_name"]);
    if ($PAGEDATA['CLIENT']) $PAGEDATA['pageConfig']['TITLE'] = $PAGEDATA['CLIENT']['clients_name'] . " Projects";
} else $PAGEDATA['CLIENT'] = false;

if (isset($_GET['page'])) $page = $bCMS->sanitizeString($_GET['page']);
else $page = 1;
$DBLIB->pageLimit = (isset($_GET['pageLimit']) ? $_GET['pageLimit'] : 30);
$DBLIB->where("projects.instances_id", $AUTH->data['instance']['instances_id']);
$DBLIB->where("projects.projects_deleted", 0);
if (strlen($PAGEDATA['search']) > 0) {
    //Search
    $DBLIB->where("(
		projects.projects_name LIKE '%" . $bCMS->sanitizeString($PAGEDATA['search']) . "%'
    )");
}
if ($PAGEDATA['CLIENT']) $DBLIB->where("projects.clients_id", $PAGEDATA['CLIENT']['clients_id']);
$DBLIB->join("clients", "projects.clients_id=clients.clients_id", "LEFT");
$DBLIB->join("users", "projects.projects_manager=users.users_userid", "LEFT");
$DBLIB->orderBy("projects.projects_archived", "ASC");
$DBLIB->orderBy("projects.projects_dates_deliver_start", "ASC");
$DBLIB->orderBy("projects.projects_name", "ASC");
$DBLIB->orderBy("projects.projects_created", "ASC");
$PAGEDATA['PROJECTSLIST'] = $DBLIB->arraybuilder()->paginate("projects", $page, ["projects_id", "projects_archived", "projects_name", "clients_name", "projects.clients_id", "projects_dates_deliver_start", "projects_dates_deliver_end","projects_dates_use_start", "projects_dates_use_end", "projects_status", "projects_manager", "users.users_name1", "users.users_name2", "users.users_email"]);
$PAGEDATA['pagination'] = ["page" => $page, "total" => $DBLIB->totalPages];

echo $TWIG->render('project/project_list.twig', $PAGEDATA);
?>