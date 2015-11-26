<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewBank_UM extends CommonBase{
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
     public function getList(){
        try{
            $obj = new BankBook_UM();
            if(!$total_point = $obj -> getTotalPoints($_POST['login_id'], $login_key = $this -> getLoginKey($_POST['login_id']))){

            }


?>

<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 履歴一覧 -->

<div id="pointable"><img src="img/dig.png" width="27" height="27"><?php echo number_format($total_point) ?></div>


<h1>DIG通帳</h1>

<div id="pointsyousai">
<p>

<?php 
    if(!isset($_POST['login_id'])){
        exit();
    }
?>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst2" href="code_um.php?login_id=<?php echo $_POST['login_id'] ?>">ギフトコード交換履歴</a></Td>
</Table>

<?php
    if(!$p = $obj -> getBankBook($_POST['login_id'])){
	echo '<p class="point">DIGの獲得履歴はありません</p>';
    }
    foreach($p as $key => $value){
?>

<Table>
<Td><p class="day"><?php echo $value['date'] ?></p></Td>
<Td RowSpan="3" width="100"><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo $value['app_point'] ?></p></Td>
<Tr><Td width="200"><p class="pointtaitol"><?php echo $value['campaign_name'] ?></p></Td></Tr>
<Tr><Td width="200"><p class="sutaus"><?php echo $value['status'] ?></p></Td></Tr>
</Table>

<hr size="1" width="90%" align="center" noshade>

<?php }  ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="inst" href="exchange_um.php?login_id=<?php echo $_POST['login_id'] ?>">交換する</a></Td>
</Table>


</body>

<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

