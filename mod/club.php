<?
$filePath = dirname(__FILE__);
require_once($filePath . "/db.php");
require_once($filePath . "/utils.php");
require_once($filePath . "/user.php");

define("CLUB_ID_NULL", -1);

class Club
{
  private $id;
  private $member;

  function __construct($data)
  {
    $this->id = intval($data["id"]);
    $this->name = $data["name"];
    $this->member = $data["member"];
  }

  function hasUser($fbid)
  {
    foreach ($this->member as $user)
    {
      if ($user->getFBId() == $fbid)
        return true;
    }
    return false;
  }

  function getId()
  {
    return $this->id;
  }

  function getName()
  {
    return $this->name;
  }

  function getData()
  {
    $data = array();
    $data["id"] = $this->id;
    $data["name"] = $this->name;
    $data["member"] = array();
    foreach ($this->member as $user)
    {
      $data["member"][] = $user->getData();
    }
    return $data;
  }

  public static function getClubNameById($id)
  {
    static $name_mapping = array();
    $name_mapping[1]= "台北西門團";
    $name_mapping[2]= "台北西區團";
    $name_mapping[3]= "中和團";
    $name_mapping[4]= "台北延平團";
    $name_mapping[5]= "台北艋舺團";
    $name_mapping[6]= "台北北門團";
    $name_mapping[7]= "台北文山團";
    $name_mapping[8]= "台北府門團";
    $name_mapping[9]= "永和團";
    $name_mapping[10]= "台北芙蓉團";
    $name_mapping[11]= "台北城中團";
    $name_mapping[12]= "台北城東團";
    $name_mapping[13]= "台北稻江團";
    $name_mapping[14]= "台北團";
    $name_mapping[15]= "翡翠團";
    $name_mapping[17]= "台北永福團";
    $name_mapping[19]= "台北大稻埕團";
    $name_mapping[21]= "台北北海團";
    $name_mapping[23]= "台北圓環團";
    $name_mapping[24]= "台北慈恩團";
    $name_mapping[25]= "台北大安團";
    $name_mapping[27]= "台北大龍峒團";
    $name_mapping[28]= "台北西北區團";
    $name_mapping[29]= "台北永平團";
    $name_mapping[31]= "台北百城團";
    $name_mapping[32]= "台北客家團";
    $name_mapping[33]= "台北東海團";
    $name_mapping[34]= "台北群英團";
    $name_mapping[35]= "金門團";
    $name_mapping[36]= "台北錫口團";
    $name_mapping[37]= "台北新世紀團";
    $name_mapping[38]= "台北圓滿團";
    $name_mapping[39]= "台北科大國際團";
    $name_mapping[40]= "台北上城團";
    $name_mapping[41]= "台北圓桌團";
    $name_mapping[42]= "台北保安團";
    return idx($name_mapping, $id, "");
  }
  
  public static function getClubNameList()
  {
  	static $name_mapping = array();
  	$name_mapping[1]= "台北西門團";
  	$name_mapping[2]= "台北西區團";
  	$name_mapping[3]= "中和團";
  	$name_mapping[4]= "台北延平團";
  	$name_mapping[5]= "台北艋舺團";
  	$name_mapping[6]= "台北北門團";
  	$name_mapping[7]= "台北文山團";
  	$name_mapping[8]= "台北府門團";
  	$name_mapping[9]= "永和團";
  	$name_mapping[10]= "台北芙蓉團";
  	$name_mapping[11]= "台北城中團";
  	$name_mapping[12]= "台北城東團";
  	$name_mapping[13]= "台北稻江團";
  	$name_mapping[14]= "台北團";
  	$name_mapping[15]= "翡翠團";
  	$name_mapping[17]= "台北永福團";
  	$name_mapping[19]= "台北大稻埕團";
  	$name_mapping[21]= "台北北海團";
  	$name_mapping[23]= "台北圓環團";
  	$name_mapping[24]= "台北慈恩團";
  	$name_mapping[25]= "台北大安團";
  	$name_mapping[27]= "台北大龍峒團";
  	$name_mapping[28]= "台北西北區團";
  	$name_mapping[29]= "台北永平團";
  	$name_mapping[31]= "台北百城團";
  	$name_mapping[32]= "台北客家團";
  	$name_mapping[33]= "台北東海團";
  	$name_mapping[34]= "台北群英團";
  	$name_mapping[35]= "金門團";
  	$name_mapping[36]= "台北錫口團";
  	$name_mapping[37]= "台北新世紀團";
  	$name_mapping[38]= "台北圓滿團";
  	$name_mapping[39]= "台北科大國際團";
  	$name_mapping[40]= "台北上城團";
  	$name_mapping[41]= "台北圓桌團";
  	$name_mapping[42]= "台北保安團";
  	return $name_mapping;
  }

  public static function NullClub()
  {
    $data = array();
    $data["id"] = CLUB_ID_NULL;
    $data["name"] = "";
    $data["member"] = array();
    return new Club($data);
  }

  public static function getClubById($id)
  {
    $data = array();
    $data["id"] = $id;
    $data["name"] = Club::getClubNameById($id);
    $data["member"] = array();

    $db = new DB();
    //$sql = "select * from user where club_id={$id} order by name";
    $sql = " SELECT id, fbid, name, club_id, title,background,birth_year, birth_month,note,gender,reg_date,exit_date,exit_reason
    ,   case isnull(userID)  WHEN '0' THEN 1 WHEN '1' THEN 0 END HasJobData
    FROM `rac-cloud`.user
    LEFT JOIN `rac-cloud`.user_career ON user.id = user_career.userID
    where club_id = '{$id}' order by name;";
    $db->query($sql);
    while ($result = $db->fetch_array())
    {
      $data["member"][] = new User($result);
    }
    return new Club($data);
  }
}
?>