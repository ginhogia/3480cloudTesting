<?
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/event.php");
require_once("mod/racyear.php");

$session = new Session();
$session->setPageId(PAGE_ID_TIMELINE);
$session->checkPermission();

$racyear = Racyear::GetThisYear(); //'201314';
$year = $_GET["racyear"];
if ($year)
	$racyear = $year;

//$meetings = Meeting::getMeetingsByClub($session->getClub()->getId());
$events = Event::getEventsByClubAndRacyear($session->getClub()->getId(), $racyear);

$data = array();
foreach ($events as $event)
{
  $data[] = $event->getData();
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
<link href="http://code.jquery.com/ui/1.10.3/themes/flick/jquery-ui.css" rel="stylesheet" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
var EventList = function(element)
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
  		me.data.push(new Event(data[i]));
    me.refresh();
  };

  me.getEventById = function(id)
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
    var addEvent = function(event)
    {
      var $event = me.element.list.find("tr.template").clone();
      $event.removeClass("template");
      $event.attr("data-src", event.id);
      //$event.find("[data-ref='id']").text(event.id);
      $event.find("[data-ref='date']").text(event.date);
      $event.find("[data-ref='topic']").text(event.topic);
      $event.find("[data-ref='eventtype']").text(event.getTypeString());
      $event.find("[data-ref='location']").text(event.location);
      //$event.find("[data-ref='note']").text(event.note);
      $event.find("[data-ref='attendance-rate']").text(event.getAttendanceRate());
      if (event.eventType == "1"){
          $event.find("[data-ref='attendance-rate']").css("display","none");
          $event.find("[data-link='edit-attendance']").css("display","none");
      }

      //$event.find("[data-link='edit']").attr("href", "event.php?id=" + event.id + location.search.replace("?","&"));
      $event.find("[data-link='edit']").attr("href", "event.php?id=" + event.id);

//       $event.find("[data-link='edit']").click(function(e)
//       {
//       	var id = $(e.target).parents("tr").attr("data-src");
//         me.showEditor(me.getEventById(id));
//       });

      $event.find("[data-link='edit-attendance']").click(function(e)
      {
       var id = $(e.target).parents("tr").attr("data-src");
        me.showAttendanceEditor(me.getEventById(id));
      });

      me.element.list.append($event);
    };

    me.element.list.find("tr:not(.template)").remove();

    me.data.sort(function(a, b)
    {
      return new Date(a.date) - new Date(b.date);
    });
    for (var i = 0; i < me.data.length; ++i)
    {
      addEvent(me.data[i]);
    }
  };

//   var showEditorNew = function()
//   {
//   	var event = new Event();
//   	if (me.data.length > 0)
//   	{
//   		event.id = me.data[me.data.length - 1].id + 1;
//   	}
//   	me.data.push(event);
//   	me.showEditor(event);
//   };

  me.showAttendanceEditor = function(event)
  {
    me.element.attendance_editor.data("src", event.id);
    me.element.attendance_editor.find("[data-ref='id']").text(event.id);
    me.element.attendance_editor.find("[data-ref='topic']").text(event.topic);

    me.element.attendance_editor.find("li:not(.template)").remove();
    $template = me.element.attendance_editor.find("li.template");
    $container = me.element.attendance_editor.find("[data-ref='attendee']");
    for (var i = 0; i < event.attendee.length; ++i)
    {
      $item = $template.clone();
      $item.removeClass("template");
      $item.data("src", event.attendee[i]);
      var user = _r.session.club.getUserById(event.attendee[i]);
      if (!user)
        continue;

      $item.text(user.name);
      $container.append($item);
    }
    $container = me.element.attendance_editor.find("[data-ref='absent']");
    for (var i = 0; i < event.absent.length; ++i)
    {
      $item = $template.clone();
      $item.removeClass("template");
      $item.data("src", event.absent[i]);
      var user = _r.session.club.getUserById(event.absent[i]);
      if (!user)
        continue;

      $item.text(user.name);
      $container.append($item);
    }
    $container = me.element.attendance_editor.find("[data-ref='regforpersonal']");
    for (var i = 0; i < event.regforpersonal.length; ++i)
    {
      $item = $template.clone();
      $item.removeClass("template");
      $item.data("src", event.absent[i]);
      var user = _r.session.club.getUserById(event.regforpersonal[i]);
      if (!user)
        continue;

      $item.text(user.name);
      $container.append($item);
    }
    $container = me.element.attendance_editor.find("[data-ref='unreg']");
    var member = _r.session.club.member;
    for (var i = 0; i < member.length; ++i)
    {
      if (event.attendee.indexOf(member[i].id) >= 0 || event.absent.indexOf(member[i].id) >= 0)
        continue;

      $item = $template.clone();
      $item.removeClass("template");
      $item.data("src", member[i].id);
      $item.text(member[i].name);
      $container.append($item);      
    }

    var updateRate = function()
    {
      var attendee = me.element.attendance_editor.find("[data-ref='attendee'] li").length;
      var absent = me.element.attendance_editor.find("[data-ref='absent'] li").length;
      var regforpersonal = me.element.attendance_editor.find("[data-ref='regforpersonal'] li").length;
      var rate;
      if (attendee + absent + regforpersonal)
        rate = parseInt(attendee * 100.0 / (attendee + absent + regforpersonal)) + "%";
      else
        rate = "尚未登錄";
      me.element.attendance_editor.find("[data-ref='rate']").text(rate);
    }

    me.element.attendance_editor.find(".member-area").sortable({connectWith:".member-area", update: updateRate}).disableSelection();
    me.element.attendance_editor.modal();
    updateRate();
  };

  me.element.attendance_editor.find("[data-link='save']").click(function()
  {
    var id = me.element.attendance_editor.data("src");
    var event = me.getEventById(id);
    event.attendee = [];
    var $attendee = me.element.attendance_editor.find("[data-ref='attendee'] li");
    for (var i = 0; i < $attendee.length; ++i)
    {
      event.attendee.push($($attendee[i]).data("src"));
    }
    event.absent = [];
    var $absent = me.element.attendance_editor.find("[data-ref='absent'] li");
    for (var i = 0; i < $absent.length; ++i)
    {
      event.absent.push($($absent[i]).data("src"));
    }
    event.regforpersonal = [];
    var $regforpersonal = me.element.attendance_editor.find("[data-ref='regforpersonal'] li");
    for (var i = 0; i < $regforpersonal.length; ++i)
    {
      event.regforpersonal.push($($regforpersonal[i]).data("src"));
    }

    //event.save(id, function(result)
    event.save(function(result)
    {
      console.log(result);
      if (result.code != 0)
      {
        alert("啊，壞掉了！\n\n錯誤碼：" + result.code);
        return;
      }
      me.element.attendance_editor.modal('hide');
      me.refresh();
    });
  });

};

