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
	
	public static function  getIndustriesArray(){
		$data = array();
		$temp = Industry::getIndustriesByCat("資訊科技");
		if ($temp)
		foreach ($temp as $Industry)
		{
			$data["indcat1"][] = $Industry->getData();
		}
		
		$temp = Industry::getIndustriesByCat("傳產/製造");
		if ($temp)
		foreach ($temp as $Industry)
		{
			$data["indcat2"][] = $Industry->getData();
		}
		$temp = Industry::getIndustriesByCat("工商服務");
		if ($temp)
		foreach ($temp as $Industry)
		{
			$data["indcat3"][] = $Industry->getData();
		}
		$temp = Industry::getIndustriesByCat("民生服務");
		if ($temp)
		foreach ($temp as $Industry)
		{
			$data["indcat4"][] = $Industry->getData();
		}
		$temp = Industry::getIndustriesByCat("文教/傳播");
		if ($temp)
		foreach ($temp as $Industry)
		{
			$data["indcat5"][] = $Industry->getData();
		}
		return $data;
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
	
	public static function getJobcatArray(){
		$data = array();
		$temp = jobcat::getJobcatsByCat("經管行銷");
		if ($temp)
		foreach ($temp as $jobcat)
		{
			$data["jobcat1"][] = $jobcat->getData();
		}
		$temp = jobcat::getJobcatsByCat("工程製造");
		if ($temp)
		foreach ($temp as $jobcat)
		{
			$data["jobcat2"][] = $jobcat->getData();
		}
		$temp = jobcat::getJobcatsByCat("文化創意");
		if ($temp)
		foreach ($temp as $jobcat)
		{
			$data["jobcat3"][] = $jobcat->getData();
		}
		$temp = jobcat::getJobcatsByCat("工商服務");
		if ($temp)
		foreach ($temp as $jobcat)
		{
			$data["jobcat4"][] = $jobcat->getData();
		}
		$temp = jobcat::getJobcatsByCat("其他專業");
		if ($temp)
		foreach ($temp as $jobcat)
		{
			$data["jobcat5"][] = $jobcat->getData();
		}
		return $data;
	}
}

?>