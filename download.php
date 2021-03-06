<?
require_once("mod/session.php");
require_once("mod/page-builder.php");
$session = new Session();
$session->setPageId(PAGE_ID_DOWNLOAD);
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

        <!-- begin content -->
        <div class="span9" id="page_content">
          <h1>文件下載</h1>
          <ul>
            <!--  <li><a href="./file/2013-14年度團秘會簽到表1105.xls">2013-14年度團秘會簽到表 (2013/11/05 更新)</a></li> -->
            <li><a href="./file/2014-15年度團秘會簽到表20140805.xlsx">2014-15年度團秘會簽到表 (2014/08/05 更新)</a></li>
            <li><a href="./file/2014-15年度團秘會簽到表20141009.xlsx">2014-15年度團秘會簽到表 (2014/10/09 更新)</a></li>
            <li><a href="./file/2014-15年度團秘會簽到表20141120.xlsx">2014-15年度團秘會簽到表 (2014/11/20 更新)</a></li>
            <li><a href="./file/2014-15年度團秘會簽到表20150129.xlsx">2014-15年度團秘會簽到表 (2015/01/29 更新)</a></li>
            <li><a href="./file/201415DistrictCong.zip">地區總監與地區代表賀詞</a></li>
          </ul>        
        </div><!--/.span9-->
        <!-- end content -->

      </div><!--/.row-fluid-->

<? $builder->outputFooter(); ?>

    </div><!--/.container-fluid-->
  </body>
</html>
