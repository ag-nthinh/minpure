<?php
/**
* プッシュ通知クラス
* @since PHP 5.4.8
* @package SendPush
* @author 平野 雅也
* @version $Id: SendPush.php,v 1.0 2013/2/9Exp $
*/
require_once "HTTP/Request.php";
Class SendPush extends CommonBase {
    /*
     * コンストラクタ
     */
    public function __construct(){
        try {
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die("false");
        }
    }
    /**
    * ログインIDで指定したユーザー宛てにプッシュ通知
    * @accsess public
    * @return
    */
    public function sendPush($user_id,$message){
        try{
            // 引数を確認
            $sql = 'SELECT
                        push_token
                    FROM
                        data_users
                    WHERE
                        CHAR_LENGTH(push_token) = 140 AND
                        user_id = "'.$user_id.'" AND
                        push_token is not null AND
                        push_token != "" AND
                        push_flg = 1 AND
                        deleted = 0;';
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("けんさくしっぱい/".$sql."/".mysql_error());
            }
            //一時的にPHP5のエラー出力からE_STRICTを外す
            $E = error_reporting();
            if(($E & E_STRICT) == E_STRICT) error_reporting($E ^ E_STRICT);

            $row = mysql_fetch_assoc($result);
            // RegistrationIDを指定して送信
            $regId = $row['push_token'];
        //    CreateLog::putDebugLog($regId);
            if(!$this->send($regId,$message)){
                throw new Exception("push送信失敗");
            }
            // error_reportingを元に戻す
            error_reporting($E);
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }

    /**
    * 全ユーザー宛てにプッシュ通知
    * @accsess public
    * @return
    */
    public function sendPushAll($message){
        try{
            // 引数を確認
            $sql = 'SELECT
                        push_token
                    FROM
                        data_users
                    WHERE
                        CHAR_LENGTH(push_token) = 140 AND
                        push_token is not null AND
                        push_token != "" AND
                        push_flg = 1 AND
                        deleted = 0;';
            $result = mysql_query($sql);
            if(!$result) {
                CreateLog::putErrorLog('SendPush:取得できませんでした。'.mysql_error().PHP_EOL);
                die();
            }
            //一時的にPHP5のエラー出力からE_STRICTを外す
            $E = error_reporting();
            if(($E & E_STRICT) == E_STRICT) error_reporting($E ^ E_STRICT);

            // クエリ結果からデバイストークンを取り出して全て送信
            while(true) {
                $row = mysql_fetch_assoc($result);
                if(!$row['push_token']) {
                    break;
                }
                // RegistrationIDを指定して送信
                $regId = $row['push_token'];
                if(!$this->send($regId,$message)){
                    throw new Exception("push送信失敗");
                }
            }
            // error_reportingを元に戻す
            error_reporting($E);

        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * RegistrationIDを指定して送信
    *
    * @accsess public
    * @param string $regId 
    * @param string $message push通知のメッセージ内容
    * @return boolean
    */
    private function send($regId,$message){
        try{
            $rq = new HTTP_Request("http://android.googleapis.com/gcm/send");
                $rq->setMethod(HTTP_REQUEST_METHOD_POST);
                $rq->addHeader("Authorization", "key=".API_KEY1);
                $rq->addPostData("collapse_key", "1");
                $rq->addPostData("data.message", $message);
                $rq->addPostData("registration_id", $regId);
                if (!PEAR::isError($rq->sendRequest())) {
                    print "\n" . $rq->getResponseBody();
                } else {
                    print "\nError has occurred";
                }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}
