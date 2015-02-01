<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/job.php");

$session = new Session();
$api = new Api($_GET);

$userID = $api->param("userID");


//$page = Page::getPageById(PAGE_ID_JOBQUERY, $api->getSession()->getClub()->getId());
//if (!$page->hasOwner($api->getSession()->getUser()))
 	//$api->returnPermissionDenied();

$career = career::getCareerByUserID($userID);


echo json_encode($career->getData());

?>