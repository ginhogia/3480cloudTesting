<?php
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/job.php");
require_once("mod/Industry.php");
require_once("mod/racyear.php");

$session = new Session();
$session->setPageId(PAGE_ID_PERSONEDIT);
$session->checkPermission();
//$racyear = Racyear::GetThisYear();
$userID = $session->getUser()->getId();
$career = Career::getCareerByUserID($userID);
// if (is_null($career)){
// 	$career = Career::CreateCareerByUserID($userID);
// }
$data = array();
$data["career"] = $career? $career->getData(): null;

$data["inds"] = array();

$temp = Industry::getIndustriesByCat("資訊科技");
if ($temp)
foreach ($temp as $Industry)
{
	$data["inds"]["indcat1"][] = $Industry->getData();
}

$temp = Industry::getIndustriesByCat("傳產/製造");
if ($temp)
foreach ($temp as $Industry)
{
	$data["inds"]["indcat2"][] = $Industry->getData();
}
$temp = Industry::getIndustriesByCat("工商服務");
if ($temp)
foreach ($temp as $Industry)
{
	$data["inds"]["indcat3"][] = $Industry->getData();
}
$temp = Industry::getIndustriesByCat("民生服務");
if ($temp)
foreach ($temp as $Industry)
{
	$data["inds"]["indcat4"][] = $Industry->getData();
}
$temp = Industry::getIndustriesByCat("文教/傳播");
if ($temp)
foreach ($temp as $Industry)
{
	$data["inds"]["indcat5"][] = $Industry->getData();
}

$data["jobs"] = array();

