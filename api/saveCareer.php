<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/job.php");

$api = new Api($_POST);
//$api->checkParameter("original_id");
$api->checkParameter("id");
$api->checkParameter("industry");
$api->checkParameter("jobcat");
$api->checkParameter("company");
$api->checkParameter("jobtitle");
$api->checkParameter("otherData");

$meeting_id = $api->param("original_id");
$userID = $api->param("id");

// $page = Page::getPageById(PAGE_ID_SCHEDULE, $api->getSession()->getClub()->getId());
// if (!$page->hasOwner($api->getSession()->getUser()))
// 	$api->returnPermissionDenied();



$career = Career::getCareerByUserID($userID);
// if (!is_null($career))
// 	$career->remove();


$data = array();
$data["id"] = $api->param("id");
$data["opendata"] = $api->param("opendata");
$data["industry"] = $api->param("industry");
$data["jobcat"] = $api->param("jobcat");
$data["company"] = $api->param("company");
$data["jobtitle"] = $api->param("jobtitle");
$data["otherData"] = $api->param("otherData");

if (is_null($career))
{
	$career = career::create($data);
}
else
{
	$career->update($data);
}


$api->returnSuccess();
?>