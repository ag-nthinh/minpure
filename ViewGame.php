<?php
/*
  使い方
  GmoGame::excute($user_id);
*/

 Class ViewGame extends CommonBase{
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

   public function execute(){
        try {
            define('GMO_MEDIA_ID','9');
            define('GMO_KEY_STRING','Ds4LAwR6UeCJTD2BVALpkrCYygfsHL3G');

            if(!$user_id = $this->getUserID($_POST['login_id'])){
                throw new Exception("ユーザー検索失敗1");
            }
            $user_id2 = 13 * $user_id + 217 + ($user_id*floor($user_id/3))%11;
//            CreateLog::putDebugLog("user_id=".$user_id." : ".$user_id2);

            $time = date('YmdHis');
            $key_string = strtoupper(MD5(strtoupper(MD5(GMO_KEY_STRING.$user_id2)).$time));
//            $url = "http://gaingame.gesoten.com/otenba?user_id=".$user_id2."&media_id=".GMO_MEDIA_ID."&time=".$time."&key=".$key;
//            header('Location: '.$url);
?>
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 交換対象 -->

<div id="pointsyousai">
<h2>ゲームを選択してください</h2>

<?php
        $game_url_string = array("otenba", "jewel", "fish", "psy");
        $game_title_string = array("戦国！姫のお宝さがし", "リーグオブジュエル", "釣神", "フォーチュンドロー");

        foreach($game_url_string as $key => $value){
                $url = "http://gaingame.gesoten.com/".$game_url_string[$key]."?user_id=".$user_id2."&media_id=".GMO_MEDIA_ID."&time=".$time."&key=".$key_string;		
//            CreateLog::putDebugLog($url);
?>
<a href="<?php echo $url ?>"><img src="img/game<?php echo $key+1 ?>.jpg" width="150" height="150" ></a>
<?php if($key%2){ ?>
<br>
<?php }} ?>

<p><a href="http://ad.atown.jp/adserver/cp?sid=6b662&did=2237&u1=<?php echo Utils::str_encrypt($_POST['login_id']); ?>"><img src="img/survey_banner2.jpg"></a></p>
<br>


</div>
</div>

</body>

<?php
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }

   public function execute2(){
        try {
            define('GMO_MEDIA_ID','9');
            define('GMO_KEY_STRING','Ds4LAwR6UeCJTD2BVALpkrCYygfsHL3G');

            if(!$user_id = $this->getUserID($_POST['login_id'])){
                throw new Exception("ユーザー検索失敗1");
            }
            $user_id2 = 13 * $user_id + 217 + ($user_id*floor($user_id/3))%11;
//            CreateLog::putDebugLog("user_id=".$user_id." : ".$user_id2);

            $time = date('YmdHis');
            $key_string = strtoupper(MD5(strtoupper(MD5(GMO_KEY_STRING.$user_id2)).$time));
//            $url = "http://gaingame.gesoten.com/otenba?user_id=".$user_id2."&media_id=".GMO_MEDIA_ID."&time=".$time."&key=".$key;
//            header('Location: '.$url);
?>
<link rel="stylesheet" type="text/css" href="css/style_cn.css" media="screen">
<html lang="ja">
<meta charset="utf-8">

<body>

<!-- 交換対象 -->

<div id="pointsyousai">
<h2>ゲームを選択してください</h2>

<center>
<table><tr>
<?php
        $game_comment_string = array("最大3,000DIG", "DIG山分け", "DIG山分け", "DIG山分け");
        $game_url_string = array("otenba", "jewel", "fish", "psy");
        $game_title_string = array("戦国！姫のお宝さがし", "リーグオブジュエル", "釣神", "フォーチュンドロー");
            CreateLog::putDebugLog("http://ad.atown.jp/adserver/cp?sid=6b662&did=2237&u1=".Utils::str_encrypt($_POST['login_id']));

        foreach($game_url_string as $key => $value){
                $url = "http://gaingame.gesoten.com/".$game_url_string[$key]."?user_id=".$user_id2."&media_id=".GMO_MEDIA_ID."&time=".$time."&key=".$key_string;
//CreateLog::putDebugLog($url);
?>

<td>
<p class="gamecomment"><?php echo $game_comment_string[$key] ?></p>
<a href="<?php echo $url ?>"><img src="img/game<?php echo $key+1 ?>.png" width="150" height="150" ></a>
</td>
<?php if($key%2){ ?>
</tr><tr>
<?php }} ?>
</tr></table>
</center>

<p><a href="http://ad.atown.jp/adserver/cp?sid=6b662&did=2237&u1=<?php echo Utils::str_encrypt($_POST['login_id']); ?>"><img src="img/survey_banner.jpg"></a></p>
<br>

<p><a href="http://appdriver.jp/3.0.19420c?media_id=3804&digest=0338e6b354cdfff05b0b04c80dd91c89f685e2a96d78126dcc983a9ffb50838b&campaign_id=29225&_identifier=<?php echo Utils::str_encrypt($_POST['login_id']); ?>"><img src="img/typing_banner.jpg"></a></p>
<br>
<br>

</div>
</div>

</body>

<?php

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }


}

