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
<link href="../../css/hikaku.css" rel="stylesheet" type="text/css" media="all" />
<link href="../../css/index.css" media="all" rel="stylesheet" type="text/css" />
</head>
<?php
  require_once(dirname(__FILE__).'/../../../conf/ini.php');
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
    $sql_all = "SELECT * FROM company_details WHERE company_details.url='clicksec'";
    if(!$result = mysql_query($sql_all, $link)){
      throw new Exception($sql_all);
    }
    $data = mysql_fetch_assoc($result);
  } catch(Exception $e){
    //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
  }
?>
<body id="hikakuBody" class="index">
  <div id="contents">
    <div id="main">
      <h2 class="main-h2 img-h2_bg02"><i class="img-medal_no2"></i><a href="http://click.actbank.net/click/?com=clicksec&pt=497" title="GMOクリック証券 公式サイトへ" rel="nofollow" target="_blank">GMOクリック証券</a><i class="img-logo_gmo main-h2-logo"></i></h2>
      <div class="total-rank">
        <div class="rank_header main-rank_header">
          <div class="banner-wrap">
            <a href="http://click.actbank.net/click/?com=clicksec&pt=497" title="GMOクリック証券 公式サイトへ" rel="nofollow" target="_blank"><img src="http://image.actbank.net/banner/fx/clicksec_468x60.gif" alt="GMOクリック証券 バナー" width="468" height="60"></a>
          </div>
          <div class="star-wrap">
            <p>FX総合力 評価点</p>
            <span><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_half'></i></span>
          </div>
          <div class="score-wrap">
            <strong><?php echo $data['point']; ?></strong><span>点</span>
          </div>
        </div><!-- /.main-rank_header -->
        <div class="total-rank_main">
          <div class="rank_main-left_column"><i class="img_radarChart_gmo"></i></div>
          <div class="rank_main-right_column">
            <table class="total-rank_point_table">
              <caption>評価ポイント</caption>
              <tr>
                <th><i class="img-point1"></i></th>
                <td><?php echo $data['point_text1']; ?></td>
              </tr>
              <tr>
                <th><i class="img-point2"></i></th>
                <td><?php echo $data['point_text2']; ?></td>
              </tr>
              <tr>
                <th><i class="img-point3"></i></th>
                <td><?php echo $data['point_text3']; ?></td>
              </tr>
            </table>
            <a href="http://click.actbank.net/click/?com=clicksec&pt=497" title="GMOクリック証券 公式サイトで口座開設" rel="nofollow" target="_blank"><div class="btn_conversion"><span>公式サイトで口座開設</span></div></a>
          </div>
        </div><!-- /.total-rank_main -->
        <div class="total-rank_comment">
          <div class="total-rank_comment-inner">
            <h3>GMOクリック証券 評価ポイントの詳細</h3>
            <p><?php echo $data['description']; ?></p>
          </div>
        </div><!-- /.total-rank_comment -->
      </div><!-- /.total-rank -->
      <a href="http://fx.minkabu.jp/hikaku/gmo.html" class="more_link-company_page">GMOクリック証券の総合比較情報はこちら</a>
    </div>
  </div>
</body>