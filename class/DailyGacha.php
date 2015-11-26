<?php
/**
* DailyGachaクラス
* @package
* @author masaya hirano
* @since PHP 5.4
* @version
*/
Class DailyGacha extends CommonBase{
    /*
    コンストラクタ
    */
    public function __construct() {
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    public function insertDailyGachaPoint($login_id,$point){
        try {
            //ログインキー取得
            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("method:insertDailyGachaPoint(ログインキー取得失敗)");
            }
            // ガチャ回数チェック
            if($this -> checkBankbooksGacha($login_id)){
                throw new Exception("method:insertDailyGachaPoint(ガチャ終了)");
            }

            $array = array();
            //履歴テーブルにインサートするデータを配列に格納
            $array['point_type']         = 2;//デイリーガチャなのでポイントタイプは2
            $array['identifier']         = $login_id;
            $array['achieve_id']         = 0;
            $array['summary_id']	 = 0;
            $array['timesale']	 	 = 0;
            $array['accepted_time']      = date("Y-m-d H:i:s", time());
            $array['advertisement_key']  = 0;
            $array['campaign_name']      = "デイリーガチャ";
            $array['advertisement_id']   = 0;
            $array['advertisement_name'] = "デイリーガチャの実行";
            $array['sub_point']          = 0;//デイリーガチャなので減算ポイントはなし
            $array['point']              = 0;
            if($point==GACHA_POINT || $point==GACHA_POINT2){
                $array['point']          = $point;
            }
            if($array['point']>0){
                $array['bank_status']    = 1;
            } else {
                $array['bank_status']    = 0;
            }

            //ポイント付与
            if(!$this -> addUserPoints($login_id,$login_key,$array) ){
                throw new Exception("method:insertDailyGachaPoint(ポイント付与に失敗しました。)");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
     /*
    *  ガチャのステータスチェック
    *
    *  @accesss public
    *  @return boolean
    */
    public function checkStatusGacha($login_id){
        try {
            $array = array();
            //本日のガチャを引いた回数をチェック

            if(!$login_key = $this -> getLoginKey($login_id)){
                throw new Exception("ユーザーkey取得失敗");
            } else{
                $array['point'] = $this -> getTotalPoints($login_id,$login_key);
		if(is_numeric($array['point'])){
			$array['point'] = number_format($array['point']);
		}
	    }

            $array['count'] = $this -> checkBankbooksGacha($login_id);
            //デイリーガチャの確率計算
            $probability = rand(1,10);
            if($array['count'] == 0){
                $probability_gacha = PROBABILITY_GACHA;
                $gacha_point = GACHA_POINT;
            } else if($array['count'] == 1){
                $probability_gacha = PROBABILITY_GACHA2;
                $gacha_point = GACHA_POINT2;
            }
            if($probability_gacha >= $probability){
                $array['getpoint'] = $gacha_point;
            } else if( $probability_gacha < $probability){
                $array['getpoint'] = 0;
            } else{
                throw new Exception($array['getpoint']);
            }
            //SNS投稿ができるかチェック
//            $array['sns_flg'] = $this -> checkSNSFlag($login_id);
//            $array['tweet_button_text'] = TWEET_BUTTON_TEXT;
            // $array['tweet_button_text'] ='';
            //JSONで出力
            $this -> displayJsonArray($array);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
}

