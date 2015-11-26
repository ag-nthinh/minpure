<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpa2_UM extends CommonBase{
    /*
    コンストラクタ
    */
    public function __construct() {
        try {
             if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
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
	    $param = "login_id=".$_GET['login_id'];

?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">


<body>

<!-- 新規実質無料案件 -->
<p class="free2hd">ガッポリ稼ぐ | 実質無料で稼げる</p>
<div id="free2">

<?php 
    if(!$p2 = $obj -> getPromotions(array("type"=>2, "timesale"=>0, "status"=>1, "num"=>200, "login_id"=>$_GET['login_id']))){}
        foreach($p2 as $key => $value){
?>
<a href="detail_um.php?adid=<?php echo $value['adid'] ?>&<?php echo $param ?>">

<Table>
<Tr>
<Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point02"><font color="#888888"><?php echo $value['amount'] ?>→</font>
<img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?>獲得</p></Td></Tr>
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

