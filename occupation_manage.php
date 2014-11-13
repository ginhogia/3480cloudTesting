<?php
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/job.php");
require_once("mod/Industry.php");
require_once("mod/racyear.php");
$session = new Session();
$session->setPageId(PAGE_ID_MANAGE_CAREER);
$session->checkPermissionForDistrictTeam();	

//$racyear = Racyear::GetThisYear();
$userID = $session->getUser()->getId();

$data = array();

$data["clubList"] = club::getClubNameList();
$data["inds"] = industry::getIndustriesArray();
$data["jobs"] = jobcat::getJobcatArray();
$data["compelete"] = career::getCareerFinishData();

$builder = new PageBuilder($session,$data);
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

.modal
{
	width: 80%;
	margin-left: -40%;
	height: 80%;
}
.modal-body
{
	overflow-x: hidden;
}

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
<script src="js/occupation.js"></script>
<script src="js/testData.js"></script>
<script type="text/javascript">

var OccupationDataSource = function(){
	var me = this;
	
};

var CompeleteTable = function(element){
	var me = this;
	me.element ={};
	me.element.root = $(element);
	me.element.table = me.element.root.find("#compeleteTable");
	me.element.tablebody = me.element.table.find("tbody");
	me.data={};
	me.setData = function(data){
		me.data = [];
	  	for (var i = 0; i < data.length; ++i)
	  		me.data.push(data[i]);
	    me.refresh();
	};

	me.refresh = function(){
		
		me.element.tablebody.find("tr.content").remove();
		
		var addResult = function(career)
	    {
		  if (career.length != 0)
		  {
	      var $careerlist = me.element.tablebody.find("tr.template").clone();	      
	      $careerlist.removeClass("template");
	      $careerlist.addClass("content");
	      $careerlist.attr("data-src", career.club_id);
	      //$meeting.find("[data-ref='id']").text(meeting.id);
	      $careerlist.find("[data-ref='club_name']").text(_r.data.clubList[career.club_id]);
	      $careerlist.find("[data-ref='totalNum']").text(career.totalNum);
	      $careerlist.find("[data-ref='done']").text(career.done);
	      var doneRate;
	      	if(career.totalNum ==0)
		      	doneRate = 0;
	      	else
		      	doneRate = ((career.done / career.totalNum) * 100).toFixed(1);
	      $careerlist.find("[data-ref='done_percent']").text(doneRate + '%');
	      $careerlist.find("[data-ref='showoff']").text(career.showoff);
	      var showoffRate;
	      	if (career.done == 0)
		      	showoffRate = 0;
	      	else
	      		showoffRate = ((career.showoff / career.done) * 100).toFixed(1);
	      $careerlist.find("[data-ref='showoff_percent']").text(showoffRate + '%');
	     

// 	      $careerlist.find("[data-link='more']").click(function(e)
// 	      {
// 	        var id = $(e.target).parents("tr").attr("data-src");
// 	        var career = me.getCareerById(id);
// 	        me.openMoreData(career);
// 	      });

	      me.element.tablebody.append($careerlist);
		  }
		};

		for (var i = 0; i < me.data.length; ++i)
    	{
			addResult(me.data[i]);
    	}
    
		};
};


var QueryResult = function(element){
	var me = this;
	me.element = {};
	me.element.root = $(element);
	me.element.list= me.element.root.find("#resultTable").find("tbody");
	me.element.listArea = me.element.root.find("#resultTable");
	me.element.resultMsg = me.element.root.find("#resultMsg");
	me.element.moreInfoArea = me.element.root.find("#moreInfoDiv");
	me.data={};
	me.getCareerById = function(id)
	  {
	  	//id = parseInt(id);
	    for (var i = 0; i < me.data.length; ++i)
	    {
	      if (me.data[i].id == id)
	        return me.data[i];
	    }
	    return null;
	  };
	me.getIndData = function(indID){
			$.ajax({
				url: './api/getCareersByIndustry.php',
				data: {industryID: indID},
				dataType: 'json',
				success: function(data){
					me.setData(data);
				},
				error: function(){
					alert('啊，壞掉啦！ error:連線失敗');
				}				
			});
		};
	me.getJobData = function(jobID){
		$.ajax({
			url: './api/getCareersByJobcat.php',
			data: {jobcatID: jobID},
			dataType: 'json',
			success: function(data){
				me.setData(data);
			},
			error: function(){
				alert('啊，壞掉啦！ error:連線失敗');
			}				
		});
		};
	me.getClubData = function(clubID){
		$.ajax({
			url: './api/getCareersByClub.php',
			data: {clubID: clubID},
			dataType: 'json',
			success: function(data){
				me.setData(data);
			},
			error: function(){
				alert('啊，壞掉啦！ error:連線失敗');
			}				
		});
		};  
	me.setData = function(data){
		me.data = [];
	  	for (var i = 0; i < data.length; ++i)
	  		me.data.push(new Career(data[i]));
	    me.refresh();
	};

	me.refresh = function(){
		
		me.element.list.find("tr.content").remove();
		
		var addResult = function(career)
	    {
	      var $careerlist = me.element.list.find("tr.template").clone();	      
	      $careerlist.removeClass("template");
	      $careerlist.addClass("content");
	      $careerlist.attr("data-src", career.id);
	      //$meeting.find("[data-ref='id']").text(meeting.id);
	      $careerlist.find("[data-ref='club']").text(_r.data.clubList[career.club_id]);
	      $careerlist.find("[data-ref='nickname']").text(career.name);
	      $careerlist.find("[data-ref='industry']").text(career.industrystring);
	      $careerlist.find("[data-ref='jobcat']").text(career.jobcatstring);
	      $careerlist.find("[data-ref='company']").text(career.company);
	      $careerlist.find("[data-ref='jobtitle']").text(career.jobtitle);
	     

	      $careerlist.find("[data-link='more']").click(function(e)
	      {
	        var id = $(e.target).parents("tr").attr("data-src");
	        var career = me.getCareerById(id);
	        me.openMoreData(career);
	      });

	      me.element.list.append($careerlist);
		};

	for (var i = 0; i < me.data.length; ++i)
    {
		addResult(me.data[i]);
    }

	if (me.data.length == 0){
		me.element.resultMsg.css('display','');
		me.element.listArea.css('display','none');
	}
	else{
		me.element.resultMsg.css('display','none');
		me.element.listArea.css('display','');
	}
    
	};
	
};



