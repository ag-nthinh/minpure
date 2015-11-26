<?php 
  //  ini_set('display_errors',1);
    require_once(dirname(__FILE__).'/../conf/ini.php');

    $obj = new Timesales();
    if(!$p_data = $obj -> getTimesalePromotion($_GET['id'])){
	echo "不正なURLです";
	exit();
    }

?>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>タイムセール新規登録</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link id="bs-css" href="css/bootstrap-weed.css" rel="stylesheet">
</head>
<body>

<?php require_once('header.php'); ?>

<h2>タイムセール新規登録</h2>
<br>
<br>
<?php echo $p_data['img'] ?><br>
ID : <?php echo $p_data['affitown_advertisement_id'] ?><br>
AffiTownID : <?php echo $p_data['adid'] ?><br>
URL : <?php echo $p_data['url'] ?><br>
案件名 : <?php echo $p_data['name'] ?><br>
案件価格 : <?php echo $p_data['amount'] ?><br>
成果地点 : <?php echo $p_data['action'] ?><br>
報酬単価 : <?php echo "￥".number_format($p_data['unit_price']) ?><br>
掲載期間 : <?php echo $p_data['start_time']." ～ ".$p_data['end_time'] ?><br>

<br>
<form name="frm" method="post" action="settimesale_end.php">
<h3>1) 付与ポイント</h3>
通常時 <b><?php echo $p_data['point'] ?></b> DIG<br>
↓<br>
タイムセール時 <input name="timesale_point" id="timesale_point" type="text" style="width:100px; height:30px;" value=""> DIG<br>
<br>
<h3>2) タイムセール実施期間</h3>
開始 <input size="40" name="start_time" id="start_time" type="text" style="width:200px; height:30px;" value="<?php echo date("Y-m-d H:i:s") ?>"><br>
<br>
終了 <input size="40" name="end_time" id="end_time" type="text" style="width:200px; height:30px;" value="<?php echo date("Y-m-d H:i:s", strtotime("+1 month")) ?>"><br>
<br>
<input type="hidden" name="affitown_advertisement_id" value="<?php echo $p_data['affitown_advertisement_id'] ?>">
<input type="hidden" name="point" value="<?php echo $p_data['point'] ?>">
<p><input type="submit" class="btn btn-primary" value="登録する"> <input type="button" class="btn btn-primary" value="キャンセル" onClick="history.back()"></p>
</form>

</body>
</html>
