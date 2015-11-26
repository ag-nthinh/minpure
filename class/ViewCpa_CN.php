<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpa_CN extends CommonBase{
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

	    $param = "login_id=".$_POST['login_id']."&login_pass=".$_POST['login_pass']."&version=".$_POST['version'];
?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>


<!-- 新規無料登録案件枠 -->
<p class="freehd">ガッポリ稼ぐ | 無料で稼げる NEW!!</p>
<div id="free1"><p>

<?php 
    if(!$p = $obj -> getPromotions(array("type"=>1, "timesale"=>0, "status"=>1, "num"=>3, "login_id"=>$_POST['login_id']))){}
    //if(!$p = $obj -> getPromotions(1,3)){}
        foreach($p as $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param; ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa1_cn.php?<?php echo $param; ?>">もっと見る</a></Td>
</Table>
</p></div>

<?php Banner::set1(); ?>


<!-- 新規実質無料案件 -->
<p class="free2hd">ガッポリ稼ぐ | 実質無料で稼げる NEW!!</p>
<div id="free2">

<?php 
    if(!$p2 = $obj -> getPromotions(array("type"=>2, "timesale"=>0, "status"=>1, "num"=>3, "login_id"=>$_POST['login_id']))){}
    //if(!$p2 = $obj -> getPromotions(2,3)){}
        foreach($p2 as $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param; ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point02">
<font color="#888888"><?php echo $value['amount'] ?>→</font>
<img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?>獲得</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa2_cn.php?<?php echo $param; ?>">もっと見る</a></Td>
</Table>

</p></div>


<?php Banner::set2(); ?>


<!-- 新規無料登録案件枠 -->
<p class="salehd">売れ筋ピックアップ</p>
<div id="sale"><p>


<?php 
    if(!$p3 = $obj -> getPromotions(array("type"=>3, "timesale"=>0, "status"=>1, "num"=>3, "login_id"=>$_POST['login_id']))){}
    //if(!$p3 = $obj -> getPromotions(3,3)){}
        foreach($p3 as $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param; ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa3_cn.php?<?php echo $param; ?>">もっと見る</a></Td>
</Table>

</p></div>

<?php Banner::set3(); ?>


</body>

<?php 

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

