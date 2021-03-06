<?php
$filePath = dirname(__FILE__);
require_once($filePath . "/db.php");

class career{
	
	private $id;
	private $name;
	private $club_id;
	private $opendata; //是否公開查詢
	private $industry; //產業
	private $industrystring;
	private $jobcat; //職務
	private $jobcatstring;
	private $company;
	private $jobtitle; //職位
	private $keyword; //TODO: 專長
	private $otherData; //自我介紹
	
	function __construct($data){
		$this->id = intval($data["userID"]);
		$this->club_id = $data["club_id"];
		$this->opendata = $data["opendata"];
		$this->name = $data["name"];
		$this->industry = intval($data["industry"]);
		$this->industrystring = $data["industrystring"];
		$this->jobcat = intval($data["jobcat"]);
		$this->jobcatstring = $data["jobcatstring"];
		$this->company = $data["company"];
		$this->jobtitle = $data["jobtitle"];
		$this->keyword = $data["keyword"];
		$this->otherData = $data["otherData"];		
		
	}
	public static function CreateCareerByUserID($userID){
		$data = array();
		$data["userID"] = $userID;
		$career = new Career($data);
		return $career;
	}
	
	public static function getCareerFinishData(){
		$sql ="select a.club_id, count(a.club_id) as totalNum, IFNULL(b.done,0) as done, IFNULL(b.showoff,0) as showoff from user a
		left join (select club_id, count(club_id) as done, sum(opendata) as showoff from user_career
		inner join user on user_career.userID = user.id group by club_id) b
		on a.club_id = b.club_id
		group by club_id ";
		$db = new DB();
		$db->query($sql);
		$outputs[] = array();
		while($result = $db->fetch_array()){
			$outputs[] = $result;
		}
		return $outputs;
	}
	
	public static function getCareersByClub($clubID,$isShowAll){
		$clubID = mysql_escape_string($clubID);
		$open = " AND user_career.opendata=1";
		if ($isShowAll)
			$open = " ";
		$sql ="SELECT user_career.userID, user.club_id,user.name, user_career.industry, user_career.jobcat
		,user_career.jobtitle, user_career.otherData , career_industry.Name as industrystring, career_jobcat.Name as jobcatstring
		,user_career.opendata, company
		FROM `rac-cloud`.user_career
		inner join `rac-cloud`.user on userID = id
		INNER JOIN `rac-cloud`.career_industry  on industry = IndID
		INNER JOIN `rac-cloud`.career_jobcat  on jobcat = jobID
		WHERE user.club_id='{$clubID}' " . $open;
		
		//echo $sql;
		$db = new DB();
		$db->query($sql);
		$careers[] = array();
		while ($result= $db->fetch_array()){
		$career = new Career($result);
		$careers[]=$career;
		}
		
		return $careers;
	}
	
	public static function getCareersByIndustry($industryID,$isShowAll){
		$industryID = mysql_escape_string($industryID);
		$open = " AND user_career.opendata=1";
		if ($isShowAll)
			$open = " ";
		$sql ="SELECT user_career.userID, user.club_id,user.name, user_career.industry, user_career.jobcat
		,user_career.jobtitle, user_career.otherData , career_industry.Name as industrystring, career_jobcat.Name as jobcatstring
		,user_career.opendata, company
		FROM `rac-cloud`.user_career
		inner join `rac-cloud`.user on userID = id
		INNER JOIN `rac-cloud`.career_industry  on industry = IndID
		INNER JOIN `rac-cloud`.career_jobcat  on jobcat = jobID
		WHERE industry='{$industryID}' " . $open;
		//echo $sql;
		$db = new DB();
		$db->query($sql);
		$careers[] = array();
		while ($result= $db->fetch_array()){
			$career = new Career($result);
			$careers[]=$career;
		}
		
		return $careers;
	}
	
