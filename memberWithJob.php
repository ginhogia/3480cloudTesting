<?
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/job.php");
require_once("mod/Industry.php");
$session = new Session();
$session->setPageId(PAGE_ID_MEMBER);
$session->checkPermission();

$data["inds"] = industry::getIndustriesArray();
$data["jobs"] = jobcat::getJobcatArray();
$builder = new PageBuilder($session,$data);
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="zh-TW">

<? $builder->outputHead(); ?>
<script src="js/jquery.form.min.js"></script>
<script src="js/occupation.js"></script>
  <body>
    <div id="fb-root"></div>

<? $builder->outputNavBar(); ?>

    <div class="container-fluid">
      <div class="row-fluid">

<? $builder->outputMenu(); ?>




<script>
function MemberList(element)
{
  var me = this;
  me.element = {};
  me.element.root = $(element);
  me.element.list = me.element.root.find("#memeberListtBody");
  me.element.editor = me.element.root.find(">[data-ref='editor']");
  me.element.editorJob = me.element.root.find(">[data-ref='editor_job']");
  me.element.indeditor = me.element.root.find("[data-ref='indeditor']");
  me.element.jobeditor = me.element.root.find("[data-ref='jobeditor']");
  
  me.data = {};

  me.setData = function(session)
  {
    me.data.club = session.club;
    me.refresh();
  };

  me.refresh = function()
  {
    var addMember = function(user)
    {
      var $member = me.element.list.find("tr.template").clone();
      $member.removeClass("template");
      $member.attr("data-src", user.id);
      $member.find("[data-ref='name']").text(user.name);
      $member.find("[data-ref='title']").text(user.title);
      if (user.hasJobData())
      {
        var icon = $member.find("[data-ref='hasJobData']");
        icon.removeClass("icon-pencil");
        icon.addClass("icon-ok");
      }
        
      if (user.hasFBId())
      {
        var $fblink = $("<a>").attr("href", user.getFBUrl()).attr("target", "_blank");
        $fblink.append($("<fb:name uid='" + user.fbid + "' useyou='false' linked='false' />"));
        $member.find("[data-ref='fbid']").append($fblink);
      }
      else
      {
        $member.find("[data-ref='fbid']").text("未連結Facebook帳號");
      }
      $member.find("[data-link='remove']").click(function(e)
      {
        var id = $(e.target).parents("tr").attr("data-src");
        var user = me.data.club.getUserById(id);
        if (confirm("確定要刪除 " + user.name + " 嗎?"))
        {
          me.data.club.removeMember(user, function(result)
          {
            console.log(result);
            if (result.code != 0)
            {
              alert("啊，壞掉了！\n\n錯誤碼：" + result.code);
              return;
            }
            $(e.target).parents("tr").remove();
          });
        }
      });
      $member.find("[data-link='edit']").click(function(e)
      {
        var id = $(e.target).parents("tr").attr("data-src");
        me.showEditor(me.data.club.getUserById(id));
      });
      $member.find("[data-link='edit_job']").click(function(e){
    	  var id = $(e.target).parents("tr").attr("data-src");
    	  var user = me.data.club.getUserById(id);
    	  if (user.hasJobData()){
        	  me.showEditorJob(id);
    	  }
    	  else
        	  me.showEditorJobNew(user);
      });
      me.element.list.append($member);
    };

    me.element.list.find("tr:not(.template)").remove();
    for (var i = 0; i < me.data.club.member.length; ++i)
    {
      addMember(me.data.club.member[i]);
    }
    if (window.FB)
      FB.XFBML.parse(me.element.list[0]);
    me.element.root.find("[data-ref='member_count']").text(me.data.club.member.length);
  };

  var updateFBInfo = function(fbid)
  {
    var $fbinfo = me.element.editor.find("#member_fb_info");
    if (fbid)
    {
      $fbinfo.find("[data-ref='fb_pic_url']").attr("src", "https://graph.facebook.com/" + fbid + "/picture?type=square");
      var fb_link = "https://www.facebook.com/" + fbid;
      $fblink = $fbinfo.find("[data-ref='fb_link']");
      $fblink.empty();
      $fblink.attr("href", fb_link).attr("target", "_blank");
      $fblink.append($("<fb:name uid='" + fbid + "' useyou='false' linked='false' />"));
      FB.XFBML.parse($fblink[0]);
    }
    $fbinfo.attr("data-src", fbid);
  };

  me.showEditorJob = function(userID)
  {
	  me.element.editorJob.attr("data-src", userID);
	  var setJobData = function(user){
		  me.element.editorJob.find("[data-ref='name']").text(user.name);
		  me.element.editorJob.find("[data-ref='company']").val(user.company);
		  me.element.editorJob.find("[data-ref='jobtitle']").val(user.jobtitle);
		  me.element.editorJob.find("[data-ref='otherData']").val(user.otherData);
		  me.element.editorJob.find("input[name='opendata'][value='" + user.opendata + "']").attr("checked", true);
		  me.element.editorJob.find("[data-ref='industryName']").text(user.industrystring);
		  me.element.editorJob.find("[data-ref='jobcatName']").text(user.jobcatstring);
		  me.element.editorJob.find("[name='industryID']").val(user.industry);
		  me.element.editorJob.find("[name='jobcatID']").val(user.jobcat);
	  };
	  var getJobData = function(userID){
			$.ajax({
				url: './api/getCareerByUserID.php',
				data: {userID: userID},
				dataType: 'json',
				success: function(data){
					setJobData(data);
				},
				error: function(){
					alert('啊，壞掉啦！ error:連線失敗');
				}				
			});
			};  
	  getJobData(userID);
	  me.element.editorJob.modal();
  };

  me.showEditorJobNew = function(user)
  {
	  me.element.editorJob.attr("data-src", user.id);
	  me.element.editorJob.find("[data-ref='name']").text(user.name);
	  me.element.editorJob.find("[data-ref='company']").val("");
	  me.element.editorJob.find("[data-ref='jobtitle']").val("");
	  me.element.editorJob.find("[data-ref='otherData']").val("");
	  me.element.editorJob.find("input[name='opendata'][value='1']").attr("checked", true);
	  me.element.editorJob.find("[data-ref='industryName']").text("");
	  me.element.editorJob.find("[data-ref='jobcatName']").text("");
	  me.element.editorJob.find("[name='industryID']").val(0);
	  me.element.editorJob.find("[name='jobcatID']").val(0);
	  me.element.editorJob.modal();
  };

  var initEditorJob = function()
  {
	  me.element.editorJob.find("#btnIndustry").click(function(){
			me.element.indeditor.modal();
	  });
	  me.element.editorJob.find("#btnJobcat").click(function(){
			me.element.jobeditor.modal();
	  });
	  me.element.indeditor.find("[data-link='setInd']").click(function(){
			var selNo = $(this).attr("data-ref");
			var selName = $(this).text();
			me.element.editorJob.find("[name='industryID']").val(selNo);
			$("#selIndustry").text(selName);
			me.element.indeditor.modal('hide');
			  return false;
		  });

		  me.element.jobeditor.find("[data-link='setJob']").click(function(){
				var selNo = $(this).attr("data-ref");
				var selName = $(this).text();
				me.element.editorJob.find("[name='jobcatID']").val(selNo);
				$("#selJobCat").text(selName);
				me.element.jobeditor.modal('hide');
				  return false;
			  });
	  me.element.editorJob.find("[data-link='jobsave']").click(function(){

		  if (me.element.editorJob.find("input[name='opendata']:checked").val() === undefined)
			{
				alert("請選擇是否公開資料");
				me.element.editorJob.find("input[name='opendata']").focus();
				return;
			}
			if (me.element.editorJob.find("[name='industryID']").val() == "0")
			{
				alert("請選擇您的產業類別");
				return;
			}
			if (me.element.editorJob.find("[name='jobcatID']").val() == "0")
			{
				alert("請選擇您的職務類別");
				return;
			}
		  var career = new Career();
		  
		  career.id = me.element.editorJob.attr("data-src");
		  career.company = me.element.editorJob.find("[data-ref='company']").val();
		  career.jobtitle = me.element.editorJob.find("[data-ref='jobtitle']").val();
		  career.otherData = me.element.editorJob.find("[data-ref='otherData']").val();
		  career.opendata = me.element.editorJob.find("input[name='opendata']:checked").val();
		  career.industry = me.element.editorJob.find("[name='industryID']").val();
		  career.jobcat = me.element.editorJob.find("[name='jobcatID']").val();
		  console.log(career);
		  career.save(function(result)
				    {
				      console.log(result);
				      if (result.code != 0)
				      {
				        alert("啊，壞掉了！\n\n錯誤碼：" + result.code);
				        return;
				      }
				      //me.data.career.id = result.id;
				      alert("儲存完成。");
				     
				    });
		  });
  };

  me.showEditor = function(user)
  {
    me.element.editor.attr("data-src", user.id);
    me.element.editor.find("[data-ref='name']").val(user.name);
    me.element.editor.find("[data-ref='title']").val(user.title);
    me.element.editor.find("[data-ref='background']").val(user.background);
    me.element.editor.find("[data-ref='birth-year']").val(user.birth_year);
    me.element.editor.find("[data-ref='birth-month']").val(user.birth_month);
    me.element.editor.find("[data-ref='note']").val(user.note);
    me.element.editor.find("[data-ref='fbid']").val("");
    //me.element.editor.find("[data-ref='reg_date']").val(user.reg_date);
    //me.element.editor.find("[data-ref='exit_date']").val(user.exit_date);
    updateFBInfo(user.fbid);
    me.element.editor.modal();
  };

  var showEditorNew = function()
  {
    var user = new User();
    me.showEditor(user);
  };

  var initEditor = function()
  {
    var getFBFriendList = function(done)
    {
      if (!me.data.has_fb_friend_list)
      {
        me.data.fb_friend_list = [];
        FB.api("/me/friends", function(response)
        {
          console.log(response);
          me.data.fb_friend_list = response.data;
          done();
        });
      }
      me.data.has_fb_friend_list = true;
      done();
    };

    var getFBIdByName = function(name)
    {
      for (var i = 0; i < me.data.fb_friend_list.length; ++i)
      {
        if (me.data.fb_friend_list[i].name == name)
          return me.data.fb_friend_list[i].id;
      }
      return null;
    };

    var getFriendNames = function()
    {
      var names = new Array();
      for (var i = 0; i < me.data.fb_friend_list.length; ++i)
      {
        names.push(me.data.fb_friend_list[i].name);
      }
      return names;
    };

    me.element.editor.find("[data-ref='fbid']").typeahead({
      source: function(query, process)
      {
        getFBFriendList(function()
        {
          process(getFriendNames());
        })
      },
      items: 4,
      updater: function(item)
      {
        console.log(item);
        var fbid = getFBIdByName(item);
        console.log(fbid);
        updateFBInfo(fbid);
      }
    });
    
    $("#member_fb_info [data-link='remove']").click(function()
    {
      $("#member_fb_info").attr("data-src", "");
    });
    
    me.element.editor.find("[data-link='save']").click(function()
    {
      var data = {};
      var id = parseInt(me.element.editor.attr("data-src"));
      var user = me.data.club.getUserById(id);
      if (!user)
      {
        user = new User();
      }

      user.name = me.element.editor.find("[data-ref='name']").val();
      user.title = me.element.editor.find("[data-ref='title']").val();
      user.fbid = me.element.editor.find("#member_fb_info").attr("data-src");
      user.club_id = me.data.club.id;
      user.background = me.element.editor.find("[data-ref='background']").val();
      user.birth_year = me.element.editor.find("[data-ref='birth-year']").val();
      user.birth_month = me.element.editor.find("[data-ref='birth-month']").val();
      user.note = me.element.editor.find("[data-ref='note']").val();
	  //user.gender = me.element.editor.find("input[name='gender']:checked").val();
	  //user.reg_date = me.element.editor.find("[data-ref='reg_date']").val();
	  //user.exit_date = me.element.editor.find("[data-ref='exit_date']").val();
      
      if (user.background == 0)
      {
        alert("請選擇團員身分");
        me.element.editor.find("[data-ref='background']").focus();
        return;
      }
      if (user.birth_year < 1980 || user.birth_year > 2014 || user.birth_month < 1 || user.birth_month > 12)
      {
        alert("請填寫正確團員出生年月");
        me.element.editor.find("[data-ref='birth-year']").focus();
        return;
      }
      if (user.isNew())
      {
        me.data.club.addMember(user,function(result)
        {
          console.log(result);
          if (result.code != 0)
          {
            alert("啊，壞掉了！\n\n錯誤碼：" + result.code);
            return;
          }
          me.element.editor.modal('hide');
          me.refresh();
        });
      }
      else
      {
        user.save(function(result)
        {
          console.log(result);
          if (result.code != 0)
          {
            alert("啊，壞掉了！\n\n錯誤碼：" + result.code);
            return;
          }
          me.element.editor.modal('hide');
          me.refresh();
        });
      }
    });
  };
  initEditor();
  initEditorJob();
  me.element.root.find("[data-link='add']").click(showEditorNew);
}

