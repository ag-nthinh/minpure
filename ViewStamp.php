<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewStamp extends CommonBase{
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
     public function getPage(){
        try{
            if(!isset($_POST['login_id'], $_POST['login_pass'], $_POST['version'])){
                exit();
            }
            $login_obj = new LoginStamp();
            $login_count = $login_obj->actionLoginStamp($_POST['login_id']);

//$login_count=3;

            CreateLog::putDebugLog("login_count = ".$login_count." / ".$login_id);
	    if(!$login_count){
		header("Location: close.php");
	    }
?>

<!--
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
-->
<html lang="ja">
<meta charset="utf-8">

<body bgcolor="#880000">

<a href="close.php"><img src="img/ls0<?php echo $login_count ?>.jpg" width="100%"></a>

</body>

<?php 
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

