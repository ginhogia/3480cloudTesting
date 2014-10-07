<?php
require_once("mod/session.php");
require_once("mod/page-builder.php");
$session = new Session();
$session->setPageId(PAGE_ID_MEMBER);
$session->checkPermission();
$builder = new PageBuilder($session);	
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

        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>地區職業分類名錄</h1>
          
          <!-- party list -->
          <table class="table table-striped table-bordered">
          	<tbody>
          	</tbody>
          </table>
          <!-- end party list -->
          
          <!-- query result -->
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>例會編號</th>
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
                <td data-ref="id"></td>
                <td data-ref="date"></td>
                <td data-ref="nickname"></td>
                <td data-ref="industry"></td>
                <td data-ref="jobcat"></td>
                <td data-ref="company"></td>
                <td data-ref="jobtitle"></td>
                 
                <td><a href="#" data-link="more" data-visible="owner">more</a></td>
                
              </tr>
            </tbody>
          </table>
         <!-- end query result -->
          <p class="clearfix"></p>

   

<? $builder->outputPageInfo(); ?>

        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
