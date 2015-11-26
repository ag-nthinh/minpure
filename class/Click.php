<?php
/**
* Clickクラス
* 案件クリックのmodelクラス
* @package Summaries
* @author 猿渡 俊輔
* @since PHP 5.4.8
* @version $Id: Clicks.php,v 1.0 2014/1/14Exp 
*/
Class Click extends CommonBase{
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
    * クリックテーブルにインサート
    *
    * @access private
    * @return array
    */
    public function insertClick($login_id, $advertisement_id){
        try{
            $now_time=date("Y-m-d H:i:s");
            $sql = "INSERT INTO log_clicks SET
                        login_id = '".$login_id."',
                        advertisement_id = '".$advertisement_id."',
                        created = '".$now_time."',
                        updated = '".$now_time."';";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにインサート出来ませんでした。".$sql);
            }
            return true;
        } catch(Exception $e){
            //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * 案件IDで成果テーブルのCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchClickSummaries($advertisement_id, $term_type){
        try{
            // term_type 0:累計 1:デイリー
            $term = "";
            if($term_type==1){
                $term = " AND created >= date(now()) ";
            }

            $sql = "SELECT
                        *
                    FROM
                        log_clicks
                    WHERE
                        advertisement_id = '".$advertisement_id."' ".$term.";";
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
    * 案件IDで成果テーブルのUCV数を取得(全データ)
    *
    * @access public
    * @return array
    */
    public function searchUniqueUserClickSummaries($advertisement_id, $term_type){
        try{
            // term_type 0:累計 1:デイリー
            $term = "";
            if($term_type==1){
                $term = " AND created >= date(now()) ";
            }

            $sql = "SELECT DISTINCT
                        login_id
                    FROM
                        log_clicks
                    WHERE
                        advertisement_id = '".$advertisement_id."' ".$term.";";
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


}
