<?php
 Class GmoGame extends CommonBase{
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

   public function getPoint(){
	try {
	
//        CreateLog::putDebugLog("game cv ".serialize($_GET));

	$user_id = floor(($_GET['user_id'] - 217)/13);

	if(!$login_id = $this -> getLoginId($user_id)){
            CreateLog::putErrorLog(get_class()." login_id検索失敗".$user_id);
            throw new Exception("");
	}

        if(!$login_key = $this -> getLoginKey($login_id)){
            CreateLog::putErrorLog(get_class()." ユーザ不在A");
            throw new Exception("");
        }

        $game_id = array('戦国！姫のお宝さがし', '戦国！姫のお宝さがし', 'リーグオブジュエル', '釣神', 'フォーチュンドロー');

        $array = array();
        $array['point_type']         = 2;
        $array['identifier']	     = $login_id;//ユーザーID
        $array['accepted_time']      = date("Y-m-d H:i:s", strtotime($_GET['date']));//成果発生日時
        $array['timesale']           = 0;
        $array['summary_id']         = 0;
//        $array['campaign_name']	     = "ゲーム";
        $array['campaign_name']      = $game_id[$_GET['game_id']];
        $array['advertisement_name'] = "GmoMedia";
        $array['point']              = $_GET['point'];
        $array['campany_type']       = 1;
        $array['sub_point']          = 0;//加算なので減算ポイントはなし
        $array['status']             = 2;
        $array['bank_status']        = 1;

            //ポイント付与
            if(!$this -> addUserPoints($login_id,$login_key,$array)){
                CreateLog::putErrorLog(get_class()." GmoGame履歴追加エラー");
                throw new Exception("");
            }
	    echo "OK";

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
	    echo "NG";
            die();
        }
   }
}

