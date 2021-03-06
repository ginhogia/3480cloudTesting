<?

// require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/api.php");
// require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/page-id.php");
// require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/event.php");

$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/event.php");

$api = new Api($_POST);
$api->checkParameter("id");
$api->checkParameter("date");
$api->checkParameter("topic");
$api->checkParameter("location");
$api->checkParameter("partner");
$api->checkParameter("note");
$api->checkParameter("racyear");
$api->checkParameter("eventType");

$event_id = $api->param("id");

$page = Page::getPageById(PAGE_ID_TIMELINE, $api->getSession()->getClub()->getId());
if (!$page->hasOwner($api->getSession()->getUser()))
	$api->returnPermissionDenied();

$event = Event::getEvent($event_id);
$data = array();
$data["club_id"] = $api->getSession()->getClub()->getId();
$data["racyear"] = $api->param("racyear");
$data["date"] = $api->param("date");
$data["eventType"] = $api->param("eventType");
$data["topic"] = $api->param("topic");
$data["location"] = $api->param("location");
$data["partner"] = $api->param("partner");
$data["note"] = $api->param("note");
$data["attendee"] = $api->param("attendee");
$data["absent"] = $api->param("absent");
$data["regforpersonal"] = $api->param("regforpersonal");

if (is_null($event))
{
	$event = Event::create($data);
}
else
{
	$event->update($data);
}
$result = array();
$result["id"] = $event->getId();
$api->returnSuccess($result);
?>