<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc_UM extends CommonBase{
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
            if(!isset($_GET['login_id'])){
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
<p>

<Table align="center">
<Tr><Td Valign="middle"><img src="img/cnpoint.jpg" width="100%"></Td></Tr>
</Table>
<Table align="center" height="50">
<Tr><Td><p class="point02"><img src="img/dig.png" width="27" height="27">500→
CNポイント1,000pt</p></Td></Tr>
</Table>
<hr size="1" width="90%" align="center" noshade>
<Table align="center" height="50">
<Tr><Td Valign="middle"><a class="btn" href="exchange2_um.php?login_id=<?php echo $_GET['login_id'] ?>">CNポイントと交換する</a></Td></Tr>
</Table>

</p>
</div>
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

