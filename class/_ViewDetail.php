<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 猿渡 俊輔
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewDetail extends CommonBase{
    /*
    コンストラクタ
    */
    public function __construct() {
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }

    /**
     * 案件を取得
     *
     * @accsess public
     */

     public function getPromotions(){
        try{
            $obj = new AffiTown();
            if(!$p = $obj -> getPromotion($_GET['adid'])){
                echo "このキャンペーンは終了しました";
                exit();
            }
            $this->getView($p);
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

     public function getTimesalePromotions(){
        try{
            $obj = new Timesales();
            if(!$p = $obj -> getPromotion($_GET['tsid'])){
                echo "このキャンペーンは終了しました";
                exit();
            }
            $this->getView($p);
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }



     private function getView($p){
        try{
?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- コンテンツ枠 -->
    <!-- メディアスペース -->
<p class="media"><img src="img/media.png" width="960"></p>


<!-- 履歴一覧 -->

<div id="shosai">
<p><Table>
<Td RowSpan="3" width="70" height="70"><?php echo $p['img'] ?></Td>
<Td><p class="nf"><?php echo $p['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/pp.png" width="27" height="27"><?php echo $p['point'] ?>P</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $p['action'] ?></p></Td></Tr>
</Table>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst" href="<?php echo $p['url'] ?>">移動する</a></Td>
</Table>

<h2 align="left">獲得条件</h2>
<p class="kakaku">価格 ： <?php echo $p['amount'] ?></p>
<p class="shosai"><?php echo $p['action'] ?></p>

<h2 align="left">概　要</h2>
<p class="shosai"><?php echo $p['description'] ?>
</p>

<h2 align="left">注意事項</h2>
<p class="shosai">
    タイムラグの関係で表示のポイント付与と実際のポイント付与数に相違が
    生じる可能性があります。</br >
    ※過去に無料会員登録されたことのある方は対象外となります。
</p>
<!-- フッダー -->

</body>

<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }

     public function getAGPromotions(){
        try{
            $obj = new AffiTown();

            if(!$p = $obj -> getAGPromotion($_POST['advertisement_id'])){
                echo "このキャンペーンは終了しました";
                exit();
            }
?>


<link rel="stylesheet" type="text/css" href="css/style.css" media="screen">

<body>

<!-- コンテンツ枠 -->
    <!-- メディアスペース -->
<p class="media"><img src="img/media.png" width="960"></p>


<!-- 履歴一覧 -->

<div id="shosai">
<p><Table>
<Td RowSpan="3" width="80" height="80"><img src="<?php echo $p['img_url'] ?>"></Td>
<Td><p class="nf"><?php echo $p['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/pp.png" width="27" height="27"><?php echo $p['point'] ?>P</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $p['remark'] ?></p></Td></Tr>
</Table>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst" href="<?php echo $p['landing_url'] ?>">移動する</a></Td>
</Table>

<h2 align="left">獲得条件</h2>
<p class="kakaku">価格 ： <?php echo $p['price'] ?></p>
<p class="shosai"><?php echo $p['remark'] ?></p>

<h2 align="left">概　要</h2>
<p class="shosai"><?php echo $p['detail'] ?>
</p>

<h2 align="left">注意事項</h2>
<p class="shosai">
    タイムラグの関係で表示のポイント付与と実際のポイント付与数に相違が
    生じる可能性があります。<br>
    ※過去に無料会員登録されたことのある方は対象外となります。
</p>
<!-- フッダー -->

</body>

<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

