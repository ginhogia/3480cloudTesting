<?

$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/meeting.php");

//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/api.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/page-id.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/meeting.php");

$api = new Api($_POST);
$api->checkParameter("id");
$api->checkParameter("racyear");


$meeting_id = $api->param("id");
$racyear = $api->param("racyear");

$page = Page::getPageById(PAGE_ID_SCHEDULE, $api->getSession()->getClub()->getId());
if (!$page->hasOwner($api->getSession()->getUser()))
	$api->returnPermissionDenied();

$meeting = Meeting::getMeetingByRacyear($api->getSession()->getClub()->getId(), $meeting_id,$racyear);
if (is_null($meeting))
	$api->returnCustomError(1, "Meeting id does not exist.");
if (!$meeting->remove())
	$api->returnCustomError(2, "Unable to remove meeting.");
$api->returnSuccess();
?>