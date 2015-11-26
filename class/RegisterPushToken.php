<?php
/**
* @package RegisterPushTokenクラス
* デバイストークン登録
* POSTで受け取ったデバイストークンをDBのuserテーブルに登録する
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: RegisterPushToken.php,v 1.0 2013/2/9Exp $
*/

class RegisterPushToken extends CommonBase{

    /* コンストラクタ*/
    function __construct(){
        try{
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
            //プッシュトークン取得
            if(!$this -> registerPushToken()){
                throw new Exception("プッシュトークン登録失敗");
            } 
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * push_tokenの登録
    *
    * @access public
    * @return boolean
    */
    public function registerPushToken(){
        try {
            if(empty($_POST['gcm_id']) || empty($_POST['login_id'])) {
                throw new Exception("POSTデータが足りていません");
            }
            $login_id = $_POST['login_id'];
            $devicetoken = $_POST['gcm_id'];
            $sql = 'UPDATE 
                            data_users
                    SET
                            push_token = "'.$devicetoken.'"
                    WHERE
                            login_id = "'.$login_id.'";';
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("UPDATE失敗".mysql_error());
            }
            if(!$result_count = $this -> searchUserData($devicetoken)){
                throw new Exception("ユーザーデータ取得失敗");
            }
            if($result_count == 2){
                if(!$this->deleteUser($devicetoken)){
                    throw new Exception("deleteUser関数実行時にエラーが起きました");
                }
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * push_tokenのかぶったユーザーがいるか検索
    *
    * @access public
    * @return boolean
    */
    public function searchUserData($devicetoken){
        try{
            $sql = "SELECT
                            *
                    FROM
                            data_users
                    WHERE
                            deleted    = 0 AND
                            push_token = '".$devicetoken."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            }
            $result_count = mysql_num_rows($result);
            if($result_count == 1){
                return 1;
            } else{
                return 2;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * push_tokenのかぶったユーザーの削除
    *
    * @access public
    * @param string $push_token プッシュトークン
    * @return boolean
    */
    public function deleteUser($push_token){
        try{
            $now_data=date("Y-m-d H:i:s");
            $sql ="UPDATE
                                data_users           
                   SET
                                deleted = 1,
                                updated ='".$now_data."'
                   WHERE
                                push_token = '".$push_token."'AND
                                deleted = 0 limit 1;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error()." ".$sql);
            } 
            return true;            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}
