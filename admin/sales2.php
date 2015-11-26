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
<?php require_once('header.php'); ?>
<body>

<h2><?php echo SITE_NAME ?></h2>

<a class="button" href="javascript:history.back()">戻　る</a>
<table class="table01" order=1>

<tr>
<th rowspan=2></th>
<th colspan=4>売　上</th>
<th colspan=4>ユーザ付与</th>
<th colspan=3>粗　利</th>
<th colspan=2>コード発行</th>
<th rowspan=2>預り金</th>
</tr>
<tr>
<th>通常</th>
<th>タイムセール</th>
<th>ゲーム</th>
<th>（計）</th>
<th>広告</th>
<th>ゲーム</th>
<th>ログイン</th>
<th>（計）</th>
<th></th>
<th>CN</th>
<th>AG</th>
<th>枚数</th>
<th>金額</th>
</tr>


<?php

$obj = new Sales();
$row = $obj -> countDailySales($_GET['m']);
$row2 = $obj -> countDailyExchange($_GET['m']);
$row3 = $obj -> countDailyAllPoints($_GET['m']);
foreach($row2 as $key => $value){
        $row[$key]['exchange'] = $value;
}
foreach($row3 as $key => $value){
        $row[$key]['advertisement'] = $value[0];
        $row[$key]['game'] = $value[2];
        $row[$key]['login'] = $value[5];
        $row[$key]['user_back'] = $value[0] + $value[2] + $value[5];
}
foreach($row as $key => $value){
        $total_sale = $value[0] + $value[1] + $value['game'];
        $profit = $total_sale - $value['user_back'];
        $profit_ag = floor($profit * SHARE_RATE);
        $profit_cn = $profit - $profit_ag;
        $pool = $value['user_back'] - $value['exchange'];
?>

<tr>
<td><?php echo $key; ?></td>
<td><a href="salesd.php?w=<?php echo $key ?>&t=0"><?php echo "￥".number_format($value[0]); ?></a></td>
<td><a href="salesd.php?w=<?php echo $key ?>&t=1"><?php echo "￥".number_format($value[1]); ?></a></td>
<td><?php if($value['game']) echo "￥".number_format($value['game'],1); ?></td>
<td><?php echo "￥".number_format($total_sale,1); ?></td>
<td><?php if($value['advertisement']) echo "￥".number_format($value['advertisement'],1); ?></td>
<td><?php if($value['game']) echo "￥".number_format($value['game'],1); ?></td>
<td><?php if($value['login']) echo "￥".number_format($value['login'],1); ?></td>
<td class="surplus"><?php if($value['user_back']) echo "￥".number_format($value['user_back'],1); ?></td>
<td class="surplus"><?php echo "￥".number_format($profit,1); ?></td>
<td><?php echo "￥".number_format($profit_ag,1); ?></td>
<td><?php echo "￥".number_format($profit_cn,1); ?></td>
<td><?php echo $value['exchange'] ?></td>
<td class="deficit"><?php echo "￥".number_format($value['exchange']*500); ?></td>
<td class="deficit"><?php echo "￥".number_format($pool,1); ?></td>
</tr>

<?php } ?>


</table>

</body>
</html>

