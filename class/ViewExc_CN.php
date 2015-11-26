<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewExc_CN extends CommonBase{
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
<head>
<script language="javascript" type="text/javascript">
function disp() {
	if(window.confirm('交換しますか？')){
		location.href="help_cn.php";
	} else {
	}
}
</script>
</head>

<body>

<!-- 交換対象 -->

<div id="pointable"><img src="img/dig.png" width="27" height="27"><?php echo number_format($total_point) ?></div>
<br>
<div id="pointsyousai">
<p>

<Table align="center">
<Tr><Td Valign="middle"><h2><center>CNポイントへの交換は、5,000DIGから可能です。</center></h2></Td></Tr>
</Table>
<Table align="center">
<Tr><Td Valign="middle"><img src="img/cnpoint.jpg" width="100%"></Td></Tr>
</Table>
<Table align="center" height="50">
<Tr><Td><p class="point02"><img src="img/dig.png" width="27" height="27">5,000→
CNポイント1,000pt</p></Td></Tr>
</Table>
<hr size="1" width="90%" align="center" noshade>

<?php if($total_point>=5000){ ?>
<Table align="center" height="50">
<Tr><Td Valign="middle"><a class="btn" href="exchange2_cn.php?login_id=<?php echo $_GET['login_id'] ?>&login_pass=<?php echo $_GET['login_pass'] ?>&version=<?php echo $_GET['version'] ?>">CNポイントと交換する</a></Td></Tr>
</Table>
<?php } ?>

<?php 
/*
if($total_point>=400000){ ?>
<Table align="center" height="50">
<Tr><Td Valign="middle"><a class="btn" href="javascript:void(0);" onclick="disp();">CNポイントと交換する</a></Td></Tr>
</Table>
<?php }
*/
 ?>



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

