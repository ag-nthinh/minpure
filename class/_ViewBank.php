<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewBank extends CommonBase{
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
     public function getList(){
        try{
            $obj = new BankBook();
            if(!$total_point = $obj -> getTotalPoints($_POST['login_id'], $login_key = $this -> getLoginKey($_POST['login_id']))){
            }


?>

<link rel="stylesheet" type="text/css" href="css/style.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- コンテンツ枠 -->
    <!-- メディアスペース -->
<p class="media"><img src="img/media.png" width="960"></p>


<!-- 履歴一覧 -->

<div id="pointable"><img src="img/pp.png" width="27" height="27" align="left"><?php echo $total_point ?>P</div>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst2" href="code.php?login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">ギフトコードの確認</a></Td>
</Table>


<h1>ポイント履歴一覧</h1>

<div id="pointsyousai">
<p>

<?php 
    if(!isset($_POST['login_id'])){
        exit();
    }
    if(!$p = $obj -> getBankBook($_POST['login_id'])){
    }
    foreach($p as $key => $value){
?>

<Table>
<Td><p class="day"><?php echo $value['date'] ?></p></Td>
<Td RowSpan="3" width="80"><p class="point"><?php echo $value['app_point'] ?>P</p></Td></Tr>
<Tr><Td width="250"><p class="pointtaitol"><?php echo $value['campaign_name'] ?></p></Td></Tr>
<Tr><Td width="250"><p class="sutaus"><?php echo $value['status'] ?></p></Td></Tr>
</Table>

<hr size="1" width="90%" align="center" noshade>

<?php }  ?>

<Table align="center" height="50">

<Td Valign="middle"><a class="inst" href="exchange.php?login_id=<?php echo $_POST['login_id'] ?>&login_pass=<?php echo $_POST['login_pass'] ?>&version=<?php echo $_POST['version'] ?>">交換する</a></Td>
</Table>

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

