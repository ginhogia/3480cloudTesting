<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/meeting.php");

$api = new Api($_POST);
$api->checkParameter("original_id");
$api->checkParameter("id");
$api->checkParameter("date");
$api->checkParameter("topic");
$api->checkParameter("type");
$api->checkParameter("location");
$api->checkParameter("reg_time");
$api->checkParameter("racyear");
$api->checkParameter("note");

$meeting_id = $api->param("original_id");
$racyear = $api->param("racyear");

$page = Page::getPageById(PAGE_ID_SCHEDULE, $api->getSession()->getClub()->getId());
if (!$page->hasOwner($api->getSession()->getUser()))
	$api->returnPermissionDenied();

$meeting = Meeting::getMeetingByRacyear($api->getSession()->getClub()->getId(), $meeting_id, $racyear);
if (!is_null($meeting))
	$meeting->remove();

$data = array();
$data["meeting_id"] = $api->param("id");
$data["club_id"] = $api->getSession()->getClub()->getId();
$data["date"] = $api->param("date");
$data["topic"] = $api->param("topic");
$data["type"] = $api->param("type");
$data["attendee"] = $api->param("attendee");
$data["absent"] = $api->param("absent");
$data["location"] = $api->param("location");
$data["note"]= $api->param("note");
$data["reg_time"] = $api->param("reg_time");
$data["racyear"] = $api->param("racyear");

$meeting = Meeting::create($data);

$api->returnSuccess();
?>