$(document).ready(function()
{

	var ind = new IndData(_r.data);
	ind.refresh();
	var job = new JobCatData( _r.data);
	job.refresh();
  var member_list = new MemberList($("#page_content"));
  member_list.setData(_r.session);
  
});
</script>
<style>
#member_fb_info
{
  margin-top: 10px;
}
#member_fb_info[data-src='']
{
  display: none;
}
#member_fb_info img[data-ref='fb_pic_url']
{
  width: 25px;
  height: 25px;
  margin-right: 5px;
}
</style>
        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>團員管理</h1>
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Nickname</th>
                <th>職稱</th>
                <th>Facebook連結</th>
                <th colspan="3">功能</th>
              </tr>
            </thead>
            <tbody id="memeberListtBody">
              <tr class="template">
                <td data-ref="name"></td>
                <td data-ref="title"></td>
                <td data-ref="fbid"></td>
                <td><a href="#" data-link="edit" data-visible="owner"><i class="icon-pencil"></i> 修改</a></td>
                <td><a href="#" data-link="edit_job" data-visible="owner"><i class="icon-pencil" data-ref="hasJobData"></i>職業</a></td>
                <td><a href="#" data-link="remove" data-visible="owner"><i class="icon-remove"></i> 刪除</a></td>
              </tr>
            </tbody>
          </table>
          <p class="pull-left">團員人數：<span data-ref="member_count"></span></p>
          <p class="pull-right" data-visible="owner">
            <a id="create_meeting" class="btn btn-primary" href="#" data-link="add">新增</a>
          </p>
          <p class="clearfix"></p>

          <!-- begin modal -->
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="editor">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4>團員資訊</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal">
                <div class="control-group">
                  <label class="control-label" for="member_name">Nickname</label>
                  <div class="controls">
                    <input type="text" id="member_name" data-ref="name" />
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_title">職稱</label>
                  <div class="controls">
                    <input type="text" id="member_title" data-ref="title" data-provide="typeahead" data-source='["團長","秘書","IPP","財務主委","團務主委","國際主委","職業主委","社服主委","團員"]'/>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_background">身分</label>
                  <div class="controls">
                    <select class="input-medium" id="member_background" data-ref="background">
                      <option value="0">尚未選擇</option>
                      <option value="1">學生</option>
                      <option value="2">社青</option>
                    </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">性別</label>
                  <div class="controls">
                    <label class="radio inline"><input type="radio" name="gender" data-ref="gender" value="0" disabled/>女</label>
                    <label class="radio inline"><input type="radio" name="gender" data-ref="gender" value="1" disabled/>男</label>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_birth">出生年月</label>
                  <div class="controls">
                    <input type="number" class="input-small" id="member_birth" data-ref="birth-year" min="1980" max="2014" />年
                    <input type="number" class="input-small" id="member_birth_month" data-ref="birth-month" min="1" max="12" />月
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_joindate">入團日期</label>
                  <div class="controls">
                    <input type="date" class="input-medium" id="member_joindate" data-ref="reg_date" disabled/> 
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_birth">退團/OB日期</label>
                  <div class="controls">
                    <input type="date" class="input-medium" id="member_exitdate" data-ref="exit_date" disabled/> 
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_note">備註</label>
                  <div class="controls">
                  <textarea data-ref="note" rows="3" class="input" placeholder=""></textarea>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="member_fbid">Facebook連結</label>
                  <div class="controls height-min-120">
                    <input type="text" id="member_fbid" data-ref="fbid" placeholder="輸入Facebook好友名稱以設定" />
                    <div class="well well-small" id="member_fb_info" data-src=""><img data-ref="fb_pic_url" /><a data-ref="fb_link"></a><a href="#" class="close pull-right" data-link="remove">&times;</a></div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
              <a href="#" class="btn btn-primary" data-link="save">確定</a>
            </div>
          </div>
          <!-- end modal -->
          <!-- model Job  -->
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="editor_job">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4>團員職業資訊 - <span data-ref="name"></span></h4>
            </div>
            <div class="modal-body">
            	<form class="form-horizontal">
