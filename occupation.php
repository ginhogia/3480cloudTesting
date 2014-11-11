<?php
require_once("mod/session.php");
require_once("mod/page-builder.php");
require_once("mod/job.php");
require_once("mod/Industry.php");
require_once("mod/racyear.php");
$session = new Session();
$session->setPageId(PAGE_ID_JOBQUERY);
$session->checkPermission();	

//$racyear = Racyear::GetThisYear();
$userID = $session->getUser()->getId();

$data = array();

$data["clubList"] = club::getClubNameList();
$data["inds"] = industry::getIndustriesArray();
$data["jobs"] = jobcat::getJobcatArray();

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
	me.openMoreData = function(data){
		me.element.moreInfoArea.find("[data-link='back']").click(function(){
// 			me.element.moreInfoArea.hide("slide",{direction: "right"},400);
// 			me.element.listArea.show("slide",{direction: "left"},400);
			me.showOrHideDetailModal(false);
			});
		
		me.element.moreInfoArea.find("[data-ref='name']").text(data.name);
		me.element.moreInfoArea.find("[data-ref='club']").text(_r.data.clubList[data.club_id]);
		me.element.moreInfoArea.find("[data-ref='company']").text(data.company);
	    me.element.moreInfoArea.find("[data-ref='jobtitle']").text(data.jobtitle);
	    me.element.moreInfoArea.find("[data-ref='otherData']").text(data.otherData);
	    me.element.moreInfoArea.find("[data-ref='industryName']").text(data.industrystring);
	    me.element.moreInfoArea.find("[data-ref='jobcatName']").text(data.jobcatstring);

	    me.showOrHideDetailModal(true);
// 		me.element.listArea.hide("slide",{direction: "left"},1000);
// 		me.element.moreInfoArea.show("slide",{direction: "right"},1000);
	};

	me.showOrHideDetailModal = function(show,anispeed){
		speed = 600;
		if(!speed)
			speed = anispeed;
		if (show == true){
			me.element.listArea.hide("slide",{direction: "left"},speed);
			me.element.moreInfoArea.show("slide",{direction: "right"},speed);
		}
		else{
			me.element.moreInfoArea.hide("slide",{direction: "right"},speed);
			me.element.listArea.show("slide",{direction: "left"},speed);
		}
	};
};



$(document).ready(function()
		{
			clubListTable(_r.data.clubList,4,$("#partyArea .tableContent"));
			var ind = new IndData(_r.data);
			ind.refresh();
			var job = new JobCatData(_r.data);
			job.refresh();

			var resultArea = new QueryResult($("#page_content"));
			//resultArea.setData(indTestData);

			$("[data-link='queryClub']").click(function(){
 				var club_id = $(this).attr("data-ref");
				resultArea.getClubData(club_id);
 				resultArea.showOrHideDetailModal(false,50);
				$("[data-ref='queryList']").modal('show');
				  return false;
			  });
			
			$("[data-link='setInd']").click(function(){
				var indID = $(this).attr("data-ref");
				resultArea.getIndData(indID);
 				resultArea.showOrHideDetailModal(false,50);
				$("[data-ref='queryList']").modal('show');
				  return false;
			  });
			$("[data-link='setJob']").click(function(){
				var jobID = $(this).attr("data-ref");
				resultArea.getJobData(jobID);
 				resultArea.showOrHideDetailModal(false,50);
				$("[data-ref='queryList']").modal('show');
				  return false;
			  });
			$(".areaHeader").click(function(){
				var $contentArea = $(this).parent().find(".tableContent");
				
				$(".tableContent").each(function(){
						$(this).slideUp();
					});
				$contentArea.slideDown();
			});
		});

</script>

        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>地區職業分類名錄</h1>
          
          <!-- party list -->
          <div id="partyArea">
          	<div class="areaHeader">
          	<h3>依團名查詢</h3>
         	 </div>
          	<div class="tableContent hide">
<!--           		<table class="table table-striped table-bordered"> -->
<!--           			<tbody> -->
          		
<!--           			</tbody> -->
<!--           		</table> -->
          	</div>
          
          </div>
          <!-- end party list -->
          
          
          <!-- industry list -->
          <div id="industryArea">
          <div class="areaHeader">
          	<h3>依產業查詢</h3>
          </div>
          <div class="tableContent hide">
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
          <!-- industry end -->
          
          
          <!-- jobCatagory list -->
          <div id="jobcatArea">
          <div class="areaHeader">
          	<h3>依職務查詢</h3>
          </div>
          <div class="tableContent hide">
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
                  		<td data-ref="jobcat1"></td>
                  		<td data-ref="jobcat2"></td>
                  		<td data-ref="jobcat3"></td>
                  		<td data-ref="jobcat4"></td>
                  		<td data-ref="jobcat5"></td>
                  	</tbody>
          	</table>
          </div>
          
          </div>
          <!-- jobCatagory end -->
          
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
