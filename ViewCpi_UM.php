<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCpi_UM extends CommonBase{
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

<!-- 新規無料登録案件枠 -->
<p class="salehd">無料で貯める</p>
<div id="sale"><p>

<?php 
    if(!$p = $obj -> getAGPromotionsUM()){}
        foreach($p as $value){
?>
<a href="<?php echo $value['landing_url'] ?>">
<Table>
<Td RowSpan="3" width="70" height="70"><img src="<?php echo $value['img_url'] ?>" width="70" height="70"></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo $value['point'] ?>　<font style="background-color:<?php echo $value['status_color']?>;"><?php echo $value['status_text'] ?></font></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['advertisement_name'] ?></p></Td></Tr>
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