<!--             		<div class="control-group"> -->
<!--                   		<label class="control-label" for="member_name" data-ref="name">Nickname</label> -->
<!--                   		<div class="controls"> -->
<!--                     	<label  id="member_name" data-ref="empty" ></label> -->
<!--                   		</div> -->
<!--                 	</div> -->
                	<div class="control-group">
                		<label class="control-label" for="opendata">公開設定</label>
                		<div class="controls">
                				<label class="radio inline"><input type="radio" name="opendata" data-ref="opendata" value="0" />不公開資料</label>
                    			<label class="radio inline"><input type="radio" name="opendata" data-ref="opendata" value="1" />公開資料</label>
                		</div>
                	</div>
                	<div class="control-gorup">
                		<label class="control-label" for="selIndustry">產業類別</label>
                		<div class="controls">
                			<input type="hidden"  name ="industryID" value="0" />
                			<span class="label label-default" id="selIndustry" data-ref="industryName"></span> 
            				<input id="btnIndustry" type="button" class="btn btn-small" value="選擇"/>
                		</div>
                	</div>
                	<br />
                	<div class="control-gorup">
                		<label class="control-label" for="selJobCat">職務類別</label>
                		<div class="controls">
                			<input type="hidden"  name ="jobcatID" value="0" />
                			<span class="label label-default" id="selJobCat" data-ref="jobcatName"></span>
         	   				<input id="btnJobcat" type="button" class="btn btn-small" value="選擇"/>
                		</div>
                	</div>
                	<br />
                	<div class="control-gorup">
                		<label class="control-label" for="company">公司</label>
                		<div class="controls">
                			<input id="company" type="text" class="input-large" data-ref="company" placeholder="公司(選填)" />
                		</div>
                	</div>
                	<div class="control-group">
                		<label class="control-label" for="jobtitle">職位</label>
                		<div class="controls">
                			<input type="text" class="input-large" data-ref="jobtitle" placeholder="職位(選填)" />
                		</div>
                	</div>
                	<div class="control-group">
                		<label class="control-label" for="otherData">自我介紹</label>
                		<div class="controls">
                			<textarea id="otherData" data-ref="otherData" rows="6" class="input-large" placeholder="自我介紹"></textarea>
                		</div>
                	</div>
            	</form>
            </div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
              <a href="#" class="btn btn-primary" data-link="jobsave">儲存</a>
            </div>
          </div>
          
          <!-- end modal Job -->
          <!-- industry model begin -->
          <div id="inds" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="indeditor">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4>選擇產業類別</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                  <table class="table table-striped table-bordered">
                  	<thead>
                  		<tr>
                  			<th>資訊科技</th>
                  			<th>傳產/製造</th>
                  			<th>工商服務</th>
                  			<th>民生服務</th>
                  			<th>文教/傳播</th>
                  		</tr>
                  	</thead>
                  	<tbody>
                  		<td data-ref="indcat1"></td>
                  		<td data-ref="indcat2"></td>
                  		<td data-ref="indcat3"></td>
                  		<td data-ref="indcat4"></td>
                  		<td data-ref="indcat5"></td>
                  	</tbody>
                  </table>
                </div>
            </div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
              
            </div>
          </div>
          
          
          <!-- industry model end -->
          
          <!-- jobcat model begin -->
          <div id="jobs" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="jobeditor">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4>選擇職務類別</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                  <table class="table table-striped table-bordered">
                  	<thead>
                  		<tr>
                  			<th>經管行銷</th>
                  			<th>工程製造</th>
                  			<th>文化創意</th>
                  			<th>工商服務</th>
                  			<th>其他專業</th>
                  		</tr>
                  	</thead>
                  	<tbody>
                  		<td data-ref="jobcat1"></td>
                  		<td data-ref="jobcat2"></td>
                  		<td data-ref="jobcat3"></td>
                  		<td data-ref="jobcat4"></td>
                  		<td data-ref="jobcat5"></td>
                  	</tbody>
                  </table>
                </div>
            </div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
              
            </div>
          </div>
          
          
          <!-- jobcat model end -->

<? $builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
