<?

$filePath = dirname(__FILE__);
require_once($filePath . "/../mod/api.php");
require_once($filePath . "/../mod/page-id.php");
require_once($filePath . "/../mod/file.php");
$api = new Api($_GET);
$api->checkParameter("file_id");

$file_id = $api->param("file_id");
$racyear = $api->param("racyear");
$club_id = $api->getSession()->getClub()->getId();
if (!$file_id || $club_id < 0)
{
	header("HTTP/1.0 404 Not Found");
	exit(0);
}

$file = File::getFileById($file_id, $club_id,$racyear);
if (!$file)
{
	header("HTTP/1.0 404 Not Found");
	exit(0);
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . $file->getOriginalName());
header("Content-Transfer-Encoding: binary");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . $file->getSize());
ob_clean();
flush();
echo $file->getContent();
?>