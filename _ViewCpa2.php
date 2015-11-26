<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpa2 extends CommonBase{
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
	    $param = "login_id=".$_GET['login_id']."&login_pass=".$_GET['login_pass']."&version=".$_GET['version'];

?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- メディアスペース -->
<p class="media"><img src="img/media.png" width="960"></p>

<!-- Formula cpa 広告ヘッダー -->
<p class="media"><img src="img/formula.png" width="320"></p>



<!-- 新規実質無料案件 -->
<p class="free2hd">新規実質無料案件</p>
<div id="free2">

<?php 
    if(!$p2 = $obj -> getPromotions(array("type"=>2, "timesale"=>0, "status"=>1, "num"=>100))){}
    //if(!$p2 = $obj -> getPromotions(2,100)){}
        foreach($p2 as $key => $value){
?>
<a href="http://minpure.com/oem/detail.php?adid=<?php echo $value['adid'] ?>&<?php echo $param ?>">

<Table>
<Tr>
<Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point02"><img src="img/pp.png" width="27" height="27"><?php echo $value['amount'] ?>→<?php echo $value['point'] ?>P獲得</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>

</p></div>

<!-- Formula広告ミドル02 -->
<p class="media"><img src="img/formula.png" width="320"></p>



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

