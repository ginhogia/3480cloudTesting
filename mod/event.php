<?
$filePath = dirname(__FILE__);
require_once($filePath . "/db.php");

class Event
{
	private $id;
	private $club_id;
	private $date;
	private $topic;
	private $location;
	private $partner;
	private $note;
	private $racyear;
	private $eventType; //活動類型 0例會 1活動

	function __construct($data)
	{
		$this->id = intval($data["event_id"]);
		$this->club_id = intval($data["club_id"]);
		$this->date = $data["date"];
		$this->topic = $data["topic"];
		$this->location = $data["location"];
		$this->partner = $data["partner"];
		$this->note = $data["note"];
		$this->racyear = $data["racyear"];
		$this->eventType = $data["eventType"];
		$this->attendee = $data["attendee"];
		$this->absent = $data["absent"];
		$this->regforpersonal = $data["regforpersonal"]; //補出席
	}

	function getData()
	{
		$data = array();
		$data["id"] = $this->id;
		$data["club_id"] = $this->club_id;
		$data["date"] = $this->date;
		$data["topic"] = $this->topic;
		$data["location"] = $this->location;
		$data["partner"] = $this->partner;
		$data["note"] = $this->note;
		$data["racyear"]= $this->racyear;
		$data["eventType"]= $this->eventType;
		$data["attendee"] = $this->attendee;
		$data["absent"] = $this->absent;
		$data["regforpersonal"] = $this->regforpersonal;
		return $data;
	}

	function getId()
	{
		return $this->id;
	}

	function remove()
	{
		$db = new DB();
		$id = $this->id;
		$club_id = $this->club_id;
		$db->query("delete from event where event_id={$id}");
		$db->query("delete from event_attendance where event_id={$id} and club_id={$club_id}");
		return true;
	}

	function update($data)
	{
		$this->date = $data["date"];
		$this->topic = $data["topic"];
		$this->location = $data["location"];
		$this->partner = $data["partner"];
		$this->note = $data["note"];
		$this->racyear = $data["racyear"];
		$this->eventType = $data["eventType"];
		$db = new DB();
		$sql = "update `event` set `date`='{$this->date}',`racyear`='{$this->racyear}', `topic`='{$this->topic}', `location`='{$this->location}', `partner`='{$this->partner}', `note`='{$this->note}',`eventType`={$this->eventType} where `event_id`={$this->id};";
		//echo $sql;
		$db->query($sql);
		$this->saveAttendance($data);
		return true;
	}

	public static function getEventsByClub($club_id)
	{
		$meetings = array();
		if (!$club_id)
			return $meetings;

		$db = new DB();
		$db->query("select event_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, location, partner, note, eventType, racyear from event where club_id={$club_id}");
		while ($result = $db->fetch_array())
		{
			$events[] = new Event($result);
		}
		return $events;
	}
	
	public static function getEventsByClubAndRacyear($club_id,$racyear){
		$meetings = array();
		if (!$club_id)
			return $meetings;
		
		$db = new DB();
		$db->query("select event_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, location, partner, note,eventType,racyear from event where club_id={$club_id} and racyear='{$racyear}'");
		while ($result = $db->fetch_array())
		{
			$event = new Event($result);
			$event->getAttendance();
			$events[] = $event;
		}
		return $events;
	}

	public static function getEvent($event_id)
	{
		$db = new DB();
		$db->query("select event_id, club_id, DATE_FORMAT(date,'%Y/%m/%d') as date, topic, location, partner, note, eventType,racyear from event where event_id={$event_id}");
		$result = $db->fetch_array();
		if ($result){
			$event = new Event($result);
			$event->getAttendance();
			return $event;
		}
		else
			return null;
	}

