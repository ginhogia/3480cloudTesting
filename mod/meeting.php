<?
$filePath = dirname(__FILE__);
require_once($filePath . "/db.php");

class Meeting
{
	private $id;
	private $club_id;
	private $date;
	private $topic;
	private $type;
	private $racyear;
	private $location;
	private $reg_time;
	private $note;

	function __construct($data)
	{
		$this->id = intval($data["meeting_id"]);
		$this->club_id = intval($data["club_id"]);
		$this->date = $data["date"];
		$this->topic = $data["topic"];
		$this->type = intval($data["type"]);
		$this->racyear = $data["racyear"];
		$this->attendee = $data["attendee"];
		$this->absent = $data["absent"];
		$this->location = $data["location"];
		$this->note = $data["note"];
		$this->reg_time = $data["reg_time"];
	}

	function getData()
	{
		$data = array();
		$data["id"] = $this->id;
		$data["club_id"] = $this->club_id;
		$data["date"] = $this->date;
		$data["topic"] = $this->topic;
		$data["type"] = $this->type;
		$data["racyear"] = $this->racyear;
		$data["attendee"] = $this->attendee;
		$data["absent"] = $this->absent;
		$data["location"]= $this->location;
		$data["note"] = $this->note;
		$data["reg_time"] = $this->reg_time;
		return $data;
	}

	function remove()
	{
		$db = new DB();
		$id = $this->id;
		$club_id = $this->club_id;
		$racyear = $this->racyear;
		$db->query("delete from meeting_schedule where meeting_id={$id} and club_id={$club_id} and racyear ={$racyear}");
		//$db->query("delete from meeting_attendance where meeting_id={$id} and club_id={$club_id}");
		return true;
	}

	function save($data)
	{
		$id = $data["id"];
		$this->fbid = $data["fbid"];
		$this->name = $data["name"];
		$this->title = $data["title"];
		$this->club_id = $data["club_id"];
		$this->attendee = $data["attendee"];
		$this->absent = $data["absent"];
		$this->racyear = $data["racyear"];
		$this->location = $data["location"];
		$this->note = $data["note"];
		$this->reg_time = $data["reg_time"];
		
		$db = new DB();
		$sql = "update `user` set `fbid`='{$this->fbid}', `title`='{$this->title}', `club_id`='{$this->club_id}', `name`='{$this->name}', `location`='{$this->location}' , `note`='{$this->note}', `racyear`='{$this->racyear}', `reg_time`='{$this->reg_time}' where `id`={$id};";
		$db->query($sql);
		//$this->saveAttendance();
		return true;
	}

	function getAttendance()
	{
		$id = $this->id;
		$club_id = $this->club_id;
		$db = new DB();

		$sql = "select user_id from meeting_attendance where club_id={$club_id} and meeting_id={$id} and attended=1";
		$db->query($sql);
		unset($this->attendee);
		$this->attendee = array();
		while ($result = $db->fetch_array())
		{
			$this->attendee[] = intval($result["user_id"]);
		}
		$sql = "select user_id from meeting_attendance where club_id={$club_id} and meeting_id={$id} and attended=0";
		$db->query($sql);
		unset($this->absent);
		$this->absent = array();
		while ($result = $db->fetch_array())
		{
			$this->absent[] = intval($result["user_id"]);
		}
	}

	function saveAttendance()
	{
		$db = new DB();
		$db->query("delete from meeting_attendance where meeting_id={$this->id} and club_id={$this->club_id}");
		if (isset($this->attendee))
		{
			foreach ($this->attendee as $user_id)
			{
				$sql = "insert into meeting_attendance(club_id, meeting_id, user_id, attended) values({$this->club_id}, {$this->id}, {$user_id}, 1)";
				$db->query($sql);
			}
		}
		if (isset($this->absent))
		{
			foreach ($this->absent as $user_id)
			{
				$sql = "insert into meeting_attendance(club_id, meeting_id, user_id, attended) values({$this->club_id}, {$this->id}, {$user_id}, 0)";
				$db->query($sql);
			}
		}
	}

	public static function getMeetingsByClub($club_id)
	{
		$meetings = array();
		if (!$club_id)
			return $meetings;

		$db = new DB();
		$db->query("select meeting_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, type from meeting_schedule where club_id={$club_id}");
		while ($result = $db->fetch_array())
		{
			$meeting = new Meeting($result);
			$meeting->getAttendance();
			$meetings[] = $meeting;
		}
		return $meetings;
	}
	
	public static function getMeetingsByClubAndRacyear($club_id,$racyear){
		$meetings = array();
		if (!$club_id)
			return $meetings;
		$db = new DB();
		$club_id = mysql_escape_string($club_id);
		$racyear = mysql_escape_string($racyear);
		$db->query("select meeting_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, type, location, reg_time, note,racyear from meeting_schedule where club_id={$club_id} and racyear={$racyear}");
		while ($result= $db->fetch_array()){
			$meeting = new Meeting($result);
			$meeting->getAttendance();
			$meetings[]=$meeting;
		}
		return $meetings;
	}
	

	public static function getMeetingByRacyear($club_id, $meeting_id,$racyear)
	{
		$db = new DB();
		$club_id = mysql_escape_string($club_id);
		$meeting_id = mysql_escape_string($meeting_id);
		$racyear = mysql_escape_string($racyear);
		$db->query("select meeting_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, type, location, reg_time, note,racyear from meeting_schedule where club_id={$club_id} and meeting_id={$meeting_id} and racyear={$racyear}");
		$result = $db->fetch_array();
		if ($result)
		{
			$meeting =  new Meeting($result);
			$meeting->getAttendance();
			return $meeting;
		}
		else
			return null;
	}

	public static function getMeeting($club_id, $meeting_id)
	{
		$db = new DB();
		$db->query("select meeting_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, type from meeting_schedule where club_id={$club_id} and meeting_id={$meeting_id}");
		$result = $db->fetch_array();
		if ($result)
		{
			$meeting =  new Meeting($result);
			$meeting->getAttendance();
			return $meeting;
		}
		else
			return null;
	}

	public static function create($data)
	{
		$id = intval($data["meeting_id"]);
		$club_id = intval($data["club_id"]);
		$racyear = $data["racyear"];
		$date = $data["date"];
		$topic = $data["topic"];
		$type = intval($data["type"]);
		$location = $data["location"];
		$note = $data["note"];
		$reg_time = $data["reg_time"];

		$db = new DB();
		$db->query("insert into meeting_schedule(meeting_id, club_id, date, topic, type, location, note, reg_time, racyear) values({$id},{$club_id},'{$date}','{$topic}',{$type},'{$location}','{$note}','{$reg_time}','{$racyear}')");
		$data["meeting_id"] = $id;
		$meeting = new Meeting($data);
		//$meeting->saveAttendance();
		return $meeting;
	}
}
?>