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
<link href="css/hikaku.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/index.css" media="all" rel="stylesheet" type="text/css" />
</head>
<?php
  require_once(dirname(__FILE__).'/../class/dbconnect.php');
  
  try{
    $sql_all = "SELECT * FROM company_details WHERE company_details.name='".$_GET['name']."'";
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
      <h2 class="main-h2 img-h2_bg02"><i class="img-medal_no2"></i>
        <?php
          echo "<a href=\"http://click.actbank.net/click/?com=".$data['redirect_link']."&pt=497\" title=\"".$data['name']."公式サイトへ\" rel=\"nofollow\" target=\"_blank\">".$data['name']."</a><i class=\"img-logo_".$data['short_name']." main-h2-logo\"></i></h2>";
        ?>
      <div class="total-rank">
        <div class="rank_header main-rank_header">
          <div class="banner-wrap">
            <?php
              echo "<a href=\"http://click.actbank.net/click/?com=".$data['redirect_link']."&pt=497\" title=\"".$data['name']."公式サイトへ\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://image.actbank.net/banner/fx/".$data['redirect_link']."_468x60.gif\" alt=\"".$data['name']." バナー\" width=\"468\" height=\"60\"></a>";
            ?>
          </div>
          <div class="star-wrap">
            <p>FX総合力 評価点</p>
              <?php 
                if($data['point'] > 95) {
                  echo "<span><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i></span>";
                } elseif($data['point'] > 85) {
                  echo "<span><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_half'></i></span>";
                } elseif($data['point'] > 70) {
                  echo "<span><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_empty'></i></span>";
                } else {
                  echo "<span><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_full'></i><i class='img-star_half'></i><i class='img-star_empty'></i></span>";
                }
              ?>          
          </div>
          <div class="score-wrap">
            <strong><?php echo $data['point']; ?></strong><span>点</span>
          </div>
        </div><!-- /.main-rank_header -->
        <div class="total-rank_main">
          <div class="rank_main-left_column"><i class="img_radarChart_<?php echo $data['short_name'] ?>"></i></div>
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
            <?php
              echo "<a href=\"http://click.actbank.net/click/?com=".$data['redirect_link']."&pt=497\" title=\"".$data['name']."公式サイトで口座開設\" rel=\"nofollow\" target=\"_blank\"><div class=\"btn_conversion\"><span>公式サイトで口座開設</span></div></a>";
            ?>
          </div>
        </div><!-- /.total-rank_main -->
        <div class="total-rank_comment">
          <div class="total-rank_comment-inner">
            <h3><?php echo $data['name'] ?> 評価ポイントの詳細</h3>
            <p><?php echo $data['description']; ?></p>
          </div>
        </div><!-- /.total-rank_comment -->
      </div><!-- /.total-rank -->
      <a href="http://fx.minkabu.jp/hikaku/<?php echo $data['short_name'] ?>.html" class="more_link-company_page"><?php echo $data['name'] ?>の総合比較情報はこちら</a>
    </div>
  </div>
</body>