	public static function create($data)
	{
		$club_id = intval($data["club_id"]);
		$date = $data["date"];
		$topic = $data["topic"];
		$location = $data["location"];
		$partner = $data["partner"];
		$note = $data["note"];
		$racyear = $data["racyear"];
		$eventType = $data["eventType"];

		$db = new DB();
		$db->query("insert into event(club_id, date, topic, location, partner, note, racyear,eventType) values({$club_id},'{$date}','{$topic}','{$location}','{$partner}','{$note}','{$racyear}','{$eventType}')");
		$data["event_id"] = $db->get_insert_id();
		$event = new Event($data);		
		$event->saveAttendance($data);
		return $event;
	}
	
	function getAttendance()
	{
		$id = $this->id;
		$club_id = $this->club_id;
		$db = new DB();
	
		$sql = "select user_id from event_attendance where club_id={$club_id} and event_id={$id} and attended=1";
		$db->query($sql);
		unset($this->attendee);
		$this->attendee = array();
		while ($result = $db->fetch_array())
		{
			$this->attendee[] = intval($result["user_id"]);
		}
		$sql = "select user_id from event_attendance where club_id={$club_id} and event_id={$id} and attended=0";
		$db->query($sql);
		unset($this->absent);
		$this->absent = array();
		while ($result = $db->fetch_array())
		{
			$this->absent[] = intval($result["user_id"]);
		}
		
		$sql = "select user_id from event_attendance where club_id={$club_id} and event_id={$id} and attended=2";
		$db->query($sql);
		unset($this->regforpersonal);
		$this->regforpersonal = array();
		while ($result = $db->fetch_array())
		{
			$this->regforpersonal[] = intval($result["user_id"]);
		}
		
	}
	
	function saveAttendance($data)
	{
		$this->attendee = $data["attendee"];
		$this->absent = $data["absent"];
		$this->regforpersonal = $data["regforpersonal"];
		
		
		$db = new DB();
		$db->query("delete from event_attendance where event_id={$this->id} and club_id={$this->club_id}");
		
		if (isset($this->attendee))
		{
			foreach ($this->attendee as $user_id)
			{
				$sql = "insert into event_attendance(club_id, event_id, user_id, attended) values({$this->club_id}, {$this->id}, {$user_id}, 1)";
				$db->query($sql);
				//echo $sql;
			}
		}
		
		if (isset($this->absent))
		{
			foreach ($this->absent as $user_id)
			{
				$sql = "insert into event_attendance(club_id, event_id, user_id, attended) values({$this->club_id}, {$this->id}, {$user_id}, 0)";
				$db->query($sql);
			}
		}
		if (isset($this->regforpersonal))
		{
			foreach ($this->regforpersonal as $user_id)
			{
				$sql = "insert into event_attendance(club_id, event_id, user_id, attended) values({$this->club_id}, {$this->id}, {$user_id}, 2)";
				$db->query($sql);
			}
		}
	}
}

class EventResource
{
	private $id;
	private $club_id;
	private $event_id;
	private $type;
	private $topic;
	private $last_update;
	private $fbid;
	private $original_name;
	private $racyear;

	function __construct($data)
	{
		$this->id = intval($data["resource_id"]);
		$this->club_id = intval($data["club_id"]);
		$this->event_id = intval($data["event_id"]);
		$this->type = intval($data["type"]);
		$this->topic = $data["topic"];
		$this->last_update = $data["last_update"];
		$this->fbid = $data["fbid"];
		$this->original_name = $data["original_name"];
		$this->racyear = $data["racyear"];
	}

	function getPath()
	{
		//return $_SERVER["DOCUMENT_ROOT"] . "/restrict/event_resource/" . $this->club_id . "/" . $this->event_id . "/" . $this->id;
		return dirname(__FILE__) . "/../restrict/event_resource/" . $this->club_id . "/" . $this->event_id . "/" . $this->id;
	}

	function isExist()
	{
		return file_exists($this->getPath());
	}

	function getSize()
	{
		if (!$this->isExist())
			return 0;
		return filesize($this->getPath());
	}

