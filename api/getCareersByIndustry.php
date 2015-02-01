<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/job.php");

$api = new Api($_GET);
$session = new Session();
$industryID = $api->param("industryID");


// $page = Page::getPageById(PAGE_ID_JOBQUERY, $api->getSession()->getClub()->getId());
// if (!$page->hasOwner($api->getSession()->getUser()))
// 	$api->returnPermissionDenied();

//$careers = career::getCareersByIndustry($industryID);
if ($session->getUser()->isDistrictTeam())
	$careers = career::getCareersByIndustry($industryID,true);
else
	$careers = career::getCareersByIndustry($industryID,false);


//echo count($careers);
$data = array();
foreach ($careers as $career)
{
	if ($career != $careers[0])
	$data[] = $career->getData();
}

echo json_encode($data);

?>