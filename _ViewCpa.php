<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpa extends CommonBase{
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

?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- メディアスペース -->
<p class="media"><img src="img/media.png" width="960"></p>

<!-- Formula cpa 広告ヘッダー -->
<p class="media"><img src="img/formula.png" width="320"></p>


<!-- 新規無料登録案件枠 -->
<p class="freehd">新規無料案件</p>
<div id="free1"><p>

<?php 
    if(!$p = $obj -> getPromotions(array("type"=>1, "timesale"=>0, "status"=>1, "num"=>3))){}
    //if(!$p = $obj -> getPromotions(1,3)){}
        for($i=0; $i<3; $i++){
?>
<a href="http://minpure.com/oem/detail.php?adid=<?php echo $p[$i]['adid'] ?>&login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $p[$i]['img'] ?></Td>
<Td><p class="nf"><?php echo $p[$i]['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/pp.png" width="27" height="27"><?php echo $p[$i]['point'] ?>P</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $p[$i]['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa1.php?login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">もっと見る</a></Td>
</Table>
</p></div>

<!-- Formula広告ミドル01 -->
<p class="media"><img src="img/formula.png" width="320"></p>


<!-- 新規実質無料案件 -->
<p class="free2hd">新規実質無料案件</p>
<div id="free2">

<?php 
    if(!$p2 = $obj -> getPromotions(array("type"=>2, "timesale"=>0, "status"=>1, "num"=>3))){}
    //if(!$p2 = $obj -> getPromotions(2,3)){}
        for($i=0; $i<3; $i++){
?>
<a href="http://minpure.com/oem/detail.php?adid=<?php echo $p2[$i]['adid'] ?>&login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $p2[$i]['img'] ?></Td>
<Td><p class="nf"><?php echo $p2[$i]['name'] ?></p></Td></Tr>
<Tr><Td><p class="point02">
<img src="img/pp.png" width="27" height="27"><?php echo $p2[$i]['amount'] ?>→<?php echo $p2[$i]['point'] ?>P獲得</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $p2[$i]['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa2.php?login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">もっと見る</a></Td>
</Table>

</p></div>

<!-- Formula広告ミドル02 -->
<p class="media"><img src="img/formula.png" width="320"></p>


<!-- 新規無料登録案件枠 -->
<p class="salehd">売れ筋ランキング</p>
<div id="sale"><p>


<?php 
    if(!$p3 = $obj -> getPromotions(array("type"=>3, "timesale"=>0, "status"=>1, "num"=>3))){}
    //if(!$p3 = $obj -> getPromotions(3,3)){}
        for($i=0; $i<3; $i++){
?>
<a href="http://minpure.com/oem/detail.php?adid=<?php echo $p3[$i]['adid'] ?>&login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">

<Table>
<Tr>
<Td RowSpan="3" width="90" height="45"><?php echo $p3[$i]['img'] ?></Td>
<Td><p class="nf"><?php echo $p3[$i]['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/pp.png" width="27" height="27"><?php echo $p3[$i]['point'] ?>P</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $p3[$i]['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa3.php?login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">もっと見る</a></Td>
</Table>

</p></div>


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

