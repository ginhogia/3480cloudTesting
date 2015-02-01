<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/job.php");

$api = new Api($_GET);
$session = new Session();
$jobcatID = $api->param("jobcatID");


// $page = Page::getPageById(PAGE_ID_JOBQUERY, $api->getSession()->getClub()->getId());
// if (!$page->hasOwner($api->getSession()->getUser()))
// 	$api->returnPermissionDenied();

//$careers = career::getCareersByJobcat($jobcatID);
if ($session->getUser()->isDistrictTeam())
	$careers = career::getCareersByJobcat($jobcatID,true);
else
	$careers = career::getCareersByJobcat($jobcatID,false);


$data = array();
foreach ($careers as $career)
{
	//$data[] = $career->getData();
	if ($career != $careers[0])
	$data[] = $career->getData();
}

echo json_encode($data);

?>