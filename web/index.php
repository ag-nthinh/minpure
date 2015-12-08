<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta name="language" content="ja" />
<meta name="robots" content="index,follow" />
<meta name="description" content="" />
<title>FXで勝つ為に！最新の人気FXを徹底比較</title>
<link href="css/common.css?x=140605" media="all" rel="stylesheet" type="text/css" />
<link href="http://fx-manual.net/" rel="canonical" />
<script type="text/javascript" src="js/jquery.min.1.5.2.js">
</script>
<script type="text/javascript" src="js/tools.js">
</script>
<script type="text/javascript" src="js/swfobject.js">
</script>
</head>
<?php
  require_once(dirname(__FILE__).'/../conf/ini.php');
  if(!$link = mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
    throw new Exception(mysql_error());
  }
  if(!mysql_select_db(DB_NAME, $link)) {
    throw new Exception(mysql_error());
  }
  if(!mysql_query("SET NAMES 'utf8'",$link)) {
    throw new Exception(mysql_error());
  }
  try{
    $sql_all = "SELECT * FROM company_details";
    if(!$result = mysql_query($sql_all, $link)){
      throw new Exception($sql_all);
    }
    $data = mysql_fetch_assoc($result);
  } catch(Exception $e){
  //  CreateLog::putErrorLog(get_class()." ".$e->getMessage());
  }
