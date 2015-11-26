<?php
    require_once(dirname(__FILE__).'/../conf/ini.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title><?php echo SITE_NAME ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link id="bs-css" href="css/bootstrap-weed.css" rel="stylesheet">
        <link rel="stylesheet" href="css/table.css" type="text/css">
</head>
<body>

<?php require_once('header.php'); ?>

<h2>売上詳細 <?php echo $_GET['w'] ?></h2>

<a class="button" href="javascript:history.back()">戻　る</a>
<table class="table01" border=0>
<tr>
<th></th>
<th>案　件　名</th>
<th>単　価</th>
<th></th>
<th>件　数</th>
<th></th>
<th>総　額</th>
<th></th>
<th>ユーザ付与</th>
<th>粗　利</th>
</tr>

<?php
$obj = new Sales();
$row = $obj -> countPPSales($_GET['w'], $_GET['t']);

$sum_co = 0;
$sum_sale = 0;

foreach($row as $key => $value){
	$sum_co += $value['co'];
	$sum_sale += $value['sale'];
?>
<tr>
<td><?php echo $value['advertisement_id']."-".$value['campany_type']."-".$value['advertisement_key']; ?></td>
<td id="anken"><?php echo $value['name']; ?></td>
<td><?php echo "￥".number_format($value['payment']); ?></td>
<td> × </td>
<td><?php echo $value['co']; ?></td>
<td> ＝ </td>
<td><?php echo "￥".number_format($value['sale']); ?></td>
<td></td>
<td><?php echo "￥".number_format($value['point'],1); ?></td>
<td><?php echo "￥".number_format($value['profit'],1); ?></td>
</tr>
<?php } ?>

<tr>
<td>計</td>
<td colspan=3></td>
<td><?php echo number_format($sum_co); ?></td>
<td></td>
<td><?php echo "￥ ".number_format($sum_sale); ?></td>
<td colspan=3></td>
</tr>
</table>

</body>
</html>

