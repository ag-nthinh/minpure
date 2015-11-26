<?php
/**
* ModelPexクラス
* 発券テーブルと照会テーブルのモデルクラス
* @package ModelPex
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: History,v 1.0 2013/8/9Exp $
*/
Class ModelPex extends CommonBase{
/**
    * コンストラクタ
    */
    public function __construct() {
        try{
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    * レスポンステーブルにインサート
    *
    * @param array
    * @access public
    * @return boolean
    */
    public function insertLogResponsePublishes($data_array){
        try{
            $sql = "INSERT INTO
                            log_response_publishes
                    SET
                            response_code = '".$data_array['response_code']."',
                            partner_code = '".$data_array['partner_code']."',
                            detail_code = '".$data_array['detail_code']."',
                            message = '".$data_array['message']."',
                            trade_id = '".$data_array['trade_id']."',
                            user_id = ".$data_array['user_id'].",
                            gift_code = '".$data_array['gift_code']."',
                            serialize_data = '".$data_array['serialize_data']."',
                            created = '".$data_array['created']."',
                            updated = '".$data_array['updated']."'
                            ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * リクエストテーブルからデータ取得    
    *
    * @param array
    * @access public
    * @return boolean
    */
    public function selectLogRequestPublishes($data_array){
    }
    
    /*
    * リクエストテーブルにインサート
    *
    * @param array 
    * @access public
    * @return boolean 
    */
    public function insertLogRequestPublishes($data_array){
        try{
            $sql = "INSERT INTO
                            log_request_publishes
                    SET
                            partner_code = '".$data_array['partner_code']."',
                            gift_identify_code = '".$data_array['gift_identify_code']."',
                            trade_id = '".$data_array['trade_id']."',
                            user_id = ".$data_array['user_id'].",
                            request_time = '".$data_array['request_time']."',
                            status = ".$data_array['status'].",
                            serialize_data = '".$data_array['serialize_data']."',
                            created = '".$data_array['created']."',
                            updated = '".$data_array['updated']."'
                            ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
    * リクエストテーブルをアップデート
    *
    * @param array
    * @access public
    * @return boolean
    */
    public function updateLogRequestPublishes($data_array){
        try{
            $sql = "UPDATE
                            log_request_publishes
                    SET
                            status = ".$data_array['status'].",
                            serialize_data = '".$data_array['serialize_data']."',
                            updated = '".$data_array['updated']."'
                    WHERE
                            user_id = ".$data_array['user_id']." AND
                            patner_code = '".$data_array['patner_code']."'
                            ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

}
