<?
$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/file.php");

//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/api.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/page-id.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/mod/file.php");
$api = new Api($_GET);
$api->checkParameter("file_id");
$api->checkParameter("racyear");

$page = Page::getPageById(PAGE_ID_PLAN, $api->getSession()->getClub()->getId());
if (!$page->hasOwner($api->getSession()->getUser()))
	$api->returnPermissionDenied();

$racyear = $api->param("racyear");
$file_id = $api->param("file_id");
$club_id = $api->getSession()->getClub()->getId();
$file = File::getFileById($file_id, $club_id, $racyear);
if (!$file)
{
	$api->returnCustomError(1, "File not found.");
}

if ($file->isExist() && !unlink($file->getPath()))
{
	$api->returnCustomError(2, "Cannot unlink file.");
}

if (!$file->remove())
{
	$api->returnCustomError(3, "Cannot remove DB record.");
}

$api->returnSuccess();
?>