<?php
/**
* ControllerDistributionsクラス
* @package
* @author Keisuke Nakamura
* @since PHP 5.4
* @version
*/
Class ControllerDistributions extends ModelDistributions{
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
    /*
    *  ギフト交換が行われた時の処理。減算、配布テーブル、ポイント履歴テーブルにインサート
    *
    *  @accesss private
    *  @param string login_id   ログインID
    *  @param string present_id プレゼントID
    */
    public function Action($login_id,$present_id){
        try{
            $pex_date=array();
            $pex_date['gift_code'] ="";
            //本日のギフトコード交換数が10以上なら交換できないようにする
            $term = " created >= '".date('Y-m-d')."' AND created < '".date('Y-m-d',strtotime('+1 day'))."'";

             if(MONTHLY_GIFT_AMOUNT <= $this->searchMonthlyDistribution()){
                CreateLog::putDebugLog("月別上限超過");
                $data['result'] = "false";
                $data['message']=PEX_ERROR_MESSAGE_4;
                $this->displayJsonArray($data);
            }


            if(DAILY_GIFT_AMOUNT <= $this->searchDailyDistribution()){
                CreateLog::putDebugLog("日別上限超過");
                $data['result'] = "false";
                $data['message']=PEX_ERROR_MESSAGE_3;
                $this->displayJsonArray($data);
            }

            if(CHANGE_CNT <= $this->searchDistributionCnt($login_id,$term)){
                CreateLog::putDebugLog("ギフトコード交換数が多い場合交換できないようにする");
                $data['result'] = "false";
                $data['message']=PEX_ERROR_MESSAGE_3;
                $this->displayJsonArray($data);
            }
            //ユーザーKEYの取得
            if(!$login_key = $this->getLoginKey($login_id)){
                throw new Exception("ユーザーKEYの取得に失敗。");
            }
            //ユーザーIDの取得
            if(!$user_id = $this->getUserID($login_id)){
                throw new Exception("ユーザーの検索に失敗しました。");
            }
            //プレゼントテーブルからプレゼント情報取得
            if(!$present_data = $this->getPresentData($login_id,$present_id)){
                throw new Exception("プレゼント情報取得失敗");
            }

            //ここでPeXAPIを使うか変更する//////////////////////////////////////////
            if(PEX_API_FRG){//PeXAPIを使用する
                //PeXAPIにアクセスする準備
                if(!$array=$this->setPexData($present_data['gift_identify_code'],$user_id)){
                    throw new Exception("PEXAPIにアクセスするデータのセットに失敗");
                }
                CreateLog::putDebugLog("ModelControllerPex");
                $obj= new ControllerPex();
                //PexAPIで取得  
                CreateLog::putDebugLog("PexAPIで取得");
                if(!$pex_date=$obj->Action($array)){
                    throw new Exception("PexAPIで取得失敗");
                }
                CreateLog::putDebugLog("PexAPIで取得成功");
                //errorか判別
                if(!$pex_date['response_code']){
                    CreateLog::putDebugLog("本日のギフトコード交換数が5以上なら交換できないようにする");
                    $data['message']=$this->getErrorMessage($pex_dat['detail_code']);
                    $data['result'] = "false";
                    $this->displayJsonArray($data);
                }
                //配布テーブルにインサートするデータを格納
                $nowtime = date("Y-m-d H:i:s");
                $array=array();
                $array['present_id']=$present_id;
                $array['user_id']=$user_id;
                //Amazonギフトコードの場合URL形式にする
                //if($present_id == 1 || $present_id == 3){
               //     $array['download_url']=AMAZON_URL.$pex_date['gift_code'];
                //    $array['download_url']=$pex_date['gift_code'];
                //} else{
                    $array['download_url']=$pex_date['gift_code'];
                //}
                $array['created']=$nowtime;
                $array['updated']=$nowtime;
                $array['status'] = 1;
                //現在の日付から14日後の日付取得
                $array['time_covered'] = date("Y-m-d H:i:s",strtotime("+2 week"));
                //プレゼント名をシリアライズ化
                $array['serialize_data']=serialize($present_data['campaign_name']);
            } else{//PeX機能をOFFにしたとき
                //配布テーブルにインサートするデータを格納
                $nowtime = date("Y-m-d H:i:s");
                $array=array();
                $array['present_id']=$present_id;
                $array['user_id']=$user_id;
                //ギフトコードは空
                $array['download_url']= "";
                $array['created']=$nowtime;
                $array['updated']=$nowtime;
                $array['status'] = 0;
                //まだコードを配布していないので0000-00-00 00:00:00にする
                $array['time_covered'] = "0000-00-00 00:00:00";
                //プレゼント名をシリアライズ化
                $array['serialize_data']=serialize($present_data['campaign_name']);
            }
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            //配布テーブルにギフトコードをインサート
            if(!$this->insertDistributions($array)){
                throw new Exception("配布テーブルにギフトコードをインサート失敗");
            }
            //減算処理
            if(!$this->subUserPoints($login_id,$login_key,$present_data)){
               throw new Exception("ポイントの減算にしっぱいしました。");
            }
            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            //正常に終了
            $data['result'] = "true";
            $this->displayJsonArray($data);
        }catch (Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            $data['message']=$this->getErrorMessage(98);
            $data['result'] = "false";
        }
    }
    /*
    * errorの種類を判別して返す
    *
    * @access public
    * @return string
    * @return boolean false
    */
    private function getErrorMessage($detail_code){
        try{
            CreateLog::putDebugLog("errorの種類を判別>>".$detail_code);
            switch($detail_code){
                case 01:
                    return PEX_ERROR_MESSAGE_1;
                case 05:
                    return PEX_ERROR_MESSAGE_3;
                case 06:
                    return PEX_ERROR_MESSAGE_4;
                case 07:
                    return PEX_ERROR_MESSAGE_3;
                case 98:
                    return PEX_ERROR_MESSAGE_2;
                default:
                    return PEX_ERROR_MESSAGE_2;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    *  PexAPIにアクセスする準備
    *
    *  @accesss privatea
    *  @param string login_id   ログインID
    *  @param string present_id プレゼントID
    */
    private function setPexData($gift_identify_code,$user_id){
        CreateLog::putDebugLog("gift_identify_code>>".$gift_identify_code);
        CreateLog::putDebugLog("user_id>>".$user_id);
        $trade_id = CreateRand::getRandomString(20) ;
        $now=date("Ymd");
        $user_identify_code=sha1($user_id.PEX_PARTNER_CODE.$now);
         CreateLog::putDebugLog("user_identify_code>>".$user_identify_code);
    //    $trade_id="testtradeid00";
        $array['gift_identify_code']=$gift_identify_code;
  //      $array['gift_identify_code']="testgift500";
        $array['partner_code']=PEX_PARTNER_CODE;
        $array['user_id']=$user_id;
        $array['response_type']="json";
        $array['trade_id']=$trade_id;
        $array['user_identify_code']= $user_identify_code;
        $array['request_time']=date("Y-m-d H:i:s");
        $array['status'] = 0;
        $array['created']=date("Y-m-d H:i:s");
        $array['updated']=date("Y-m-d H:i:s");
        $array['serialize_data']=serialize($array);
        return $array;
    }
}
