<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpa1_CN extends CommonBase{
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


<!-- 新規無料登録案件枠 -->
<p class="freehd">ガッポリ稼ぐ | 無料で稼げる</p>
<div id="free1"><p>

<?php 
    if(!$p = $obj -> getPromotions(array("type"=>1, "timesale"=>0, "status"=>1, "num"=>200, "login_id"=>$_GET['login_id']))){}
        foreach($p as $key => $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param ?>">

<Table>
<Tr>
<Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>

<?php } ?>
</p></div>


<?php Banner::set1(); ?>

</body>

<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

