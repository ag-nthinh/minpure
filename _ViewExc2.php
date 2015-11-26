<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc2 extends CommonBase{
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
            if(!isset($_GET['ac'])){
                exit();
            }

            $obj = new BankBook();
            if(!$total_point = $obj -> getTotalPoints($_GET['login_id'], $login_key = $this -> getLoginKey($_GET['login_id']))){

            }

?>
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen">
<html lang="ja">
<meta charset="utf-8">
<SCRIPT LANGUAGE="JavaScript">
<!--
function disp(){
    if(window.confirm('本当に良いですか？')){
        return true;
    } else{
        return false;
    }
}
-->
</SCRIPT>
<body>

<!-- 履歴一覧 -->

<div id="pointable"><img src="img/pp.png" width="27" height="27" align="left"><?php echo $total_point ?>P</div>

<!-- 交換対象 -->

<div id="pointsyousai">
<p><img src="img/banner_<?php echo $_GET['ac'] ?>.png" width="200" height="150"></p>

<p class="kpoint">
<form action="exchange3.php" method="get">
<select name="gift_id" size="4">
    <option value="0" selected>選択してください</option>

<?php
            $obj2 = new NTTCS();
            if(!$gift_data = $obj2 -> searchGiftAmounts($_GET['ac'])){
                exit();
            }
            foreach($gift_data as $key => $value){
?>
    <option value=<?php echo '"'.$key.'">'.$value['name'].' ＝＞ '.$value['point'].'P' ?></option>
<?php } ?>

</select>
<input type="hidden" name="login_id" value="<?php echo $_GET['login_id'] ?>">
<input type="hidden" name="login_pass" value="<?php echo $_GET['login_pass'] ?>">
<input type="hidden" name="version" value="<?php echo $_GET['version'] ?>">


<hr size="1" width="60%" align="center" noshade>
<p class="naiyou">●お申し込み後のキャンセルはお受け出来ませんのでご了承下さい。<br>
●お申し込み上限は1日に2回までとなりますのでご了承下さい。<br>
●場合によってはお申し込みから最大で5営業日程お待ち頂く可能性が御座いますので、ご留意ください。<br>
●お申し込み頂いたギフトコードの申請状況は、前の画面の[配布済みギフトコードの確認]からいつでもご確認頂きます。

<hr size="1" width="90%" align="center" noshade>

<input type="submit" value="交換する">

</form>
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