	function getOriginalName()
	{
		return $this->original_name;
	}

	function getData()
	{
		$data = array();
		$data["id"] = $this->id;
		$data["club_id"] = $this->club_id;
		$data["event_id"] = $this->event_id;
		$data["type"] = $this->type;
		$data["topic"] = $this->topic;
		$data["fbid"] = $this->fbid;
		$data["last_update"] = $this->last_update;
		$data["original_name"] = $this->original_name;
		$data["racyear"] = $this->racyear;
		return $data;
	}

	function getContent()
	{
		if ($this->isExist())
			return file_get_contents($this->getPath());
		else
			return "";
	}

	function isLink()
	{
		return $this->type == 3;
	}

	function isImage()
	{
		return $this->type == 2;
	}

	function getLink()
	{
		$link = $this->original_name;
		if (stripos($link, "http://") === 0 || stripos($link, "https://") === 0)
			return $link;
		else
			return "http://" . $link;
	}

	function setLastUpdate($last_update)
	{
		$this->last_update = $last_update;
	}

	function setFBId($fbid)
	{
		$this->fbid = $fbid;
	}

	function setOriginalName($original_name)
	{
		$this->original_name = $original_name;
	}

	function update($data)
	{
		$this->club_id = intval($data["club_id"]);
		$this->event_id = intval($data["event_id"]);
		$this->type = intval($data["type"]);
		$this->topic = $data["topic"];
		$this->last_update = $data["last_update"];
		$this->fbid = $data["fbid"];
		$this->original_name = $data["original_name"];
		$this->racyear = $data["racyear"];
		$resource_id = $this->id;
		$club_id = $this->club_id;
		$event_id = $this->event_id;
		$type = $this->type;
		$topic = $this->topic;
		$fbid = $this->fbid;
		$last_update = $this->last_update;
		$original_name = $this->original_name;
		$racyear = $this->racyear;
		$db = new DB();
		
		$sql = "update event_resource set topic='{$topic}', fbid='{$fbid}', last_update=FROM_UNIXTIME({$last_update}), original_name='{$original_name}' where resource_id={$resource_id}";
		if (!$db->query($sql))
			return false;
		return true;
	}

	function remove()
	{
		$db = new DB();
		$resource_id = $this->id;
		$db->query("delete from event_resource where resource_id={$resource_id}");
		return true;
	}

	public static function getResourcesByEvent($event_id)
	{
		$resources = array();
		if (!$event_id)
			return $resources;

		$db = new DB();
		$db->query("select resource_id, club_id, event_id, type, topic, fbid, UNIX_TIMESTAMP(last_update) as last_update, original_name from event_resource where event_id={$event_id}");
		while ($result = $db->fetch_array())
		{
			$resources[] = new EventResource($result);
		}
		return $resources;
	}

	public static function getResourceById($id)
	{
		if (!$id)
			return null;

		$db = new DB();
		$db->query("select resource_id, club_id, event_id, type, topic, fbid, UNIX_TIMESTAMP(last_update) as last_update, original_name from event_resource where resource_id={$id}");
		$data = $db->fetch_array();
		if ($data)
		{
			return new EventResource($data);
		}
		else
		{
			return null;
		}
	}

	public static function create($data)
	{
		$club_id = $data["club_id"];
		$event_id = $data["event_id"];
		$type = $data["type"];
		$topic = $data["topic"];
		$fbid = $data["fbid"];
		$last_update = $data["last_update"];
		$original_name = $data["original_name"];
		
		$db = new DB();
		$sql = "insert into event_resource(club_id, event_id, type, topic, fbid, last_update, original_name) values({$club_id}, {$event_id}, {$type}, '{$topic}', '{$fbid}', FROM_UNIXTIME({$last_update}), '{$original_name}')";

		if (!$db->query($sql))
			return null;
		$data["resource_id"] = $db->get_insert_id();
		$resource = new EventResource($data);
		return $resource;
	}
}
?>