$temp = jobcat::getJobcatsByCat("經管行銷");
if ($temp)
foreach ($temp as $jobcat)
{
	$data["jobs"]["jobcat1"][] = $jobcat->getData();
}
$temp = jobcat::getJobcatsByCat("工程製造");
if ($temp)
foreach ($temp as $jobcat)
{
	$data["jobs"]["jobcat2"][] = $jobcat->getData();
}
$temp = jobcat::getJobcatsByCat("文化創意");
if ($temp)
foreach ($temp as $jobcat)
{
	$data["jobs"]["jobcat3"][] = $jobcat->getData();
}
$temp = jobcat::getJobcatsByCat("工商服務");
if ($temp)
foreach ($temp as $jobcat)
{
	$data["jobs"]["jobcat4"][] = $jobcat->getData();
}
$temp = jobcat::getJobcatsByCat("其他專業");
if ($temp)
foreach ($temp as $jobcat)
{
	$data["jobs"]["jobcat5"][] = $jobcat->getData();
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

<link href="css/datepicker.css" rel="stylesheet">
<script src="js/jquery.form.min.js"></script>
<script type="text/javascript">

var IndData = function(data){
	var me = this;
	me.cats = data.inds; 
	me.setData = function(data){
		cats = data.inds;
	};
	me.refresh = function(){
		var tdCat = $("[data-ref='indcat1']");
		for( var ind in me.cats.indcat1){
			var indEnt = me.cats.indcat1[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat2']");
		for( var ind in me.cats.indcat2){
			var indEnt = me.cats.indcat2[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat3']");
		for( var ind in me.cats.indcat3){
			var indEnt = me.cats.indcat3[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat4']");
		for( var ind in me.cats.indcat4){
			var indEnt = me.cats.indcat4[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat5']");
		for( var ind in me.cats.indcat5){
			var indEnt = me.cats.indcat5[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
	};
};

var JobCatData = function(data){
	var me = this;
	me.cats = data.jobs; 
	me.setData = function(data){
		cats = data.jobs;
	};
	me.refresh = function(){
		var tdCat = $("[data-ref='jobcat1']");
		for( var no in me.cats.jobcat1){
			var jobEnt = me.cats.jobcat1[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat2']");
		for( var no in me.cats.jobcat2){
			var jobEnt = me.cats.jobcat2[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat3']");
		for( var no in me.cats.jobcat3){
			var jobEnt = me.cats.jobcat3[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat4']");
		for( var no in me.cats.jobcat4){
			var jobEnt = me.cats.jobcat4[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat5']");
		for( var no in me.cats.jobcat5){
			var jobEnt = me.cats.jobcat5[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
	};
};


var PersonEditor = function(element)
{
  var me = this;
  me.element = {};
  me.element.root = $(element);
  me.element.basic = me.element.root.find("#basic");
  me.element.save_button = me.element.basic.find("[data-link='save']");
  me.element.indeditor = me.element.root.find("[data-ref='indeditor']");
  me.element.jobeditor = me.element.root.find("[data-ref='jobeditor']");
  //me.element.editor_form = me.element.editor.find("form");

  me.data = {};

  me.setData = function(data)
  {
    console.log(data);
  	me.data.career = new Career(data.career);
  	me.data.career.id = _r.session.user.id;
    me.refresh();
  };

  me.refresh = function()
  {
	//me.element.basic.find("[data-ref='racyear']").val(me.data.event.racyear);
    //me.element.basic.find("[data-ref='name']").text("Hello, " + me.data.career.name);
    //me.element.basic.find("input[name='eventtype'][value='" + me.data.event.eventType + "']").attr("checked", true);
    me.element.basic.find("[data-ref='company']").val(me.data.career.company);
    me.element.basic.find("[data-ref='jobtitle']").val(me.data.career.jobtitle);
    me.element.basic.find("[data-ref='otherData']").val(me.data.career.otherData);
    me.element.basic.find("input[name='opendata'][value='" + me.data.career.opendata + "']").attr("checked", true);
    me.element.basic.find("[data-ref='industryName']").text(me.data.career.industrystring);
    me.element.basic.find("[data-ref='jobcatName']").text(me.data.career.jobcatstring);
    me.element.basic.find("[name='industryID']").val(me.data.career.industry);
    me.element.basic.find("[name='jobcatID']").val(me.data.career.jobcat);

   
  };

  

  me.element.basic.find("[data-link='save']").click(function(e)
  {
//     if (me.element.basic.find("[data-ref='topic']").val() == "")
//     {
//       alert("請填入例會/活動名稱。");
//       me.element.basic.find("[data-ref='topic']").focus();
//       return;
//     }
//     if (me.element.basic.find("[data-ref='date']").val() == "")
//     {
//       alert("請填入例會/活動日期。");
//       me.element.basic.find("[data-ref='date']").focus();
//       return;
//     }
	if (me.element.basic.find("input[name='opendata']:checked").val() === undefined)
	{
		alert("請選擇是否公開資料");
		me.element.basic.find("input[name='opendata']").focus();
		return;
	}

    me.data.career.company = me.element.basic.find("[data-ref='company']").val();
    me.data.career.jobtitle = me.element.basic.find("[data-ref='jobtitle']").val();
    me.data.career.opendata = me.element.basic.find("input[name='opendata']:checked").val();
    me.data.career.industry = me.element.basic.find("[name='industryID']").val();
    me.data.career.jobcat = me.element.basic.find("[name='jobcatID']").val();
    me.data.career.otherData = me.element.basic.find("[data-ref='otherData']").val();
    console.log(me.data);
    me.data.career.save(function(result)
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
  me.element.basic.find("#btnIndustry").click(function(){
			me.element.indeditor.modal();
	  });
  me.element.basic.find("#btnJobcat").click(function(){
		me.element.jobeditor.modal();
});

  me.element.indeditor.find("[data-link='setInd']").click(function(){
	var selNo = $(this).attr("data-ref");
	var selName = $(this).text();
	me.element.basic.find("[name='industryID']").val(selNo);
	$("#selIndustry").text(selName);
	me.element.indeditor.modal('hide');
	  return false;
  });

  me.element.jobeditor.find("[data-link='setJob']").click(function(){
		var selNo = $(this).attr("data-ref");
		var selName = $(this).text();
		me.element.basic.find("[name='jobcatID']").val(selNo);
		$("#selJobCat").text(selName);
		me.element.jobeditor.modal('hide');
		  return false;
	  });

};



$(document).ready(function()
		{
			var ind = new IndData(_r.data);
			ind.refresh();
			var job = new JobCatData(_r.data);
			job.refresh();
			var person_editor = new PersonEditor($("#page_content"));
			person_editor.setData(_r.data);
			
			
		});
	

</script>

<!-- begin content -->
        <div class="span9" id="page_content">
          <h1>個人資料編輯</h1>
          <div id="basic">
<!--           	<label data-ref="name"></label> -->
            <div class="controls">
                    <label class="radio inline"><input type="radio" name="opendata" data-ref="opendata" value="0" />不公開資料</label>
                    <label class="radio inline"><input type="radio" name="opendata" data-ref="opendata" value="1" />公開資料</label>
            </div><br />
            
            <div class="control-group">
            <h4>產業類別
            <input type="hidden"  name ="industryID" value="0" />
             <span class="label label-default" id="selIndustry" data-ref="industryName"></span> 
            <input id="btnIndustry" type="button" class="btn btn-small" value="選擇"/>
            </h4>
            
             
            </div>
            
            <div class="control-group">
            	<h4>職務類別
            		<input type="hidden"  name ="jobcatID" value="0" />
           			 <label class="label" id="selJobCat" data-ref="jobcatName"></label>
         	   <input id="btnJobcat" type="button" class="btn btn-small" value="選擇"/>
            	</h4>
            </div>
            
            
            
         
            
          	
            <input type="text" class="input-xxlarge" data-ref="company" placeholder="公司(選填)" /><br />
            <input type="text" class="input-xxlarge" data-ref="jobtitle" placeholder="職位(選填)" /><br />
            <textarea data-ref="otherData" rows="6" class="input-xxlarge" placeholder="自我介紹"></textarea><br />

            
            <p class="pull-right">
              <a class="btn btn-primary" href="#" data-link="save">儲存</a>
            </p>
          </div>

          <!-- industry model begin -->
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="indeditor">
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
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="jobeditor">
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
          

<? //$builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>