$(document).ready(function()
		{
			//clubListTable(_r.data.clubList,4,$("#partyArea .tableContent"));
			
			var compelete = new CompeleteTable($("#page_content"));
			compelete.setData(_r.data.compelete);
			var resultArea = new QueryResult($("#page_content"));
			//resultArea.setData(indTestData);

			
			
		});

</script>

        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>地區職業分類名錄管理</h1>
          
          <div class="tableContent">
          	<table id="compeleteTable" class="table table-striped table-bordered">
                  	<thead>
                  		<tr>
                  			<th>團名</th>
                  			<th>總人數</th>
                  			<th>填寫人數</th>
                  			<th>填寫比率</th>
                  			<th>開放查詢人數</th>
                  			<th>開放比率</th>
                  		</tr>
                  	</thead>
                  	<tbody>
                  		<tr class="template">
                  		<td data-ref="club_name"></td>
                  		<td data-ref="totalNum"></td>
                  		<td data-ref="done"></td>
                  		<td data-ref="done_percent"></td>
                  		<td data-ref="showoff"></td>
                  		<td data-ref="showoff_percent"></td>
                  		</tr>
                  	</tbody>
          	</table>
          </div>
                 
          
          <!-- query result -->
          <div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-ref="queryList">
          	<div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 data-ref="resultHeader">查詢結果</h4>
            </div>
            <div class="modal-body">
            	<div class="row-fluid">
            	<div id="resultMsg"><strong class="text-info">喔喔，沒找到任何符合條件的資料</strong></div>
            	<div id="resultTableDiv" class="">
            		<table id="resultTable" class="table table-striped table-bordered">
            		<thead>
            		  <tr>
           			     <th>團名</th>
           			     <th>暱稱</th>
           			     <th>產業別</th>
           			     <th>職務別</th>
           			     <th>公司</th>
           			     <th>職位</th>
           			     <th></th>
          		      </tr>
            </thead>
            <tbody>
              <tr class="template">
                <td data-ref="club"></td>
                <td data-ref="nickname"></td>
                <td data-ref="industry"></td>
                <td data-ref="jobcat"></td>
                <td data-ref="company"></td>
                <td data-ref="jobtitle"></td>
                 
                <td><a href="#" data-link="more">more</a></td>
                
              </tr>
            </tbody>
          </table>
            	</div>
            	<div id="moreInfoDiv" class="hide">
            		<a href="#" data-link="back">&laquo; 返回</a>
            		<h2 data-ref="name"></h2>
            		<h4 data-ref="club"></h4>
            		
            		<table class="table table-condensed">
            			<tbody>
            				<tr>
            					<td class="span3">產業類別</td>
            					<td class="span9"><span class="label label-default" id="selIndustry" data-ref="industryName"></span></td>
            				</tr>
            				<tr>
            					<td>職務類別</td>
            					<td><label class="label" id="selJobCat" data-ref="jobcatName"></label></td>
            				</tr>
            				<tr>
            					<td>公司</td>
            					<td><p data-ref="company"></p></td>
            				</tr>
            				<tr>
            					<td>職稱</td>
            					<td><p data-ref="jobtitle"></p></td>
            				</tr>
            				<tr>
            					<td>自我介紹</td>
            					<td><p data-ref="otherData"></p></td>
            				</tr>
            			</tbody>
            		</table>
            		

<!--             		專長： <input type="text" disabled="true" placeholder="尚未開放" /><br /> --> 
            	</div>
            	</div> 
            </div>  <!-- model body end -->
                  
          </div>
          
         <!-- end query result -->
          <p class="clearfix"></p>

   

<? //$builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
