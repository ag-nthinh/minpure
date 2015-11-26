<?php
/**
* ModelDistributionsクラス
* 配布テーブルのモデルクラス
*
* @package ModelDistributions
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: ModelDistributions.php,v 1.0 2013/8/11Exp $
*/
Class ModelDistributions extends CommonBase { 
    /*
    コンストラクタ
    */
    public function __construct(){
        try {
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    *  ユーザーごとの配布履歴取得
    *
    *  @accesss public
    *  @return boolean
    */
    public function searchDistributionCnt($login_id,$term){
        try {
            //ユーザーID取得
            if(!$user_id = $this -> getUserID($login_id)){
                throw new Exception("ユーザーID取得失敗");
            }
            $sql="SELECT        
                        *
                  FROM  
                        data_distributions
                  WHERE 
                        user_id=".$user_id." AND
                        ".$term.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            return mysql_num_rows($result);
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
   /*
    *  当日の配布履歴数取得
    *
    *  @accesss public
    *  @return int
    */
    public function searchDailyDistribution(){
        try {
            $sql="SELECT        
                    sum(mp.point) spp
                  FROM
                    master_presents mp,
                    data_distributions dd
                  WHERE
                    mp.present_id=dd.present_id AND
                    dd.created>=date(now());";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row['spp'];

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
   /*
    *  当月の配布履歴数取得
    *
    *  @accesss public
    *  @return int
    */
    public function searchMonthlyDistribution(){
        try {
            $sql="SELECT
                    sum(mp.point) spp
                  FROM
                    master_presents mp,
                    data_distributions dd
                  WHERE
                    mp.present_id=dd.present_id AND
                    dd.created>=DATE_FORMAT(now(),'%Y-%m-01');";
            if(!$result = mysql_query($sql ,$this->getDatabaseLink())){
                throw new Exception("検索に失敗しました".$sql."/".mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row['spp'];

        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
 
    /*
    *  配布テーブルにギフトコードをインサート
    *
    *  @accesss public
    *  @return boolean
    */
    public function insertDistributions($array){
        try{
            $sql = "INSERT INTO
                            data_distributions
                    SET
                            present_id = '".$array['present_id']."',
                            user_id ='".$array['user_id']."',
                            download_url = '".$array['download_url']."',
                            status =  '".$array['status']."',
                            time_covered = '".$array['time_covered']."',
                            serialize_data='".$array['serialize_data']."',
                            created = '".$array['created']."',
                            updated = '".$array['updated']."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }}
