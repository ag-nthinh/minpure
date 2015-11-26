<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewHeader_CN extends CommonBase{
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
     public function getView(){
        try{
	    $param = "login_id=".$_POST['login_id']."&login_pass=".$_POST['login_pass']."&version=".$_POST['version'];

?>
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<link rel="stylesheet" type="text/css" href="css/style_back.css" media="screen">
<html lang="ja">
<head>
<meta charset="utf-8">
</head>

<body background="img/header.jpg" width="100%">
<br>
<p align="left"><img src="img/title.png" width="38%"></p>
<br>

<?php Banner::setHeader(); ?>

</body>
</html>
<?php 

            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage);
            return false;
        }
     }
}