?>
<body>
<div id="wrapper">
<div id="header">
<div class="headerMain">
<h1>FXを検討中＆FX初心者のみなさま！円安トレンドの今　最大のFXチャンスを逃すな！</h1>
<p class="logo"><a href="/"><img width="451" height="57" alt="FXで勝つ為に！最新の人気FXを徹底比較" src="img/title.png" /></a></p>
</div>
<p class="headerBanner"><a title="投資家が選んだ最新FXランキング" href="/ninki/"><img width="468" height="60" border="0" src="img/banner_ranking_468x60.gif" alt="" /></a></p>
</div>
<div id="mainBox">
<div class="mainColumn">
<div class="pointSearchBox">
<h2 class="mainImage"><!-- <img width="158" height="153" alt="" src="img/img_top_mainimage1.jpg" /> --> <img width=auto height=auto alt="4つのポイントから選ぶFX会社選び" src="img/search.jpeg"/></h2>
<ul>
<li><a href="/type/small/"><img width="165" height="92" alt="少額スタート" src="img/img_top_point_search_btn_small.gif" class="imgRo" /></a></li>
<li><a href="/type/useful/"><img width="165" height="92" alt="使いやすさ重視" src="img/img_top_point_search_btn_useful.gif" class="imgRo" /></a></li>
<li><a href="/type/beginner/"><img width="165" height="92" alt="初心者で安心重視" src="img/img_top_point_search_btn_beginner.gif" class="imgRo" /></a></li>
<li><a href="/type/campaign/"><img width="165" height="92" alt="キャンペーンおトク度重視" src="img/img_top_point_search_btn_campaign.gif" class="imgRo" /></a></li>
</ul>
</div>
<div class="searchSetting add">
<h3 class="title"><img width="239" height="44" alt="FX会社を詳しく検索" src="img/img_search_form_title.gif" /></h3>
<form method="get" action="/search/" name="sform" id="sform">
<div class="inner">
<table class="searchTable">
<tbody>
<tr class="pairs">
<th><span>取引通貨ペア</span></th>
<td>
<p class="pair"><label for="pairs-1"><input type="checkbox" id="pairs-1" name="pairs[]" value="usdjpy" />米ドル/円</label> <label for="pairs-2"><input type="checkbox" id="pairs-2" name="pairs[]" value="audjpy" />豪ドル/円</label> <label for="pairs-3"><input type="checkbox" id="pairs-3" name="pairs[]" value="eurjpy" />ユーロ/円</label> <label for="pairs-4"><input type="checkbox" id="pairs-4" name="pairs[]" value="gbpjpy" />ポンド/円</label> <label for="pairs-5"><input type="checkbox" id="pairs-5" name="pairs[]" value="nzdjpy" />NZドル/円</label> <label for="pairs-6"><input type="checkbox" id="pairs-6" name="pairs[]" value="eurusd" />ユーロ/米ドル</label> <label for="pairs-7"><input type="checkbox" id="pairs-7" name="pairs[]" value="chfjpy" />スイスフラン/円</label> <label for="pairs-8"><input type="checkbox" id="pairs-8" name="pairs[]" value="zarjpy" />南アランド/円</label></p>
</td>
</tr>
<tr class="smartphone">
<th><span>スマホ取引対応</span></th>
<td>
<p class="chumon"><label for="smartphone-1"><input type="checkbox" id="smartphone-1" name="smartphone[]" value="iphone" />iPhone</label> <label for="smartphone-2"><input type="checkbox" id="smartphone-2" name="smartphone[]" value="android" />Android</label></p>
</td>
</tr>
<tr class="collapsibleRow pairsCount">
<th><span>通貨ペア数</span></th>
<td>
<p class="chumon"><label for="pairsCount-1"><input type="radio" id="pairsCount-1" name="pairsCount" value="0" checked="checked" />指定しない</label> <label for="pairsCount-2"><input type="radio" id="pairsCount-2" name="pairsCount" value="15" />15種類～</label> <label for="pairsCount-3"><input type="radio" id="pairsCount-3" name="pairsCount" value="20" />20種類～</label></p>
</td>
</tr>
<tr class="collapsibleRow tradingUnit">
<th><span>最小取引単位</span></th>
<td>
<p class="chumon"><label for="tradingUnit-1"><input type="radio" id="tradingUnit-1" name="tradingUnit" value="0" checked="checked" />指定しない</label> <label for="tradingUnit-2"><input type="radio" id="tradingUnit-2" name="tradingUnit" value="1000" />1,000通貨</label></p>
</td>
</tr>
<tr class="collapsibleRow orderfunc">
<th><span>注文機能</span></th>
<td>
<p class="chumon"><label for="orderfunc-1"><input type="checkbox" id="orderfunc-1" name="orderfunc[]" value="1" />成行</label> <label for="orderfunc-2"><input type="checkbox" id="orderfunc-2" name="orderfunc[]" value="2" />指値</label> <label for="orderfunc-3"><input type="checkbox" id="orderfunc-3" name="orderfunc[]" value="3" />逆指値</label> <label for="orderfunc-4"><input type="checkbox" id="orderfunc-4" name="orderfunc[]" value="4" />OCO</label> <label for="orderfunc-5"><input type="checkbox" id="orderfunc-5" name="orderfunc[]" value="5" />IFD</label> <label for="orderfunc-6"><input type="checkbox" id="orderfunc-6" name="orderfunc[]" value="6" />IFO</label> <label for="orderfunc-7"><input type="checkbox" id="orderfunc-7" name="orderfunc[]" value="7" />期日指定</label> <label for="orderfunc-8"><input type="checkbox" id="orderfunc-8" name="orderfunc[]" value="8" />トレール</label></p>
</td>
</tr>
<tr class="collapsibleRow tradefunc">
<th><span>取引機能</span></th>
<td>
<p class="chumon"><label for="tradefunc-1"><input type="checkbox" id="tradefunc-1" name="tradefunc[]" value="1" />マージンコール</label> <label for="tradefunc-2"><input type="checkbox" id="tradefunc-2" name="tradefunc[]" value="2" />ロスカット</label> <label for="tradefunc-5"><input type="checkbox" id="tradefunc-5" name="tradefunc[]" value="5" />約定通知</label></p>
</td>
</tr>
<tr class="collapsibleRow fee">
<th><span>取引手数料</span></th>
<td>
<p class="chumon"><label for="nonfee"><input type="checkbox" id="nonfee" name="nonfee" value="1" />無料</label></p>
</td>
</tr>
</tbody>
</table>
</div>
<ul class="collapsibleSwitch">
<li class="add"><a href="javascript:void;">検索条件を追加する</a></li>
<li class="close"><a href="javascript:void;">検索条件を閉じる</a></li>
</ul>
<p class="submit"><input type="image" name="submit" id="submit" src="img/btn_search_submit.gif" alt="この条件でFX会社を検索" class="imgRo" /></p>
</form>
</div>
<p class="banner"><a href="/ninki/"><img width="670" height="86" alt="総合人気FXランキング" src="img/banner_ranking_670x86.gif" /> <span>どこのFX口座を選ぶか迷っているなら、「現役トレーダーの評価」をそのままいただいちゃいましょう！</span></a></p>
<div class="itemSortBlock">
<table class="itemSortTable">
<tbody>
<tr class="headingFirst">
<th rowspan="2">FX会社</th>
<th colspan="4">スプレッド</th>
<th rowspan="2">キャッシュ<br />
バック<br />
金額</th>
<th rowspan="2">&nbsp;</th>
</tr>
<tr class="headingSecond">
<th>
<p>米ドル<br />
/円</p>
<span class="sortIcon"><a href="javascript:void(0);" class="ascend" data-query="order=3-usdjpy&amp;sort=asc"><img width="19" height="13" alt="昇順" src="img/btn_sort_ascend.gif" /></a> <a href="javascript:void(0);" class="ascend" data-query="order=3-usdjpy&amp;sort=desc"><img width="19" height="13" alt="降順" src="img/btn_sort_descend.gif" /></a></span></th>
<th>
<p>ユーロ<br />
/円</p>
<span class="sortIcon"><a href="javascript:void(0);" class="ascend" data-query="order=3-eurjpy&amp;sort=asc"><img width="19" height="13" alt="昇順" src="img/btn_sort_ascend.gif" /></a> <a href="javascript:void(0);" class="ascend" data-query="order=3-eurjpy&amp;sort=desc"><img width="19" height="13" alt="降順" src="img/btn_sort_descend.gif" /></a></span></th>
<th>
<p>ユーロ<br />
/米ドル</p>
<span class="sortIcon"><a href="javascript:void(0);" class="ascend" data-query="order=3-eurusd&amp;sort=asc"><img width="19" height="13" alt="昇順" src="img/btn_sort_ascend.gif" /></a> <a href="javascript:void(0);" class="ascend" data-query="order=3-eurusd&amp;sort=desc"><img width="19" height="13" alt="降順" src="img/btn_sort_descend.gif" /></a></span></th>
<th>
<p>豪ドル<br />
/円</p>
<span class="sortIcon"><a href="javascript:void(0);" class="ascend" data-query="order=3-audjpy&amp;sort=asc"><img width="19" height="13" alt="昇順" src="img/btn_sort_ascend.gif" /></a> <a href="javascript:void(0);" class="ascend" data-query="order=3-audjpy&amp;sort=desc"><img width="19" height="13" alt="降順" src="img/btn_sort_descend.gif" /></a></span></th>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/clicksec/"><img width="120" height="60" alt="GMOクリック証券" src="banner/clicksec_120x60.gif" /><br />
<span>GMOクリック証券</span></a></p>
</td>
<td>0.3銭<em class="note">原則固定</em></td>
<td>0.6銭<em class="note">原則固定</em></td>
<td>0.5P<em class="note">原則固定</em></td>
<td>0.7銭<em class="note">原則固定</em></td>
<td>最大<br />
5,000円</td>
<td>
<p class="button"><a target="_self" href="spec/clicksec/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/gaitameonline/"><img width="120" height="60" alt="外為オンライン" src="banner/gaitameonline_120x60.gif" /><br />
<span>外為オンライン</span></a></p>
</td>
<td>1銭<em class="note">原則固定</em></td>
<td>2銭<em class="note">原則固定</em></td>
<td>1P<em class="note">原則固定</em></td>
<td>3銭<em class="note">原則固定</em></td>
<td>最大<br />
5,000円</td>
<td>
<p class="button"><a target="_self" href="spec/gaitameonline/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/cyberagentfx/"><img width="120" height="60" alt="ワイジェイFX" src="banner/cyberagentfx_120x60.gif" /><br />
<span>ワイジェイFX</span></a></p>
</td>
<td>0.3銭<em class="note">原則固定</em><em class="note">※期間限定</em></td>
<td>0.7銭<em class="note">原則固定</em><em class="note">※期間限定</em></td>
<td>0.6P<em class="note">原則固定</em><em class="note">※期間限定</em></td>
<td>0.8銭<em class="note">原則固定</em><em class="note">※期間限定</em></td>
<td>最大<br />
24,000円</td>
<td>
<p class="button"><a target="_self" href="spec/cyberagentfx/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/hirosefx/"><img width="120" height="60" alt="ヒロセ通商 LION FX" src="banner/hirosefx_120x60.gif" /><br />
<span>ヒロセ通商 LION FX</span></a></p>
</td>
<td>0.3銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.6銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.5P<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.8銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>最大<br />
10,000円</td>
<td>
<p class="button"><a target="_self" href="spec/hirosefx/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/minfx/"><img width="120" height="60" alt="みんなのFX" src="banner/minfx_120x60.gif" /><br />
<span>みんなのFX</span></a></p>
</td>
<td>0.3銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.6銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.5P<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.7銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>最大<br />
10,000円</td>
<td>
<p class="button"><a target="_self" href="spec/minfx/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
<tr>
<td class="companyCol">
<p class="companyName"><a target="_self" href="spec/gaitamecom/"><img width="120" height="60" alt="外為どっとコム" src="banner/gaitamecom_120x60.gif" /><br />
<span>外為どっとコム</span></a></p>
</td>
<td>0.3銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.6銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.5P<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>0.7銭<em class="note">原則固定</em><em class="note">※例外あり</em></td>
<td>最大<br />
30,000円</td>
<td>
<p class="button"><a target="_self" href="spec/gaitamecom/"><img width="76" height="41" alt="詳細" src="img/img_detail_button_large_76x41.gif" class="imgRo" /></a></p>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<div class="subColumn">
<div class="news">
<p class="title"><img width="250" height="35" alt="注目ニュース" src="img/img_news_sub_title.gif" /></p>
<ul>
<li><a title="外為オンライン" href="spec/gaitameonline/">外為オンライン</a>
<p>米ドル円スプレッド１銭固定＋高スワップ、完全信託保全の安心感も加わりとても人気があります。今なら5,000円キャッシュバックキャンペーン実施中！</p>
</li>
</ul>
</div>
<div class="fxCategory">
<p class="title"><img width="250" height="35" alt="FXポイント別比較" src="img/img_hikaku_menu_sub_title.gif" /></p>
<ul>
<li class="total"><a href="/compare/total/" title="総合力で比較">総合力で比較</a></li>
<li class="usability"><a href="/compare/usability/" title="使いやすさで比較">使いやすさで比較</a></li>
<li class="spread"><a href="/compare/spread/" title="スプレッドで比較">スプレッドで比較</a></li>
<li class="fee"><a href="/compare/fee/" title="手数料で比較">手数料で比較</a></li>
<li class="order"><a href="/compare/order/" title="注文機能で比較">注文機能で比較</a></li>
</ul>
</div>
<div class="faqMenuSub">
<p class="title"><img width="250" height="49" alt="FXビギナーのよくある質問" src="img/img_faq_menu_sub_title.gif" /></p>
<ul>
<li><a href="/etc/faq/#q1">投資は初めてなのですが、FXで利益を出すのは難しいですか？</a></li>
<li><a href="/etc/faq/#q2">FXをやってみたいのですが、初期投資資金ってどのくらい？</a></li>
</ul>
<p class="button"><a href="/etc/faq/"><img width="130" height="34" alt="もっと見る" src="img/btn_faq_menu_sub_button.gif" class="imgRo" /></a></p>
</div>
<div class="banner">
<div id="swf"><a href="http://www.adobe.com/go/getflashplayer" target="_blank"><img src="img/img_no_flash.gif" width="250" height="200" alt="Flash Playerのインストール" /></a></div>
<script type="text/javascript" xml:space="preserve">
//<![CDATA[
$(function(){
var so = new SWFObject("/swf/tradermanual_fortune_04.swf", "tradermanual_fortune", "250", "200", "11", "#FFFFFF");
so.addParam("allowScriptAccess", "always");
so.addParam("quality", "high");
so.write("swf");
});
//]]>
</script></div>
<div class="columnMenuSub">
<p class="title"><img width="250" height="35" alt="FXコラム" src="img/img_column_menu_sub_title.gif" /></p>
<ul>
<li><a href="/column/1/" title="アベノミクスの波に乗れ！怒涛の為替市場を攻める"><img width="50" height="50" alt="アベノミクスの波に乗れ！怒涛の為替市場を攻める" src="img/img_column_menu_sub_thumb1.gif" /> <span>アベノミクスの波に乗れ！</span><br />
怒涛の為替市場を攻める</a></li>
<li><a href="/column/2/" title="全力予測！これから始めるFXの利益予想！"><img width="50" height="50" alt="全力予測！これから始めるFXの利益予想！" src="img/img_column_menu_sub_thumb2.gif" /> 全力予測！これから始める<br />
<span>FXの利益予想！</span></a></li>
<li><a href="/column/3/" title="そのFXは大丈夫か？FX会社を取引前に総点検する"><img width="50" height="50" alt="そのFXは大丈夫か？FX会社を取引前に総点検する" src="img/img_column_menu_sub_thumb3.gif" /> そのFXは大丈夫か？FX会社を<br />
<span>取引前に総点検する</span></a></li>
</ul>
</div>
</div>
<p class="localLink"><a href="#header"><span>このページのトップへ</span></a></p>
<div class="companyList">
<p><span class="bold">FX会社一覧</span><span class="small">こちらから資料請求することができます。資料請求・口座開設は何社でも一切無料です！</span></p>
<ul>
<li><a href="spec/clicksec/"><img width="115" height="57" alt="GMOクリック証券" src="banner/clicksec_115x57.gif" /></a></li>
<li><a href="spec/gaitameonline/"><img width="115" height="57" alt="外為オンライン" src="banner/gaitameonline_115x57.gif" /></a></li>
<li><a href="spec/cyberagentfx/"><img width="115" height="57" alt="ワイジェイFX" src="banner/cyberagentfx_115x57.gif" /></a></li>
<li><a href="spec/hirosefx/"><img width="115" height="57" alt="ヒロセ通商 LION FX" src="banner/hirosefx_115x57.gif" /></a></li>
<li><a href="spec/minfx/"><img width="115" height="57" alt="みんなのFX" src="banner/minfx_115x57.gif" /></a></li>
<li><a href="spec/gaitamecom/"><img width="115" height="57" alt="外為どっとコム" src="banner/gaitamecom_115x57.gif" /></a></li>
</ul>
</div>
</div>
<!-- <div id="footer">
<p class="logo"><a href="/"><img src="img/img_footer_logo.gif" alt="FXで勝つ為に！最新の人気FXを徹底比較" height="57" width="199" /></a></p>
<div class="footerMenu">
<div class="search">
<p>こだわりの条件でFXを選ぶ</p>
<ul>
<li><a href="/type/small/" title="少額スタート">少額スタート</a></li>
<li><a href="/type/useful/" title="使いやすさ重視">使いやすさ重視</a></li>
<li><a href="/type/beginner/" title="初心者で安心重視">初心者で安心重視</a></li>
<li><a href="/type/campaign/" title="キャンペーンおトク度重視">キャンペーンおトク度重視</a></li>
</ul>
</div>
<div class="hikaku">
<p>FXポイント別で比較</p>
<ul>
<li><a href="/compare/total/" title="総合力で比較">総合力で比較</a></li>
<li><a href="/compare/usability/" title="使いやすさで比較">使いやすさで比較</a></li>
<li><a href="/compare/spread/" title="スプレッドで比較">スプレッドで比較</a></li>
<li><a href="/compare/fee/" title="手数料で比較">手数料で比較</a></li>
<li><a href="/compare/order/" title="注文機能で比較">注文機能で比較</a></li>
</ul>
</div>
<div class="column">
<p>FXコラム</p>
<ul>
<li><a href="/column/1/" title="アベノミクスの波に乗れ！怒涛の為替市場を攻める"><span>アベノミクスの波に乗れ！</span><br />
怒涛の為替市場を攻める</a></li>
<li><a href="/column/2/" title="全力予測！これから始めるFXの利益予想！">全力予測！これから始める<br />
<span>FXの利益予想！</span></a></li>
<li><a href="/column/3/" title="そのFXは大丈夫か？FX会社を取引前に総点検する">そのFXは大丈夫か？FX会社を<br />
<span>取引前に総点検する</span></a></li>
</ul>
</div>
<div class="other">
<ul>
<li><a href="/enquete/" title="アンケート" class="conpany">アンケート</a></li>
<li><a href="/etc/faq/" title="よくある質問">よくある質問</a></li>
<li><a href="/ninki/">FXランキング</a></li>
<li><a href="/etc/company/" title="運営会社情報">運営会社情報</a></li>
</ul>
</div>
</div>
<p class="copy">Copyright(C)&nbsp;2015&nbsp;FXで勝つ為に！最新の人気FXを徹底比較</p>
</div> -->
</div>
<div style=" height: 1px; padding: 0px; margin: 0px; display: block; overflow: hidden;"><script language="JavaScript" type="text/javascript" xml:space="preserve">
//<![CDATA[
<!--
document.write("<img width=1 height=1 src=https://www.actbank.net/conv/?g=3&s=304&r=" + escape(document.referrer) + "&l=" + escape(document.location) + ">");
// -->
//]]>
</script> <script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 990342224;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script> <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script> <noscript>
<div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/990342224/?value=0&amp;guid=ON&amp;script=0" /></div>
</noscript></div>
</body>
</html>