	public static function getCareersByJobcat($jobcatID,$isShowAll){
		$jobcatID = mysql_escape_string($jobcatID);
		$open = " AND user_career.opendata=1";
		if ($isShowAll)
			$open = " ";
		$sql ="SELECT user_career.userID, user.club_id,user.name, user_career.industry, user_career.jobcat
		,user_career.jobtitle, user_career.otherData , career_industry.Name as industrystring, career_jobcat.Name as jobcatstring
		,user_career.opendata, company
		FROM `rac-cloud`.user_career
		inner join `rac-cloud`.user on userID = id
		INNER JOIN `rac-cloud`.career_industry  on industry = IndID
		INNER JOIN `rac-cloud`.career_jobcat  on jobcat = jobID
		WHERE jobcat='{$jobcatID}' " . $open;
		//echo $sql;
		$db = new DB();
		$db->query($sql);
		$careers[] = array();
		while ($result= $db->fetch_array()){
			$career = new Career($result);
			$careers[]=$career;
		}
		
		return $careers;
	}
	
	public static function getCareerByUserID($userID){
		$userID = mysql_escape_string($userID);
		$sql ="SELECT user_career.userID, user.club_id,user.name, user_career.industry, user_career.jobcat 
				,user_career.jobtitle, user_career.otherData , career_industry.Name as industrystring, career_jobcat.Name as jobcatstring 
				,user_career.opendata, company 
				FROM `rac-cloud`.user_career 
				inner join `rac-cloud`.user on userID = id 
				INNER JOIN `rac-cloud`.career_industry  on industry = IndID 
				INNER JOIN `rac-cloud`.career_jobcat  on jobcat = jobID 
				WHERE id='{$userID}'";
		
		$db = new DB();
		//echo $sql;
		$db->query($sql);
		$result = $db->fetch_array();
		if ($result){
			$career = new Career($result);
			return $career;
		}
		else{
			return null; 
		}
		
	}
	
	function getData(){
		$data = array();
		$data["id"] = $this->id;
		$data["club_id"] = $this->club_id;
		$data["opendata"] = $this->opendata;
		$data["name"] = $this->name;
		$data["industry"] = $this->industry;
		$data["industrystring"] = $this->industrystring;
		$data["jobcat"] = $this->jobcat;
		$data["jobcatstring"] = $this->jobcatstring;
		$data["company"] = $this->company;
		$data["jobtitle"] = $this->jobtitle;
		$data["otherData"] = $this->otherData;
		
		return $data;
		
	}
	
	function update($data){
		
		$this->opendata = $data["opendata"];
		$this->industry = $data["industry"];
		$this->jobcat = $data["jobcat"];
		$this->company = $data["company"];
		$this->jobtitle = $data["jobtitle"];
		$this->otherData = $data["otherData"];
		
		$db = new DB();
		$sql = "update `user_career` set `industry`='{$this->industry}', `opendata`='{$this->opendata}',`jobcat`='{$this->jobcat}', `company`='{$this->company}',
		 `jobtitle`='{$this->jobtitle}', `otherData`='{$this->otherData}' where `userID`={$this->id};";
		//echo $sql;
		$db->query($sql);
		return true;
	}
	function remove(){
		$db = new DB();
		$id = $this->id;
		$db->query("delete from user_career where userID={$id}");
		return true;
	}
	function create($data){
		$userID = intval($data["id"]);
		$opendata = $data["opendata"];
		$industry = $data["industry"];
		$jobcat = $data["jobcat"];
		$company = $data["company"];
		$jobtitle = $data["jobtitle"];
		$otherData = $data["otherData"];
		
		
		$db = new DB();
		$sql = "insert into user_career(userid, opendata, industry, jobcat, company, jobtitle, otherData) values('{$userID}','{$opendata}','{$industry}','{$jobcat}','{$company}','{$jobtitle}','{$otherData}')";
		//echo $sql;
		$db->query($sql);
		$data["userID"] = $db->get_insert_id();
		$event = new career($data);
		return $event;
	}
	
}



?>