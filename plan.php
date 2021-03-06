<?
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/file.php");

$session = new Session();
$session->setPageId(PAGE_ID_PLAN);
$session->checkPermission();
$data = array();
$racyear = '201415';



$year = $_GET["racyear"];
if ($year)
	$racyear = $year;
  
  
for ($i = 1; $i <= 11; ++$i)
{
  $file = File::getFileById($i, $session->getClub()->getId(), $racyear);
  if ($file)
    $data[$i] = $file->getData();
  else
    $data[$i] = null;
}

$builder = new PageBuilder($session, $data);
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="zh-TW">

<? $builder->outputHead(); ?>

  <body>
    <div id="fb-root"></div>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="js/jquery.fileupload.js"></script>
    
<? $builder->outputNavBar(); ?>

    <div class="container-fluid">
      <div class="row-fluid">

<? $builder->outputMenu(); ?>

<style>
a.disabled
{
  color: gray;
  cursor: not-allowed;
  text-decoration: none;
}
a[data-link='upload']
{
  position: relative;
  overflow: hidden;
  display: inline-block;
}
input[type='file']
{
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
  opacity: 0;
  display: block;
  cursor: pointer;
}
</style>
<script>
var racyear = '<?php echo $racyear ?>';

var FileList = function(element)
{
  var me = this;
  me.element = {};
  me.element.root = element;
  me.element.list = element.find("#plan_list");
  me.data = {};

  me.setData = function(data)
  {
    me.data = data;
    me.refresh();
  };

  me.refresh = function()
  {
    for (var i = 1; i <= 11; ++i)
    {
      var $tr = me.element.list.find("tr[data-src=" + i + "]");
      var $date = $tr.find("[data-ref='date']");
      var $user = $tr.find("[data-ref='user']");
      var $download = $tr.find("[data-link='download']");
      var $remove = $tr.find("[data-link='remove']");
      if (me.data[i])
      {
        var user = _r.session.club.getUserByFBId(me.data[i].fbid);
        if (user)
          $user.text(user.name);
        else
          $user.text("未知的使用者 " + me.data[i].fbid);
        $date.text(new Date(me.data[i].last_update * 1000).toLocaleString());
        $download.removeClass("disabled");
        $remove.removeClass("disabled");
      }
      else
      {
        $user.text("尚未提交");
        $date.text("尚未提交");
        $download.addClass("disabled");
        $remove.addClass("disabled");
      }
    }
  };

  me.element.list.find("[data-link='download']").click(function(e)
  {
    if ($(e.target).hasClass("disabled"))
      return;
    var id = $(e.target).parents("tr[data-src]").attr("data-src");
    window.open("./api/downloadFile.php?file_id=" + id + location.search.replace("?", "&") + '&racyear=' + racyear);
  });

  me.element.list.find("[data-link='remove']").click(function(e)
  {
    if ($(e.target).hasClass("disabled"))
      return;
    var id = $(e.target).parents("tr[data-src]").attr("data-src");
    me.data[id] = null;
    $.get("./api/removeFile.php", {"file_id": id, "racyear": racyear}, function(result)
    {
      if (result.code == 0)
      {
        me.refresh();
      }
      else
      {
        alert("啊，壞掉了！\n\n錯誤碼：" + e.code);
      }
    }, "json");
  });

//   me.element.list.find("input[type='file']").click(function(){
// 	  alert("提交時間已過，明年請早");
//   });
  me.element.list.find("input[type='file']").fileupload({
    dataType: 'json',
    done: function (e, data) {
      if (data.result.code == 0)
      {
        var id = $(e.target).parents("tr[data-src]").attr("data-src");
        me.data[id] = {};
        me.data[id].last_update = new Date().getTime() / 1000;
        me.data[id].fbid = _r.session.user.fbid;
        me.refresh();
      }
      else
      {
        if (data.result.code == -1002)
        {
          alert("檔案大小超過限制（5MB）");
        }
        else
        {
          alert("啊，壞掉了！\n\n錯誤碼：" + data.result.code);
        }
      }
    }
  });

};

var ddlYear= function(element){
	var me = this;
	me.ddl = element;
	me.setData = function(){
		me.ddl.val('<?php echo $racyear ?>');
	};
	me.ddl.on('change' ,function(){
		var strUrl = "plan.php?racyear=" + this.value;
		window.location.replace(strUrl);
		});
};

$(document).ready(function()
{
	
  var file_list = new FileList($("#page_content"));
  var ddl = new ddlYear($("#year"));
  ddl.setData();
  file_list.setData(_r.data);
});
</script>
        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>服務計畫提交</h1>
          年度：
          <select id="year">
          	<option value="201314">2013-14</option>
          	<option value="201415">2014-15</option>
          	<option value="201516">2015-16</option>
          </select><br />
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>服務計畫</th>
                <th>更新日期</th>
                <th>更新使用者</th>
                <th colspan="3">功能</th>
              </tr>
            </thead>
            <tbody id="plan_list">
              <tr data-src="1">
                <td data-ref="name">團務服務計畫</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":1,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="2">
                <td data-ref="name">國際服務計畫</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":2,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="3">
                <td data-ref="name">職業服務計畫</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":3,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="4">
                <td data-ref="name">社區服務計畫</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":4,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="5">
                <td data-ref="name">公關服務計畫</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":5,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="6">
                <td data-ref="name">團長獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":6,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="7">
                <td data-ref="name">團務服務獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":7,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="8">
                <td data-ref="name">國際服務獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":8,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="9">
                <td data-ref="name">職業服務獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":9,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="10">
                <td data-ref="name">社區服務獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":10,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
              <tr data-src="11">
                <td data-ref="name">公關服務獎評</td>
                <td data-ref="date"></td>
                <td data-ref="user"></td>
                <td><a href="#" data-link="download"><i class="icon-download"></i> 下載</a></td>
                <td><a href="#" data-link="upload" data-visible="owner"><i class="icon-upload"></i> 上傳新版本<input type="file" name="file" data-url="./api/uploadFile.php" data-form-data='{"file_id":11,"racyear":"<?php echo $racyear?>"}'></a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除檔案</a></td>
              </tr>
            </tbody>
          </table>

<? $builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>