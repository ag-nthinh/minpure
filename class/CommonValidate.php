<?php
/**
* CommonValidateクラス
* データ整合性確認クラス
* @package CommonValidate
* @author 金丸 祐治
* @since PHP 5.4.8
* @version $Id: CommonValidate.php,v 1.0 2012/12/11Exp $
*/
Class CommonValidate {
    /**
    * ユーザーID(UIID)のチェック
    *
    * @accsess public static
    * @param string $user_id
    * @return string　ログインID、不正時はfalse
    */
    public static function validateLoginId($login_id){
        try {
            if(empty($login_id)) {
                throw new Exception("user_idの値が存在しません");
            }
            return mysql_real_escape_string($login_id);
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーKEYのチェック
    *
    * @accsess public static
    * @param string $login_key
    * @return string　ユーザーkey、不正時はfalse
    */
    public static function validateLoginKey($login_key){
        try {
            if(empty($login_key)) {
                throw new Exception("login_keyの値が存在しません");
            }
            return $login_key;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーTokenのチェック
    *
    * @accsess public static
    * @param string $token
    * @return string　ユーザーtoken、不正時はfalse
    */
    public static function validateUserToken($token){
        try {
            if(empty($token)) {
                throw new Exception("login_keyの値が存在しません");
            }
            return mysql_real_escape_string($token);
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ログインタイムアウトのチェック
    *
    * @accsess public static
    * @param string $login_time
    * @return string　更新時刻、不正時はfalse
    */
    public static function validateLoginTime($login_time) {
        try {
            if(empty($login_key)) {
                throw new Exception("login_timeの値が存在しません");
            }
            if($login_time + 3600 < time()) {
                throw new Exception("login_timeが1時間以上更新されていません");
            }
            return $login_time;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーticketのチェック
    *
    * @accsess public static
    * @param string $user_ticket
    * @param string
    * @return string　ユーザーticket、不正時はfalse
    */
    public static function validateUserTicket($user_id,$user_key,$time,$user_ticket) {
        try {
            if(empty($user_id) || empty($user_key) || empty($time) || empty($user_ticket)) {
                throw new Exception("user_ticketに必要な値が存在しません");
            }

            $check = sha1(MAGIC_CODE.$user_id.$user_key.$time);
            if($check === $user_ticket) {
                return $user_ticket;
            } else {
                throw new Exception("チケットガ一致しません。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ログインpassチェック
    * login_id:login_key:秘密鍵をhash化
    *
    * @accsess public static
    * @param string $login_id
    * @param string $login_key
    * @param string $login_pass
    * @return boolean 成功時true 失敗時false
    */
    public static function validateLoginPass($login_id,$login_key,$login_pass){
        $sorce = $login_id.":".$login_key.":".SECRET_KEY;
        $pass  = hash('sha256',$sorce,false);
        //CreateLog::putDebugLog(get_class()." ".$login_id."\n/".$login_key."\n/".SECRET_KEY."\n/".$pass."/\n".$login_pass);
        if($pass == $login_pass){
            return true;
        }else{
            return false;
        }
    }

}//End of class

