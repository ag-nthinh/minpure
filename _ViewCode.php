<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewCode extends CommonBase{
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

<p class="media"><img src="img/media.png" width="960"></p>

<!-- 履歴一覧 -->

<div id="pointable"><img src="img/pp.png" width="27" height="27" align="left"><?php echo $total_point ?>P</div>

<h1>ギフト交換履歴一覧</h1>

<div id="pointsyousai">


<?php
            $obj = new NTTCS();
            if(!$code_data = $obj -> searchGiftCodes($_GET['login_id'])){
            }
            foreach($code_data as $key => $value){
?>

<p>
<Table>
<Tr><Td RowSpan="3" width="40"><p class="point"><img src="img/pp.png"></p></Td>
<Td><p class="day">発行日：<?php echo $value['created'] ?></p></Td></Tr>
<Tr><Td><p class="pointtaitol">コード：<?php echo $value['code'] ?></p></Td></Tr>
<Tr><Td><p class="time">有効期限：<?php echo $value['exchange_end'] ?></p></Td></Tr>
</Table>
</p>

<hr size="1" width="90%" align="center" noshade>
<?php } ?>

<br>
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

