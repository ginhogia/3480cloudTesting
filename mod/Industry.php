<?php

class industry{
	private $id;
	private $name;
	
	function __construct($data){
		$this->id = intval($data["IndID"]);
		$this->name = $data["Name"];	
	}
	
	function getData(){
		$data = array();
		$data["id"] = $this->id;
		$data["name"] = $this->name;
		return $data;
	}
	
	public static function getIndustriesByCat($catName){
		$db = new DB();
		$db->query("select IndID,  Name from career_industry where Catagory='{$catName}'");
		while ($result = $db->fetch_array())
		{
			$inds[] = new industry($result);
		}
		return $inds;
	}
	
	
}


class jobcat{
	private $id;
	private $name;
	
	function __construct($data){
		$this->id = intval($data["JobID"]);
		$this->name = $data["Name"];
	}
	
	function getData(){
		$data = array();
		$data["id"] = $this->id;
		$data["name"] = $this->name;
		return $data;
	}
	
	public static function getJobcatsByCat($catName){
		$db = new DB();
		$db->query("select JobID,  Name from career_jobcat where Catagory='{$catName}'");
		while ($result = $db->fetch_array())
		{
			$inds[] = new jobcat($result);
		}
		return $inds;
	}
}

?>