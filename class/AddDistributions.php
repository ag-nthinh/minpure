<?php
/**
* AddSummariesクラス
* @package
* @author Keisuke Nakamura
* @since PHP 5.4
* @version
*/
Class AddDistributions extends CommonBase{
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
    public function addDistributionData($login_id,$present_id){

        try{
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
            //減算処理
            if(!$this->subUserPoints($login_id,$login_key,$present_data)){
               throw new Exception("ポイントの減算にしっぱいしました。");
            }
            //プレゼント名をシリアライズ化
            $serialize_data = serialize($present_data['campaign_name']);
            $nowtime = date("Y-m-d H:i:s");
            $sql = "INSERT INTO
                            data_distributions
                    SET
                            present_id = '".$present_id."',
                            user_id ='".$user_id."',
                            serialize_data='".$serialize_data."',
                            created = '".$nowtime."',
                            updated = '".$nowtime."';";
           
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            } 
        }catch (Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
}
