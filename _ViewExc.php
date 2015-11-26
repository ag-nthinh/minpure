<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc extends CommonBase{
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
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 履歴一覧 -->

<div id="pointable"><img src="img/pp.png" width="27" height="27" align="left"><?php echo $total_point ?>P</div>

<!-- 交換対象 -->

<br>
<p>
<a href="exchange2.php?ac=AGC&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_AGC.gif" width="100" higth="100"></a>
<a href="exchange2.php?ac=VPC&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_VPC.gif" width="100" higth="100"></a>
<a href="exchange2.php?ac=NAN&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_NAN.gif" width="100" higth="100"></a>
<a href="exchange2.php?ac=SPG&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_SPG.gif" width="100" higth="100"></a>
<a href="exchange2.php?ac=EGI&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_EGI.gif" width="100" higth="100"></a>
<a href="exchange2.php?ac=ITG&login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>"><img src="img/ex_ITG.gif" width="100" higth="100"></a>
</p>

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

