<?php
require_once(dirname(__FILE__).'/../conf/ini.php');
?>

<html lang="ja">
<head>
    <meta charset="utf-8">
</head>

<?php
$object = new Timesales();
/*
if($count = $object -> getTimesalePromotionCount() >= 3){
	echo "タイムセールが既に3件登録されているため、新規登録できませんでした。<br>";
	echo "現在掲載されているタイムセールの終了後に再度登録してください。<br>";
	echo "<a href=\"promotions.php\">戻る</a>";
	exit();
}
*/
if(!$object -> setTimesalePromotion($_POST)){
	echo "データが正しくありません。もう一度やり直してください。<br>";
	echo "<a href=\"settimesale.php?id=".$_POST['affitown_advertisement_id']."\">戻る</a>";
	exit();
}
?>
 
登録が完了しました。<br>
<a href="promotions.php?timesale=1">タイムセール一覧</a>


