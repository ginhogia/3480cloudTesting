<?
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/meeting.php");
require_once("mod/racyear.php");
require_once ("mod/club.php");

$session = new Session();
$session->setPageId(PAGE_ID_SCHEDULEQUERY);
$session->checkPermission();

$racyear = '201415';
$year = $_GET["racyear"];
$club_id = $_GET["club_id"];
if ($year)
	$racyear = $year;
if (!$club_id)
	$club_id = $session->getClub()->getId();
//$meetings = Meeting::getMeetingsByClub($session->getClub()->getId());
$meetings = Meeting::getMeetingsByClubAndRacyear($club_id, $racyear);

$data = array();
foreach ($meetings as $meeting)
{
  $data[] = $meeting->getData();
}

$builder = new PageBuilder($session, $data);
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="zh-TW">

<? $builder->outputHead(); ?>

  <body>
    <div id="fb-root"></div>

<? $builder->outputNavBar(); ?>

    <div class="container-fluid">
      <div class="row-fluid">

<? $builder->outputMenu(); ?>

<style>
.member-area
{
  min-height: 24px;
}
.member-area li
{
  margin: 2px;
}
.member-area li.template
{
  display: none;
}
</style>
<link href="http://code.jquery.com/ui/1.10.3/themes/flick/jquery-ui.css" rel="stylesheet">
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
var MeetingList = function(element)
{
  var me = this;
  me.element = {};
  me.element.root = $(element);
  me.element.list = me.element.root.find("tbody");
  me.element.editor = me.element.root.find("> [data-ref='editor']");
  me.element.attendance_editor = me.element.root.find("> [data-ref='attendance-editor']");
  me.data = {};

  me.setData = function(data)
  {
  	me.data = [];
  	for (var i = 0; i < data.length; ++i)
  		me.data.push(new Meeting(data[i]));
    me.refresh();
  };

  me.getMeetingById = function(id)
  {
  	id = parseInt(id);
    for (var i = 0; i < me.data.length; ++i)
    {
      if (me.data[i].id == id)
        return me.data[i];
    }
    return null;
  };

  me.refresh = function()
  {
    var addMeeting = function(meeting)
    {
      var $meeting = me.element.list.find("tr.template").clone();
      $meeting.removeClass("template");
      $meeting.attr("data-src", meeting.id);
      $meeting.find("[data-ref='id']").text(meeting.id);
      $meeting.find("[data-ref='date']").text(meeting.date);
      $meeting.find("[data-ref='topic']").text(meeting.topic);
      $meeting.find("[data-ref='type']").text(meeting.getTypeString());
      $meeting.find("[data-ref='location']").text(meeting.location);
      $meeting.find("[data-ref='note']").text(meeting.note);
      $meeting.find("[data-ref='reg_time']").text(meeting.reg_time);

      me.element.list.append($meeting);
    };

    me.element.list.find("tr:not(.template)").remove();

    me.data.sort(function(a, b)
    {
      return a.id - b.id;
    });
    for (var i = 0; i < me.data.length; ++i)
    {
      addMeeting(me.data[i]);
    }
  };

};



 



$(document).ready(function()
{
	var meeting_list = new MeetingList($("#page_content"));
	meeting_list.setData(_r.data);
	$("#selClub").change(function(){
		var url = "querySchedule.php?racyear=" + <?php echo Racyear::GetThisYear();?> + "&club_id=" + this.value;
		window.location.replace(url);
	});
	$("#selClub").val(<?php echo $club_id;?>);
});
</script>
        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1><?php echo Club::getClubNameById($club_id).' ';  echo Racyear::GetThisYear();?> 年度行事曆</h1>
          <?php PageBuilder::clubSelector();?>
          <br />
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>例會編號</th>
                <th>例會日期</th>
                <th>例會名稱</th>
                <th>例會分類</th>
                <th>註冊時間</th>
                <th>地點</th>
                
              </tr>
            </thead>
            <tbody>
              <tr class="template">
                <td data-ref="id"></td>
                <td data-ref="date"></td>
                <td data-ref="topic"></td>
                <td data-ref="type"></td>
                <td data-ref="reg_time"></td>
                <td data-ref="location"></td>
              
              </tr>
            </tbody>
          </table>
          
          <p class="clearfix"></p>

          



        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