var ddlYear= function(element){
	var me = this;
	me.ddl = element;
	me.setData = function(){
		me.ddl.val('<?php echo $racyear ?>');
	};
	me.ddl.on('change' ,function(){
		var strUrl = "eventListNew.php?racyear=" + this.value;
		window.location.replace(strUrl);
		});
};

$(document).ready(function()
{
	var event_list = new EventList($("#page_content"));
	var ddl = new ddlYear($("#year"));
	  ddl.setData();
	event_list.setData(_r.data);
});
</script>
        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>活動紀錄</h1>
          <select id="year">
          	<option value="201314">2013-14</option>
          	<option value="201415">2014-15</option>
          	<option value="201516">2015-16</option>
          </select><br />
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
<!--                 <th>編號</th> -->
                <th>日期</th>
                <th>名稱</th>
                <th>類型</th>
                <th>地點</th>
                <th colspan="2">功能</th>
              </tr>
            </thead>
            <tbody>
              <tr class="template">
<!--                 <td data-ref="id"></td> -->
                <td data-ref="date"></td>
                <td data-ref="topic"></td>
                <td data-ref="eventtype"></td>                
                <td data-ref="location"></td>
                <td><a href="#" data-link="edit" data-visible=""><i class="icon-pencil"></i> 詳情</a></td>
<!--                 <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除</a></td> -->
                <td><a href="#" data-link="edit-attendance"><i class="icon-list-alt"></i> 出席登錄</a> <span data-ref="attendance-rate"></span></td>
                
              </tr>
            </tbody>
          </table>
          <p class="pull-right" data-visible="owner">
            <a id="create_meeting" class="btn btn-primary" href="event.php" data-link="add">新增</a>
          </p>
          <p class="clearfix"></p>

       

          <!-- begin modal -->
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="attendance-editor">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4>出席登錄：<span data-ref="topic"></span></h4>
            </div>
            <div class="modal-body">
            未登錄/免記出席
            <ul class="inline member-area well well-small" data-ref="unreg">
              <li class="template btn"></li>
            </ul>
            補出席但不計入團內出席
            <ul class="inline member-area well well-small" data-ref="regforpersonal">
            </ul>
            出席(含補出席且計入團內出席)
            <ul class="inline member-area well well-small" data-ref="attendee">
            </ul>
            未出席
            <ul class="inline member-area well well-small" data-ref="absent">
            </ul>
            </div>
            <div class="modal-footer">
              <div class="pull-left">
                出席率：<span data-ref="rate"></span>
              </div>
              <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
              <a href="#" class="btn btn-primary" data-visible="owner" data-link="save">確定</a>
            </div>
          </div>
          <!-- end modal -->

<? $builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
