<?php
require_once(dirname(__FILE__).'/../conf/ini.php');

$object = new AffiTown();
if(!$object -> setNewPromotion($_POST)){
	echo "データが正しくありません。もう一度やり直してください。<br>";
	echo "<a href=\"setpromotion.php?id=".$_POST['affitown_advertisement_id']."\">戻る</a>";
	exit();
}

?>
<html lang="ja">
<head>
    <meta charset="utf-8">
</head>
 
登録が完了しました。<br>
<a href="promotions.php?timesale=0">広告一覧</a>


