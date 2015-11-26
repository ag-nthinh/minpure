<?php
/**
* Summariesクラス
* 成果通知周りのmodelクラス
* @package Summaries
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: Summaries.php,v 1.0 2013/5/15Exp 
*/
Class Summaries extends CommonBase{
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
    * 案件の重複チェック
    * ユーザーID、広告キー、成果地点ID、ASPの4つの情報が一致していたら重複なので弾く
    * @access public
    * @return boolean
    */
    public function checkDuplicate($user_id,$advertisement_key,$outcome_id,$campany_type){
        try{
            $sql = "SELECT
                        summary_id
                    FROM
                        data_summaries
                    WHERE
                        user_id = '".$user_id."' AND
                        advertisement_key = '".$advertisement_key."' AND
                        outcome_id = '".$outcome_id."' AND
                        campany_type = '".$campany_type."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $num =  mysql_num_rows($result);
            switch($num) {
                case 0 :
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.重複ではありません！！");
                        return true;
                default :
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.重複しています");
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.user_id>>".$user_id);
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.advertisement_key>>".$advertisement_key);
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.outcome_id>>".$outcome_id);
                        CreateLog::putDebugLog("AddSummaries.checkDuplicate.campany_type>>".$campany_type);
                        return false;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }

    }
    /*
    * 成果テーブルをアップデート
    *
    * @access private
    * @return boolean
    */
    public function updateSummaries($array,$serialize_data){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "UPDATE  
                            data_summaries
                    SET                        
                            status='".$array['status']."',
                            serialize_data='".mysql_real_escape_string($serialize_data)."',
                            updated='".$now_time."'
                    WHERE       
                            user_id ='".$array['user_id']."' AND
                            advertisement_key ='".$array['advertisement_key']."' AND
                            outcome_id ='".$array['outcome_id']."' AND
                            campany_type='".$array['campany_type']."';";
     //       CreateLog::putDebugLog($sql);
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにUPDATE出来ませんでた。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 成果テーブルにインサート
    *
    * @access private
    * @return array
    */
    public function insertSummaries($array,$serialize_data){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "INSERT INTO data_summaries(
                                            advertisement_id,
                                            user_id,
                                            advertisement_key,
                                            outcome_id,
                                            campany_type,
                                            timesale,
                                            payment,
                                            status,
                                            serialize_data,
                                            created,
                                            updated
                                            )
                    VALUES(
                                            '".$array['advertisement_id']."',
                                            '".$array['user_id']."',
                                            '".$array['advertisement_key']."',
                                            '".$array['outcome_id']."',
                                            '".$array['campany_type']."',
                                            '".$array['timesale']."',
                                            '".$array['payment']."',
                                            '".$array['status']."',
                                            '".mysql_real_escape_string($serialize_data)."',
                                            '".$now_time."' ,
                                            '".$now_time."'
                                            );";
            //CreateLog::putDebugLog($sql);
            
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにインサート出来ませんでた。".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 成果テーブルのデータを取得
    * 
    * @access private
    * @return array 
    */
    public function searchSummaries($array){
        try{
            $sql = "SELECT
                        summary_id, status
                    FROM
                        data_summaries
                    WHERE
                        user_id = '".$array["user_id"]."' AND
                        advertisement_key = '".$array["advertisement_key"]."' AND
                        outcome_id = '".$array["outcome_id"]."' AND
                        campany_type = '".$array["campany_type"]."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return 0;
            } else{
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e){
            return false;
        }
    }
    /*
    * 成果テーブルのデータを取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchAllSummaries($array){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
                        user_id = '".$array["user_id"]."' AND
                        advertisement_key = '".$array["advertisement_key"]."' AND
                        outcome_id = '".$array["outcome_id"]."' AND
                        campany_type = '".$array["campany_type"]."';";
   //         CreateLog::putDebugLog($sql);
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return "not_date";
            } else{
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件IDで成果テーブルのCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchConversionSummaries($advertisement_id, $term_type){
        try{
            // term_type 0:累計 1:当日 2:前日
            $term = "";
            if($term_type==1){
                $term = " updated >= date(now()) AND ";
            } else if($term_type==2){
                $term = " updated BETWEEN '".date("Y-m-d", strtotime("-1 day"))."' AND '".date("Y-m-d")."' AND ";
            }

            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
                        advertisement_id = '".$advertisement_id."' AND
                        ".$term."
                        status=2;";
   //         CreateLog::putDebugLog($sql);
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            return mysql_num_rows($result);
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件IDで成果テーブルのCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchConversionSummariesDetail($advertisement_id){
        try{
            $sql = "SELECT
                        *
                    FROM
                        data_summaries
                    WHERE
                        advertisement_id = '".$advertisement_id."' order by updated desc;";
   //         CreateLog::putDebugLog($sql);
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception($sql);
            }
            while($table = mysql_fetch_assoc($result)){
                $serialize_data = unserialize($table['serialize_data']);
                $row[$num]['campaign_name'] = $serialize_data['campaign_name'];
                $row[$num] = $table;
                $num++; 
            }
            return $row;
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}
