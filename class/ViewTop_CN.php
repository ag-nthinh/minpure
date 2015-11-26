<?php
/**
  * AffiTownクラス
  * 特集ページ
  * @package SearchAll
  * @author 氏名 平野 雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/15日Exp $
 */
Class ViewTop_CN extends CommonBase{
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
     public function getPromotions(){
        try{
            $obj = new AffiTown();

	    $param = "login_id=".$_POST['login_id']."&login_pass=".$_POST['login_pass']."&version=".$_POST['version'];

?>
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<head>
<meta charset="utf-8">
<SCRIPT LANGUAGE="JavaScript">
<!--

function ctl() {
    clock();
}
function clock() {
    // 締め切り日時
    var sime = [];
    // 残り時間
    var zan = [];
    // Body に記述されている日時を読む
    sime[0] = new Date(document.getElementById("s01").innerHTML);
    sime[1] = new Date(document.getElementById("s02").innerHTML);
    sime[2] = new Date(document.getElementById("s03").innerHTML);
    // 初期化
    zan[0] = " ";
    zan[1] = " ";
    zan[2] = " ";
    
    //今日の日付データを作成する
    var today1 = new Date();
    // ループ
    for (var i=0; i<3; i++) {
        if (sime[i] > today1) {
            // 締め切り日時と現在を比較
            var dispday1 = sime[i]-today1;
            // 日、時、分、秒に分解
            var date = Math.ceil(dispday1/(24*60*60*1000));
            var hour = Math.ceil((dispday1 % (24*60*60*1000)) / (60*60*1000));
            var min = Math.ceil((dispday1 % (24*60*60*1000)) / (60*1000) % 60);
            var sec = Math.ceil((dispday1 % (24*60*60*1000)) / (1000) % 60 % 60);
            var ms10 = Math.ceil((dispday1 % (24*60*60*1000)) / (10) % 60 % 60);
            date = date - 1;
            hour = hour - 1;
            min = min - 1;
            sec = sec - 1;
            ms10 = ms10 - 1;
        
            // 数値が1桁の場合、頭に0を付けて2桁で表示する指定
            if(hour < 10) { hour = "0" + hour; }
            if(min < 10) { min = "0" + min; }
            if(sec < 10) { sec = "0" + sec; }
            if(ms10 < 10) { ms10 = "0" + ms10; }
//            if(date > 0) {
            // フォーマット
                zan[i] = "<p class=\"times\">残り" + date + "日 " + hour + "時間" + min + "分" + sec + "秒" + ms10 + "</p>";
//            } else {
            // フォーマット
//                zan[i] = "残り" + date + "日 " + hour + "時間" + min + "分" + sec + "秒";
//            }
        } else {
            zan[i] = "<p class=\"times\">時間切れ</p>";
        }
    }
    document . getElementById( 'clock-01' ) . innerHTML= zan[0] . toLocaleString(); // div id="clock-01"
    document . getElementById( 'clock-02' ) . innerHTML= zan[1] . toLocaleString(); // div id="clock-02"
    document . getElementById( 'clock-03' ) . innerHTML= zan[2] . toLocaleString(); // div id="clock-03"
    
    
    // 10ミリ秒ごとに処理を実効
    window . setTimeout( "clock()", 10);
}
window . onload = ctl;
//-->
</SCRIPT>
</head>

<body>

<a href="https://www.clubnets.co.jp/smp/giftcd/dig?appid="><img src="img/dig3cnp.jpg"></a>

<!--
<a href="https://www.clubnets.co.jp/smp/giftcd/diglog?appid="><img src="img/dig2cnpppp2.png"></a>
-->

<p class="salehd">タイムセール開催中!!</p>
<!-- タイムセール枠 -->

<?php 
    $obj2 = new Timesales();

if($timesale_data = $obj2 -> getTimesalePromotions()){
	foreach($timesale_data as $key => $value){
?>

<div id="s0<?php echo $key+1 ?>"><?php echo $value['time'] ?></div>
<div id="timesale">
<p>
<a href="detailts_cn.php?aaid=<?php echo $value['affitown_advertisement_id'] ?>&<?php echo $param; ?>">
<Table>
<Tr><Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><font color="#888888"><?php echo number_format($value['point']) ?>→</font><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['timesale_point']); ?></p></Td></Tr>
<Tr><Td><div id="clock-0<?php echo $key+1 ?>"></div></Td></Tr>
</Table>
</a>
</p>
</div>

<?php }} ?>
<script>
document.getElementById('s01').style.display='none';
document.getElementById('s02').style.display='none';
document.getElementById('s03').style.display='none';
</script>

<?php Banner::set1(); ?>

<!-- 新規無料案件枠 -->
<p class="freehd">無料でガッポリ稼ぐ NEW!!</p>
<div id="free1"><p>

<?php
    if(!$p2 = $obj -> getPromotions(array("type"=>1, "timesale"=>0, "status"=>1, "num"=>3, "login_id"=>$_POST['login_id']))){}
            //CreateLog::putDebugLog(serialize($p2));
        foreach($p2 as $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param; ?>">
<Table>
<Tr>
<Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['timesale_point']) ?></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa1_cn.php?<?php echo $param; ?>">もっと見る</a></Td>
</Table>

</p></div>

<?php Banner::set2(); ?>

<!-- 新規アプリ案件枠 -->
<p class="apphd">手軽に稼ぐ NEW!!</p>
<div id="apps"><p>

<?php 
    if(!$p = $obj -> getAGTopPromotions()){}
        foreach($p as $value){
?>
<a href="close.php">
<Table>
<Td RowSpan="3" width="70" height="70"><img src="<?php echo $value['img_url'] ?>" width="70" height="70"></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point"><img src="img/dig.png" width="27" height="27"><?php echo $value['point'] ?>　<font style="background-color:<?php echo $value['status_color']?>;"><?php echo $value['status_text'] ?></font></p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['advertisement_name'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>

</p>
<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="close.php">もっと見る</a></Td>
</Table>
</div>

<?php Banner::set3(); ?>

<!-- 新規実質無料案件 -->
<p class="free2hd">実質無料でガッポリ稼ぐ NEW!!</p>
<div id="free2">

<?php 
    if(!$p3 = $obj -> getPromotions(array("type"=>2, "timesale"=>0, "status"=>1, "num"=>3, "login_id"=>$_POST['login_id']))){}
        foreach($p3 as $value){
?>
<a href="detail_cn.php?adid=<?php echo $value['adid'] ?>&<?php echo $param; ?>">
<Table>
<Tr>
<Td RowSpan="3" width="70" height="70"><?php echo $value['img'] ?></Td>
<Td><p class="nf"><?php echo $value['name'] ?></p></Td></Tr>
<Tr><Td><p class="point02"><font color="#888888"><?php echo $value['amount'] ?>で</font><img src="img/dig.png" width="27" height="27"><?php echo number_format($value['point']) ?>獲得</p></Td></Tr>
<Tr><Td><p class="time"><?php echo $value['action'] ?></p></Td></Tr>
</Table>
</a>
<hr size="1" width="90%" align="center" noshade>
<?php } ?>

<Table align="center" height="50">
<Td Valign="middle"><a class="btn" href="cpa2_cn.php?<?php echo $param; ?>">もっと見る</a></Td>
</Table>

</p></div>


<!-- フッダー -->

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

