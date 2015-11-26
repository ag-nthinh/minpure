<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc2_CN extends CommonBase{
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
            if(!isset($_GET['login_id'], $_GET['login_pass'], $_GET['version'])){
                exit();
            }
            $obj = new BankBook();
            if(!$total_point = $obj -> getTotalPoints($_GET['login_id'], $login_key = $this -> getLoginKey($_GET['login_id']))){
            }

?>
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 交換対象 -->

<div id="pointable"><img src="img/dig.png" width="27" height="27"><?php echo number_format($total_point) ?></div>
<br>
<div id="pointsyousai">

<?php
            $obj = new CNGiftCode();
            if(!$gift_data = $obj -> getCode($_GET['login_id'])){
?>

<p class="title">申し訳ございませんが、交換することができませんでした。所持DIG数をご確認ください。</p>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst" href="exchange_cn.php?login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>">交換する</a></Td>
</Table>

<?php
            exit();
            }
?>

<p class="title">お申し込みが完了致しました。</p>
<p class="title">コード：<?php echo $gift_data['code'] ?> </p>
<p class="title">有効期限：<?php echo $gift_data['exchange_end'] ?> </p>

<!--
<p class="subtitle">※5営業日を過ぎても配布されない場合は、<br>
お手数ですが、お問い合わせフォームより<br>お問い合わせ下さい。</p>
-->

<hr size="1" width="90%" align="center" noshade>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst2" href="code_cn.php?login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>">ギフトコードの確認</a></Td>
</Table>

</div>

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

