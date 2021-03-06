<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
$api = new Api($_POST);
$api->checkParameter("name");
$api->checkParameter("title");
$api->checkParameter("fbid");
$api->checkParameter("club_id");

$data = $_POST;

$page = Page::getPageById(PAGE_ID_MEMBER, $api->getSession()->getClub()->getId());
if (!$page->hasOwner($api->getSession()->getUser()))
	$api->returnPermissionDenied();

$user = User::create($data);
$result = array();
$result["id"] = $user->getId();
$api->returnSuccess($result);
?>