<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>広告一覧</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- The styles -->
	<link id="bs-css" href="css/bootstrap-weed.css" rel="stylesheet">
	<style type="text/css">
	  body {
		padding-bottom: 40px;
	  }
	  .sidebar-nav {
		padding: 9px 0;
	  }
	  .viewoff {
		background-color: #aaaaaa;
	  }
	</style>

	<!-- The fav icon -->
	<link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
    
<?php
//    require_once("login.php");
    require_once(dirname(__FILE__).'/../conf/ini.php');
    $obj = new AffiTown();
    $array = array();
    $array['type'] = 0;
    $array['num'] = 3000;
    if(isset($_GET['name'])){
	$array['name'] = $_GET['name'];
    }
    if(isset($_GET['status'])){
	$array['status'] = $_GET['status'];
    } else {
	$array['status'] = 0;
    }
    if(isset($_GET['timesale'])){
	$array['timesale'] = $_GET['timesale'];
    } else {
	$array['timesale'] = 0;
    }
    if(isset($_GET['page'])){
	$array['page'] = $_GET['page'];
    } else {
	$array['page'] = 0;
    }

    $row = $obj -> getPromotions($array);

?>
    
<?php require_once('header.php'); ?>
	<!-- topbar ends -->
		<div class="container-fluid">
		<div class="row-fluid">
			
			<noscript>
				<div class="alert alert-block span10">
					<h4 class="alert-heading">Warning!</h4>
					<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
				</div>
			</noscript>
			
			<div id="content" class="span10">

			<form action="setpromotion.php"><input type="submit" class="btn btn-primary" value="新規案件追加"></form>
			<!-- content starts -->
			<h2><?php if($array['timesale']){ echo "タイムセール"; } else { echo "通常広告"; } ?>一覧</h2>
			<div class="row-fluid sortable">
				<div class="box span12"><!-- フルサイズのパネル？ -->
					<div class="box-header well" data-original-title>
					<div class="box-content">
						<form class="form-horizontal" id="frmInput" name="frmInput" method="get" action="">
						  <fieldset>
							<div class="control-group">
								<label class="control-label">広告名</label>
								<div class="controls">
								  <input name="name" class="input-xlarge focused" id="name" type="text" value="<?php echo $array['name'] ?>">
								</div>
				            </div>

                            <div class="control-group"><!-- セレクトメニュー -->
								<label class="control-label">掲載</label>
								<div class="controls">
								<input type="radio" name="status" value="0" <?php if($array['status']!=1){echo "checked"; }?>>全案件
								<input type="radio" name="status" value="1" <?php if($array['status']==1){echo "checked"; }?>>掲載中の案件のみ
								</div>
							  </div>
                            <div class="form-actions">
                                <input type="hidden" name="page" value="0">
                                <input type="hidden" name="timesale" value="<?php echo $array['timesale'] ?>">
                                <input type="submit" class="btn btn-primary" value="検索"/>
                            </div>
						  </fieldset>
						</form>   
					</div>
				</div><!--/span-->
			</div><!--/row-->
            <div class="row-fluid sortable">
			<div class="box span12"><!-- フルサイズのパネル？ -->
				<div class="box-content">
					<table class="table table-striped table-bordered bootstrap-datatable">
					  <thead>
						<tr>
			                                <th scope="col">ID</th>
			                                <th scope="col">ATID</th>
                        			        <th scope="col" colspan=2>広告概要</th>
			                                <th scope="col">価格</th>
                        			        <th scope="col">付与</th>
                        			        <th scope="col">報酬</th>
			                                <th scope="col">日別</th>
                        			        <th scope="col">累計</th>
			                                <th scope="col">掲載設定期間</th> 
                        			        <th scope="col">TS</th> 
						  </tr>
					  </thead>   
					  <tbody>
	
                          <?php
                                 foreach($row as $key => $value){
/*
                                    $obj2 = new Summaries();
                                    $cva = $obj2 -> searchConversionSummaries($value['advertisement_id'],0);
                                    $cvd = $obj2 -> searchConversionSummaries($value['advertisement_id'],1);
*/
                                ?>
				<tr<?php if($value['view']==0){ echo ' class="viewoff"'; } ?>>
				    <td class="center"><a href="setpromotion.php?id=<?php print(htmlspecialchars($value['affitown_advertisement_id'])); ?>"><?php print(htmlspecialchars($value['affitown_advertisement_id'])); ?></a></td>
                                    <td class="center"><?php print(htmlspecialchars($value['adid'])); ?></td>
				    <td class="center"><?php echo $value['img']; ?></td>
                                    <td class="center"><?php print(htmlspecialchars($value['name'])); ?></td>
                                    <td class="center"><?php print(htmlspecialchars($value['amount']));?></td>
                                    <td class="center"><?php print(htmlspecialchars(number_format($value['unit_price']*DEFAULT_RATE)."DIG"));
					if($array['timesale']){ print(" → <font color=\"#f00\">".number_format($value['timesale_point'])."DIG</font>"); } ?></td>
                                    <td class="center"><?php print(htmlspecialchars("￥".number_format($value['unit_price'])));?></td>
                                    <td class="center"><?php if($cvd) { print(htmlspecialchars($cvd)); } ?></td>
                                    <td class="center"><?php if($cva) { print(htmlspecialchars($cva)); } ?></td>
                                    <td class="center"><?php print(htmlspecialchars($value['start_time']." ～ ".$value['end_time'])); ?></td>
				    <td class="center"><a href="settimesale.php?id=<?php print(htmlspecialchars($value['affitown_advertisement_id']));?>">TS</a></td>
				</tr>
                                  <?php } ?>
			  </tbody>
			 </table>
                        <form method="get" action="">
                            <input type="hidden" name="name" value="<?php echo $array['name'] ?>">
                            <input type="hidden" name="status" value="<?php echo $array['status'] ?>">
                            <input type="hidden" name="timesale" value="<?php echo $array['timesale'] ?>">
                            <input type="hidden" name="page" value="<?php echo $array['page']-1 ?>">
                            <input type="submit" style="margin: 10px; float: left;" class="btn btn-primary" value="前へ" <?php if($array['page']<1){echo "disabled";}?>>
                        </form>

                        <form method="get" action="">
                            <input type="hidden" name="name" value="<?php echo $array['name'] ?>">
                            <input type="hidden" name="status" value="<?php echo $array['status'] ?>">
                            <input type="hidden" name="timesale" value="<?php echo $array['timesale'] ?>">
                            <input type="hidden" name="page" value="<?php echo $array['page']+1 ?>">
                            <input type="submit" style="margin: 10px; float: left;" class="btn btn-primary" value="次へ" <?php if(count($row)<3000){echo "disabled";}?>>
                        </form>

					</div>
				</div><!--/span-->
			</div><!--/row-->
				<!-- content ends -->
			</div><!--/#content.span10-->
				</div><!--/fluid-row-->
				
		<hr>
		
	</div><!--/.fluid-container-->

	<!-- jQuery -->
	<script src="../js/jquery-1.7.2.min.js"></script>
	<!-- jQuery UI -->
	<script src="../js/jquery-ui-1.8.21.custom.min.js"></script>
	<!-- transition / effect library -->
	<script src="../js/bootstrap-transition.js"></script>
	<!-- alert enhancer library -->
	<script src="../js/bootstrap-alert.js"></script>
	<!-- modal / dialog library -->
	<script src="../js/bootstrap-modal.js"></script>
	<!-- custom dropdown library -->
	<script src="../js/bootstrap-dropdown.js"></script>
	<!-- scrolspy library -->
	<script src="../js/bootstrap-scrollspy.js"></script>
	<!-- library for creating tabs -->
	<script src="../js/bootstrap-tab.js"></script>
	<!-- library for advanced tooltip -->
	<script src="../js/bootstrap-tooltip.js"></script>
	<!-- popover effect library -->
	<script src="../js/bootstrap-popover.js"></script>
	<!-- button enhancer library -->
	<script src="../js/bootstrap-button.js"></script>
	<!-- accordion library (optional, not used in demo) -->
	<script src="../js/bootstrap-collapse.js"></script>
	<!-- carousel slideshow library (optional, not used in demo) -->
	<script src="../js/bootstrap-carousel.js"></script>
	<!-- autocomplete library -->
	<script src="../js/bootstrap-typeahead.js"></script>
	<!-- tour library -->
	<script src="../js/bootstrap-tour.js"></script>
	<!-- library for cookie management -->
	<script src="../js/jquery.cookie.js"></script>
	<!-- calander plugin -->
	<script src='../js/fullcalendar.min.js'></script>
	<!-- data table plugin -->
	<script src='../js/jquery.dataTables.min.js'></script>

	<!-- chart libraries start -->
	<script src="../js/excanvas.js"></script>
	<script src="../js/jquery.flot.min.js"></script>
	<script src="../js/jquery.flot.pie.min.js"></script>
	<script src="../js/jquery.flot.stack.js"></script>
	<script src="../js/jquery.flot.resize.min.js"></script>
	<!-- chart libraries end -->

	<!-- select or dropdown enhancer -->
	<script src="../js/jquery.chosen.min.js"></script>
	<!-- checkbox, radio, and file input styler -->
	<script src="../js/jquery.uniform.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="../js/jquery.colorbox.min.js"></script>
	<!-- rich text editor library -->
	<script src="../js/jquery.cleditor.min.js"></script>
	<!-- notification plugin -->
	<script src="../js/jquery.noty.js"></script>
	<!-- file manager library -->
	<script src="../js/jquery.elfinder.min.js"></script>
	<!-- star rating plugin -->
	<script src="../js/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="../js/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="../js/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="../js/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="../js/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="../js/charisma.js"></script>
	
		
</body>
</html>
