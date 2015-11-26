<?php
/**
* AddSummariesクラス
* ポイントの加算関連をまとめたクラス
* @package AddSummaries
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: AddSummaries.php,v 1.0 2013/2/9Exp 
*/
Class AddSummaries extends Summaries{
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
        }
    }
    /*
    *  ユーザーに付与するポイントの計算 = 本来のポイント * ポイントレート * 調整ポイント
    *
    *  @accesss public
    *  @return point
    */
    public function  castPoint($advertisement_data){
        $serialize_data = unserialize($advertisement_data['serialize_data']);
        //調整ポイント
        $adjustment_data = $serialize_data['adjustment_data'];
        //本来のポイント
        //$default_point = $advertisement_data['point'];
        $default_point = $advertisement_data['margin'];
        //ユーザーに付与するポイント

        $point = $default_point * DEFAULT_RATE + $adjustment_data;
        //$point = $default_point;

        //小数点第一切り捨て
        return floor($point);
    }
    /*
    *  成果通知が届いてからの処理
    *
    *  @param array $array 成果データ
    *  @accesss public
    */
    public function getSummariesData($array){ 
        try{
            CreateLog::putDebugLog("成果通知＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝");
            //ログインID復号
            CreateLog::putDebugLog("identifier -> ".$array['identifier']);
            if(!$login_id=$this->decodeLoginId($array['identifier'], $array['campany_type'])){
		$login_id = "";
            }
            CreateLog::putDebugLog("login_id>>".$login_id);
            CreateLog::putDebugLog("campany_type>>".$array['campany_type']);
            //ユーザーID取得
            if(!$array['user_id'] = $this->getUserID($login_id)){
		$array['user_id'] = 0;
                CreateLog::putDebugLog("別のアプリのユーザ");
            }
            //データを格納する
            if($array['campany_type']==1){//AffiTown
                if(!$array=$this->setCPAData($array)){
                    throw new Exception("データ格納失敗");
                }
            } else if($array['campany_type']==9){//AffiTown アンケート
            } else {
                if(!$array=$this->setCPIData($array)){
                    throw new Exception("データ格納失敗");
                }
            }

            //パラメータをすべてシリアライズ化しておく
            $serialize_data=serialize($array);
            //$array['branche_margin']=0;    

            if($array['special_payment']){//多段階成果などの成果通知時に報酬が決まるパターン
                $array['payment'] = $array['special_payment'];
                $array['point'] = floor($array['special_payment'] * DEFAULT_RATE);
            }
            if($array['special_point']){//多段階成果などの成果通知時にポイントが決まるパターン
                $array['point'] = $array['special_point'];
            }

            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            //成果テーブルのデータを取得
            if(!$summary_data=$this->searchSummaries($array)){
                CreateLog::putDebugLog("[成果の種類]");
                //成果の種類を判定
                switch($array['status']){
                    case 0: //承認待ち案件のため成果テーブルのみインサート
                        CreateLog::putDebugLog("成果内容>>承認待ち案件");
                        //成果テーブルのみインサート
                        if(!$this->insertSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにインサート失敗");
                        }
                        $array['summary_id'] = mysql_insert_id();
                        CreateLog::putDebugLog("成果テーブルにインサート".$array['summary_id']);
                        //ポイント付与
                        if(!$this->vestPoint($array)){
                            throw new Exception("vestPoint失敗");
                        }
 
                        //ASPに結果を返却
                        if(!$this->returnASP($array['campany_type'])){
                            throw new Exception("ASPに結果を返却失敗");
                        }
                        CreateLog::putDebugLog("ASPに結果を返却");
                        //トランザクション完了
                        if(!mysql_query("commit;",$this->getDatabaseLink())){
                            throw new Exception("トランザクション失敗");
                        }
                        return true;
                    case 3:
                        CreateLog::putDebugLog("成果内容>>起動待ち案件");        
                        //成果テーブルのみインサート
                        if(!$this->insertSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにインサート失敗");
                        }
                        $array['summary_id'] = mysql_insert_id();
                        CreateLog::putDebugLog("成果テーブルにインサート");
/*
                        //ASPに結果を返却
                        if(!$this->returnASP($array['campany_type'])){
                            throw new Exception("ASPに結果を返却失敗");
                        }
                        CreateLog::putDebugLog("ASPに結果を返却"); 
*/
                        //トランザクション完了
                        if(!mysql_query("commit;",$this->getDatabaseLink())){
                            throw new Exception("トランザクション失敗");
                        }
                        return true;
                    case 9:
                        CreateLog::putDebugLog("成果内容>>応募不可案件");
                        //成果テーブルのみインサート
                        if(!$this->insertSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにインサート失敗");
                        }
                        $array['summary_id'] = mysql_insert_id();
                        CreateLog::putDebugLog("成果テーブルにインサート");

                        //ポイント付与
                        if(!$this->vestPoint($array)){
                            throw new Exception("vestPoint失敗");
                        }
 
                        if(!$this->returnASP($array['campany_type'])){
                            throw new Exception("結果返却失敗");
                        }
 
                        //トランザクション完了
                        if(!mysql_query("commit;",$this->getDatabaseLink())){
                            throw new Exception("トランザクション失敗");
                        }
                        return true;

                    case 2://即時成果案件のため成果テーブルと履歴テーブルにインサートしてポイント付与
                        CreateLog::putDebugLog("成果内容>>即時成果案件");
                        //成果テーブルにインサート
                        if(!$this->insertSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにインサート失敗");
                        }
                        $array['summary_id'] = mysql_insert_id();
                        CreateLog::putDebugLog("成果テーブルにインサート成功");
                        //ポイント付与
                        if(!$this->vestPoint($array)){
                            throw new Exception("vestPoint失敗");
                        }
                        CreateLog::putDebugLog("ポイント付与成功");
                        break;
                }
            } else{
                $array['summary_id'] = $summary_data['summary_id'];
                CreateLog::putDebugLog("現在のstatus=".$summary_data['status']);
                if($summary_data['status']==2 || $summary_data['status']==9) {
                    CreateLog::putDebugLog("再度成果送信");
                    throw new Exception("ポイント付与済み");
                }

                switch($array['status']){
                    case 0:
                    case 3:
                        break;
                    case 9:
                        if(!$this->updateSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにアップデート失敗");
                        }
                        //ポイント付与
                        if(!$this->vestPoint($array)){
                            throw new Exception("vestPoint失敗");
                        }
 
                        if(!$this->returnASP($array['campany_type'])){
                            throw new Exception("結果返却失敗");
                        }
                        break;
                    case 2: //成果テーブルはアップデートして履歴テーブルはインサートしてポイント付与
                        CreateLog::putDebugLog("成果内容>>承認待ち案件の成果");
                        //成果テーブルにアップデート
                        if(!$this->updateSummaries($array,$serialize_data)){
                            throw new Exception("成果テーブルにアップデート失敗");
                        }
                        CreateLog::putDebugLog("成果テーブルにアップデート成功");
                        //ポイント付与
                        if(!$this->vestPoint($array)){
                            throw new Exception("vestPoint失敗");
                        }
                        CreateLog::putDebugLog("ポイント付与成功");
                        break;
                }
            }
/*
            if($array['campany_type']!=1){
                // 上限確認
                $advertisement_id = $array['advertisement_id'];
                $obj = new SearchAll();
                if($advertisement_data = $obj -> searchAdvertisementofKey($advertisement_id)){
                    if($max_count = $advertisement_data['max_count']){
                        $obj2 = new Summaries();
                        if($cv_count = $obj2 -> searchConversionSummaries($advertisement_id, 0)){
                            if($cv_count >= $max_count){
                                $obj3 = new UpdateAll();
                                $res = $obj3 -> disableAdvertisement($advertisement_id, 1);
                            }
                        }
                    }
                    if($daily_max_count = $advertisement_data['daily_max_count']){
                        $obj2 = new Summaries();
                        if($daily_cv_count = $obj2 -> searchConversionSummaries($advertisement_id, 1)){
                            if($daily_cv_count >= $daily_max_count){
                                $obj3 = new UpdateAll();
                                $res = $obj3 -> disableAdvertisement($advertisement_id, 0);
                            }
                        }
                    }
                }
            }
*/

            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }

            //END
            exit();
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
        }
    }
    /**
    * 成果テーブルと履歴テーブルにインサートするための、広告データ取得して格納する
    *
    * @access private
    * @return 広告データ
    */
    private function setCPIData($array){
        try{
            //広告クラスのオブジェクト生成
            $ad_obj = new Advertisements();
            //広告テーブルからデータ取得
            $advertisement_data = array();
            if(!$advertisement_data = $ad_obj->getAdvertisementData($array['advertisement_key'], $array['campany_type'])){
                throw new Exception("広告テーブルからデータ取得失敗\n広告キー：".$array['advertisement_key']."ASP:".$array['campany_type']);
            }
            //広告データを入れ込む
            $array['payment'] = $advertisement_data['margin'];
            $array['campaign_name'] = $advertisement_data['name'];
            $array['advertisement_id'] =$advertisement_data['advertisement_id'];
            $array['outcome_id'] =$advertisement_data['outcome_id'];
            //シリアライズを解く
//            $serialize_data = unserialize($advertisement_data['serialize_data']);
//            $array['advertisement_name'] = $serialize_data['name'];//バグるので消す
            //ユーザーに与えるポイント計算
            $array['point'] = $this->castPoint($advertisement_data);

	    $array['timesale'] = 0;

            return $array;
        } catch(Exception $e) {
          CreateLog::putErrorLog(get_class()." ".$e->getMessage());
          return false;
        }
    }

    /**
    * 成果テーブルと履歴テーブルにインサートするための、広告データ取得して格納する
    *
    * @access private
    * @return 広告データ
    */
    private function setCPAData($array){
	try{
//    CreateLog::putDebugLog("setCPAData : ".serialize($array));
	    $adt_obj = new Timesales();
	    if(!$advertisement_data = $adt_obj->getPromotionConversion($array['advertisement_key'], $array['accepted_time'])){
	    	$ad_obj = new AffiTown();
	    	if(!$advertisement_data = $ad_obj->getPromotion($array['advertisement_key'])){//タイムセールになければ通常案件検索
			throw new Exception("広告テーブルからデータ取得失敗\n広告キー：".$array['advertisement_key']."ASP:".$array['campany_type']);
	    	}
		$array['timesale'] = 0;
	    } else {
		$array['timesale'] = 1;
	    }

	
            //広告データを入れ込む
            $array['advertisement_id'] = "AF".$advertisement_data['affitown_advertisement_id'];
            $array['payment'] = $advertisement_data['unit_price'];
            $array['point'] = $advertisement_data['timesale_point'];
	    if(!is_numeric($array['payment'])){
		$array['payment'] = 0;
	    }

            return $array;
        } catch(Exception $e) {
          CreateLog::putErrorLog(get_class()." ".$e->getMessage());
          return false;
        }
    }
    /**
    * ログインID復号
    *
    * @access private
    * @return login_id
    */
    private function decodeLoginId($code,$campany_type){
        if($campany_type!=1){
            $login_id = Utils::str_decrypt($code);//復号
        } else {
            $login_id = $code;
        }
        return $login_id;
    }
    /**
    *  ユーザーにポイントを付与する
    *
    * @access private
    * @return boolean
    */
    private function vestPoint($array){
        try {
            //ログインID復号
            if(!$login_id=$this->decodeLoginId($array['identifier'], $array['campany_type'])){
                throw new Exception("ログインID復号失敗");
            }
            //ログインキー取得
            $login_key = $this -> getLoginKey($login_id);
            if($login_key == false){
                throw new Exception("ユーザーKey取得失敗。");
            }
            //ポイント付与
            if(!$this -> addUserPoints($login_id,$login_key,$array) ){
                throw new Exception("ポイント付与に失敗しました。");
            }
            //ASPに結果を返却
            if(!$this->returnASP($array['campany_type'])){
                throw new Exception("結果返却失敗");
            }
            return true;
      } catch(Exception $e) {
          CreateLog::putErrorLog(get_class()." ".$e->getMessage());
          return false;
      }
    }
    /*
    * ASPに結果を返却
    *
    * @access public 
    * @param $campany_type 
    * @return true
    */
    private function returnASP($campany_type){
        //ASPに結果を返す
        switch($campany_type){
            case 9:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                echo "1";
                break;
            case 6:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                echo "OK";
                break;
            case 5:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                break;
            case 4:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                echo "1";
                break;
            case 3:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                echo "1";
                break;
            case 2:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                break;
            case 1:
                header("HTTP/1.1 200 OK");
                header("Status: 200");
                header("Content-Type: text/plain");
                echo "1";
                break;
       }
            return true;
    }
}
