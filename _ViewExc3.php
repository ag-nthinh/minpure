<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc3 extends CommonBase{
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
            if(!isset($_GET['login_id'], $_GET['login_pass'], $_GET['version'], $_GET['gift_id'])){
                exit();
            }

?>

<link rel="stylesheet" type="text/css" href="css/style.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 履歴一覧 -->

<div id="pointable"><img src="img/pp.png" width="27" height="27" align="left"><?php echo $total_point ?>P</div>

<div id="pointsyousai">


<?php
            $obj = new NTTCS();
            if(!$gift_data = $obj -> sendGift($_GET['login_id'], $_GET['gift_id'])){

?>

<p class="title">エラーが発生しました。申し訳ございません。お手数ですが初めからやり直してください。</p>

<Table align="center" height="50">
<Td Valign="middle"><a class="inst" href="exchange.php?login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>">交換する</a></Td>
</Table>

<?php
            exit();
            } 

?>

<p class="title">お申し込みが完了致しました。</p>
<p class="title">コード：<?php echo $gift_data['code'] ?> </p>
<p class="title">有効期限：<?php echo $gift_data['exchange_end'] ?> </p>

<hr size="1" width="90%" align="center" noshade>

<Table align="center" height="50">

<Td Valign="middle"><a class="inst2" href="code.php?login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>">ギフトコードの確認</a></Td>
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

