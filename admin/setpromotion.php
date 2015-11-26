<?php 
    require_once(dirname(__FILE__).'/../conf/ini.php');

    $is_new = true;
    if($_GET['id']){
	$is_new = false;
	$obj = new AffiTown();
	if(!$data = $obj -> getPromotionById($_GET['id'])){
		$is_new = true;
	}

    }

?>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>通常案件新規登録</title>
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

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />
    <script type="text/javascript">
    $(function(){
		$.datepicker.setDefaults($.datepicker.regional['ja']);
		$("#date1").datepicker({ dateFormat: "yy-mm-dd 00:00:00" });
		$("#date2").datepicker({ dateFormat: "yy-mm-dd 23:59:59" });
    });
    </script>

	<link rel="shortcut icon" href="img/favicon.ico">
</head>
<body>

<?php require_once('header.php'); ?>


<?php if($is_new) { ?>


<h2>通常案件新規登録</h2>
<br>

<form name="frm" method="post" action="setpromotion_end.php">

<h3>AffiTown広告ID</h3>
<p><input name="adid" id="adid" type="text" value="" style="width:80px; height:30px;"></p>
<h3>広告名</h3>
<p><input name="name" id="name" type="text" style="width:600px; height:30px;" value=""></p>
<h3>商品価格</h3>
<p>￥<input name="amount" id="amount" type="text" style="width:80px; height:30px;" value="">
<select name="tax" id="tax" style="width:100px; height:30px;">
<option value="1">税抜</option>
<option value="2">税込</option>
</select></p>

<h3>報酬単価</h3>
<p>￥<input name="unit_price" id="unit_price" type="text" style="width:80px; height:30px;" value=""> </p>

<h3>獲得条件（成果地点）</h3>
<p><select name="action_point" id="action_point" style="width:200px; height:30px;">
<option value="01">無料会員登録</option>
<option value="02">有料会員登録</option>
<option value="03">キャンペーン・懸賞応募</option>
<option value="04">アンケート回答</option>
<option value="05">資料請求</option>
<option value="06">サンプル申し込み</option>
<option value="07">商品購入</option>
<option value="09">初回商品購入</option>
<option value="10">ショップ新規購入</option>
<option value="08">クレジットカード申し込み</option>
<option value="19">口座開設</option>
<option value="11">面談完了</option>
<option value="12">面談申込</option>
<option value="13">来店予約</option>
<option value="14">来店</option>
<option value="16">アプリインストール</option>
<option value="17">その他</option>
</select></p>

<h3>掲載期間</h3>
<p>開始：<input name="start_time" id="date1" type="text" class="form-control" style="width:200px; height:30px;" value="<?php echo date("Y-m-d H:i:s") ?>"></p>
<p>終了：<input name="end_time" id="date2" type="text" class="form-control" style="width:200px; height:30px;" value="2038-01-01 00:00:00">　無期限のときは 「2038-01-01 00:00:00」</p>


<h3>成果承認期間</h3>
<p><select name="approval_span" id="approval_span" style="width:100px; height:30px;">
<option value="1">全承認（即時）</option>
<option value="2">7日</option>
<option value="3">15日</option>
<option value="4">30日</option>
<option value="5">60日</option>
<option value="6">90日</option>
</select></p>

<h3>画像タグ</h3>
<p><input name="img" id="img" type="text" style="width:800px; height:30px;" value=""></p>
<p>例）&lt;img src='http://tad.atown.jp/adserver/banner/b?id=290' alt='リニューアルテスト広告（SP）'&gt;</p>
<h3>リンク先URL</h3>
<p><input name="url" id="url" type="text" style="width:800px; height:30px;" value=""></p>


<h3>概要（広告説明文）</h3>
<p><textarea name="description" style="width:400px; height:200px;"></textarea></p>

<h3>注意事項</h3>
<p><textarea name="note" style="width:400px; height:200px;"></textarea></p>

<h3>ステータス</h3>
<p><select name="status" id="status" style="width:100px; height:30px;">
<option value="1">掲載する</option>
<option value="0">掲載しない</option>
</select></p>
<p>ステータスが「掲載する」かつ掲載期間に現在日時が含まれる場合に表示されます。</p>

<input name="unique_check" id="unique_check" type="hidden" value="1">
<input name="price_type" id="price_type" type="hidden" value="1">
<input name="timesale" id="timesale" type="hidden" value="0">
<input name="timesale_point" id="timesale_point" type="hidden" value="0">

<p><input type="submit" class="btn btn-primary" value="登録する"> <input type="button" class="btn btn-primary" value="キャンセル" onClick="history.back()"></p>

</fieldset>
</form>


<?php } else { ?>

<?php if($data['timesale']) { ?>
<h2>タイムセール案件修正</h2>
<?php } else { ?>
<h2>通常案件修正</h2>
<?php } ?>

<br>
<p>ID:<?php echo $data['affitown_advertisement_id'] ?></p>

<form name="frm" method="post" action="uppromotion_end.php">

<h3>AffiTown広告ID</h3>
<p><input name="adid" id="adid" type="text" value="<?php echo $data['adid'] ?>" style="width:80px; height:30px;"></p>

<h3>広告名</h3>
<p><input name="name" id="name" type="text" value="<?php echo $data['name'] ?>" style="width:600px; height:30px;"></p>

<h3>商品価格</h3>
<p>￥<input name="amount" id="amount" type="text" value="<?php echo $data['amount'] ?>" style="width:80px; height:30px;">
<select name="tax" id="tax" style="width:100px; height:30px;">
<?php $tx[$data['tax']] = "selected"; ?>
<option value="1" <?php echo $tx[1] ?>>税抜</option>
<option value="2" <?php echo $tx[2] ?>>税込</option>
</select></p>

<h3>報酬単価</h3>
<p>￥<input name="unit_price" id="unit_price" type="text" value="<?php echo $data['unit_price'] ?>" style="width:80px; height:30px;"></p>
<input name="timesale" id="timesale" type="hidden" value="<?php echo $data['timesale'] ?>">
<h3>ユーザー付与数</h3>
<p><input name="timesale_point" id="timesale_point" type="text" value="<?php echo $data['timesale_point'] ?>" style="width:80px; height:30px;">DIG</p>
<p>￥1＝10DIG</p>
<p>報酬単価設定￥<?php echo $data['unit_price']."に対しデフォルトユーザー付与設定70% ".($data['unit_price']*7)."DIG（￥".number_format($data['unit_price']*0.7,1)."相当）"; ?></p>


<h3>獲得条件（成果地点）</h3>
<p><select name="action_point" id="action_point" style="width:200px; height:30px;">
<?php $ap[$data['action_point']] = "selected"; ?>
<option value="01" <?php echo $ap[01] ?>>無料会員登録</option>
<option value="02" <?php echo $ap[02] ?>>有料会員登録</option>
<option value="03" <?php echo $ap[03] ?>>キャンペーン・懸賞応募</option>
<option value="04" <?php echo $ap[04] ?>>アンケート回答</option>
<option value="05" <?php echo $ap[05] ?>>資料請求</option>
<option value="06" <?php echo $ap[06] ?>>サンプル申し込み</option>
<option value="07" <?php echo $ap[07] ?>>商品購入</option>
<option value="09" <?php echo $ap[09] ?>>初回商品購入</option>
<option value="10" <?php echo $ap[10] ?>>ショップ新規購入</option>
<option value="08" <?php echo $ap[08] ?>>クレジットカード申し込み</option>
<option value="19" <?php echo $ap[19] ?>>口座開設</option>
<option value="11" <?php echo $ap[11] ?>>面談完了</option>
<option value="12" <?php echo $ap[12] ?>>面談申込</option>
<option value="13" <?php echo $ap[13] ?>>来店予約</option>
<option value="14" <?php echo $ap[14] ?>>来店</option>
<option value="16" <?php echo $ap[16] ?>>アプリインストール</option>
<option value="17" <?php echo $ap[17] ?>>その他</option>
</select></p>

<h3>掲載期間</h3>
<p>開始：<input name="start_time" id="date1" type="text" class="form-control" style="width:200px; height:30px;" value="<?php echo $data['start_time']; ?>"></p>
<p>終了：<input name="end_time" id="date2" type="text" class="form-control" style="width:200px; height:30px;" value="<?php echo $data['end_time']; ?>">　無期限のときは 「2038-01-01 00:00:00」</p>
<?php if($data['timeale']) { ?>
<p>タイムセール案件の掲載期間設定は成果承認後の識別に用いるので、掲載期間にブランクがある場合または付与数を途中変更する場合、掲載終了日時を現在日時に設定して掲載を終了させ、新規タイムセール案件を作成してください。</p>
<?php } ?>

<h3>成果承認期間</h3>
<p><select name="approval_span" id="approval_span" style="width:100px; height:30px;">
<?php $as[$data['approval_span']] = "selected"; ?>
<option value="1" <?php echo $as[1] ?>>全承認（即時）</option>
<option value="2" <?php echo $as[2] ?>>7日</option>
<option value="3" <?php echo $as[3] ?>>15日</option>
<option value="4" <?php echo $as[4] ?>>30日</option>
<option value="5" <?php echo $as[5] ?>>60日</option>
<option value="6" <?php echo $as[6] ?>>90日</option>
</select></p>

<h3>成果ユニーク</h3>
<p><select name="unique_check" id="unique_check" style="width:100px; height:30px;">
<?php $uc[$data['unique_check']] = "selected"; ?>
<option value="1" <?php echo $uc[1] ?>>なし</option>
<option value="2" <?php echo $uc[2] ?>>あり</option>
</select></p>

<h3>画像タグ</h3>
<p><input name="img" id="img" type="text" value="<?php echo $data['img'] ?>" style="width:800px; height:30px;"></p>
<p>例）&lt;img src='http://tad.atown.jp/adserver/banner/b?id=290' alt='リニューアルテスト広告（SP）'&gt;</p>
<br>
<h3>リンク先URL</h3>
<p><input name="url" id="url" type="text" value="<?php echo $data['url'] ?>" style="width:800px; height:30px;"></p>

<h3>概要（広告説明文）</h3>
<p><textarea name="description" style="width:400px; height:200px;"><?php echo $data['description'] ?></textarea></p>

<h3>注意事項</h3>
<p><textarea name="note" style="width:400px; height:200px;"><?php echo $data['note'] ?></textarea></p>

<h3>ステータス</h3>
<p><select name="status" id="status" style="width:100px; height:30px;">
<?php $st[$data['status']] = "selected"; ?>
<option value="1" <?php echo $st[1] ?>>掲載する</option>
<option value="0" <?php echo $st[0] ?>>掲載しない</option>
</select></p>
<p>ステータスが「掲載する」かつ掲載期間に現在日時が含まれる場合に表示されます。</p>

<input name="affitown_advertisement_id" id="affitown_advertisement_id" type="hidden" value="<?php echo $data['affitown_advertisement_id'] ?>">
<input name="unique_check" id="unique_check" type="hidden" value="<?php echo $data['unique_check'] ?>">
<input name="price_type" id="price_type" type="hidden" value="<?php echo $data['price_type'] ?>">

<p><input type="submit" class="btn btn-primary" value="登録する"> <input type="button" class="btn btn-primary" value="キャンセル" onClick="history.back()"></p>

</fieldset>
</form>


<?php } ?>

</body>
</html>
