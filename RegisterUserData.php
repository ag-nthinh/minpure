<?php
/**
* RegisterUserDataクラス
* ユーザー登録クラス:
* @package RegisterUserData
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: RegisterUserData,v 1.0 2012/12/13Exp $
*/

Class RegisterUserData extends CommonBase {
    /**
    * コンストラクタ
    */
    public function __construct() {
        global $_COLLECT_CODE;      //一連の処理をまとめるグローバル関数
        $_COLLECT_CODE = CreateRand::getRandomString();
        try {
            $this->checkRegisterPass();
            if(!$this->connectDataBase()) {
                $data['error'] = "false";
                $this->displayJsonArray($data);
                throw new Exception("DB接続失敗で異常終了しました。");
            }
            if($registed_key = $this->isRegistered()) {
                $data['login_key'] = $registed_key;
                //$data['error'] = "false";
                $this->displayJsonArray($data);
            }
            if(!$user_key=$this->registerData()) {
                $data['error'] = "false";
                $this->displayJsonArray($data);
                throw new Exception("ユーザー登録失敗");
            } else{
                $data['login_key'] = $user_key;
                $this->displayJsonArray($data);
            }
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /**
    * USERデータ登録
    *
    * @accsess private
    * @return boolean
    */
    private function registerData() {
        try {

            $login_id   = CommonValidate::validateLoginId($_POST['login_id']);
            $login_key  = CreateRand::getRandomString(40);
            $login_pass = "";
            $token = "";
            $invitation_id = CreateRand::getRandomInvitationId(5);//招待ID
            $point_array = array(
                            date('Y-m',strtotime('-11 month')) => 0 ,
                            date('Y-m',strtotime('-10 month')) => 0 ,
                            date('Y-m',strtotime('-9 month')) => 0 ,
                            date('Y-m',strtotime('-8 month')) => 0 ,
                            date('Y-m',strtotime('-7 month')) => 0 ,
                            date('Y-m',strtotime('-6 month')) => 0 ,
                            date('Y-m',strtotime('-5 month')) => 0 ,
                            date('Y-m',strtotime('-4 month')) => 0 ,
                            date('Y-m',strtotime('-3 month')) => 0 ,
                            date('Y-m',strtotime('-2 month')) => 0 ,
                            date('Y-m',strtotime('-1 month')) => 0 ,
                            date('Y-m',strtotime('now')) => 0);

            $point_list = serialize($point_array);
          //  $token = CommonValidate::validateUserToken($_POST['token']);
            $serialize_data = "";
            //トランザクション開始
            if(!mysql_query("begin;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            }
            $ip_address = $_SERVER["REMOTE_ADDR"];

            $sql = "INSERT INTO data_users(
                          login_id,
                          login_key,
                          point_list,
                          push_token,
                          ip_address,
                          serialize_data,
                          created,
                          invitation_id
                        ) values ( 
                          '".$login_id."',
                          '".$login_key."',
                          '".$point_list."',
                          '".$token."',
                          '".$ip_address."',
                          '".$serialize_data."',
                          now(),
                          '".$invitation_id."'
                        );";

            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception("DBにインサートできませんでした。".mysql_error().$sql);
            }
            //SNSテーブルにユーザー情報登録
            if(!$this->registerSNS($login_id)){
                throw new Exception("SNSテーブルにユーザー情報を登録できませんでした");
            }
            //トランザクション完了
            if(!mysql_query("commit;",$this->getDatabaseLink())){
                throw new Exception("トランザクション失敗");
            } 
            return $login_key;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            mysql_query("rollback;",$this->getDatabaseLink());
            return false;
        }
    }
     /*
    * SNSテーブルにユーザー情報登録
    * @access private
    * @param string $login_id ログインID
    * @boolean
    *
    */
    private function registerSNS ($login_id){
        try {
             if(!$user_id = $this -> getUserID($login_id)){
                 throw new Exception ("ユーザーIDの取得に失敗しました");
             }
             $sql = "INSERT INTO
                        data_snsflgs
                     SET
                        user_id = '".$user_id."',
                        created = now(),
                        updated = now();";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception ("DBインサート失敗");
            }
            return true;
        } catch (Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /*
     * 既に登録済みのユーザーに対する処理
     * とりあえずID,パス,tokenがDBに存在してたら登録済みと判断してみる
     * access private
     * return boolean
     */
    private function isRegistered(){
        try {
              if(isset($_POST['login_id'],$_POST['register_pass'],$_POST['login_key'])) {
                $login_id = CommonValidate::validateLoginId($_POST['login_id']);
                //DBに登録されているかどうかチェック
                $sql = "SELECT
                                login_key
                        FROM
                                data_users
                        WHERE
                                login_id = '".$login_id."'
                        AND
                                deleted = 0;";
                $result = mysql_query($sql,$this->getDatabaseLink());
                $num_rows = mysql_num_rows($result);
                if($num_rows >0){
                    $row = mysql_fetch_assoc($result);
                   return $row['login_key'];
                    //return true;
                }else{
                   throw new Exception(mysql_error()); 
                }
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }

    }
    /**
    *  register_passのチェック
    *  login_idと秘密鍵をsha256でハッシュ化して比較
    *  access private
    *  return boolean
    */
    private function checkRegisterPass(){
        $user_id       = CommonValidate::validateLoginId($_POST['login_id']);
        $register_pass = h($_POST['register_pass']);
        $sha           = hash_hmac('sha256',$user_id."nise4649",false);
        if($register_pass === $sha){
            return true;
        }else{
            return false;
        }

    } 
}//End of class
