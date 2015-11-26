<?php
/**
* Historyクラス
* 招待IDまわり
* @package History
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: History,v 1.0 2013/3/1Exp $
*/

Class History extends CommonBase {
    /**
    * コンストラクタ
    */
    public function __construct() {
        try{
            parent::__construct();
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /*
    * 招待IDのJSON出力
    * @access public
    * @param string $login_id ログインID
    * @return JSON 招待ID
    */
    public function outputInvitaionsID($login_id){
        try{
            $sql = "SELECT
                            invitation_id
                    FROM
                            data_users
                    WHERE
                            login_id = '".$login_id."' AND
                            deleted = 0
                    ;
                        ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            $data['invitation_id'] = $row['invitation_id'];
            $data['review_url'] = REVIEW_URL;
            $this->displayJsonArray($data);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
            die();
        }
    }
     /*
    * 招待ID使用履歴のインサート
    * @access public
    * @param string $login_id ログインID
    * @param string $invitation_id 招待ID
    * @boolean
    */
    public function historyInvitation($login_id,$invitation_id){
        try{
            //ユーザーID取得
            if(!$user_id = $this -> getUserID($login_id)){
                 throw new Exception ("ユーザーIDの取得に失敗しました");
            }
            //ユーザーKEY取得
            if(!$login_key = $this -> getLoginKey($login_id)){
                 throw new Exception ("ユーザーIDの取得に失敗しました");
            }
            //招待される側の招待ID取得
            if(!$this -> getInvitationID($login_id)){
                 throw new Exception ("ユーザーIDの取得に失敗しました");
            }
            //招待側のユーザー情報取得
            if(!$user_array = $this -> checkInvitationsID($invitation_id)){
                throw new Exception ("ユーザー情報取得失敗");
            }
            //招待テーブルにインサート
            if(!$this -> insertInvitationID($invitation_id,$user_id)){
                throw new Exception ("招待テーブルにインサート失敗");
            }
            $array = array();
            /**
            *   招待数３未満なら招待する側もポイント付与 
            *                                           
            **/
            //招待数を取得
            $invitation_cnt = $this -> getInvitationIDCnt($invitation_id);
            if($invitation_cnt <= 3){
                $array["point_type"] = 3;
                $array['achieve_id'] = 0;
                $array['point'] = INVITATION_POINT;
                $array['sub_point'] = 0;           
                $array['identifier'] = $user_array['login_id'];
                $array['accepted_time']      = date("Y-m-d H:i:s", time());
                $array['advertisement_key']  = 0;
                $array['campaign_name']      = "友達招待";
                $array['bank_status'] = 1;
                if(!$this -> addUserPoints($user_array['login_id'],$user_array['login_key'],$array)){
                    throw new Exception("友達招待失敗");
                }
            }
            $array["point_type"] = 4;
            $array['achieve_id'] = 0;
            $array['point'] = INVITATION_POINT;
            $array['sub_point'] = 0;
            $array['identifier'] = $login_id;
            $array['accepted_time']      = date("Y-m-d H:i:s", time());
            $array['advertisement_key']  = 0;
            $array['campaign_name']      = "招待ID使用";
            if(!$this -> addUserPoints($login_id,$login_key,$array)){
                throw new Exception("招待ID使用失敗");
            }
            //成功通知
            $data['notice'] = "true";
            $this->displayJsonArray($data);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
            die();
        }
    }
    /**
    * 招待数取得
    *
    * @accsess public
    * @param string $invitation_id 招待ID
    * @return int 
    */
    public function getInvitationIDCnt($invitation_id){
        try{
            $sql = "SELECT
                            invitation_id 
                    FROM
                            data_invitations
                    WHERE
                            invitation_id = '".$invitation_id."' AND
                            deleted = 0
                    ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            return $result_count;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
            die();
        }
    }
    /**
    * 招待テーブルにインサート
    *
    * @accsess public
    * @param string $invitation_id 招待ID
    * @return string 
    */
    public function insertInvitationID($invitation_id,$invitations_user_id) {
        try {
            $serialize_data = "";
            $sql = "INSERT INTO
                                data_invitations
                    SET
                                invitation_id = '".$invitation_id."',
                                user_id = '".$invitations_user_id."',
                                serialize_data  = '".$serialize_data."',
                                status = 1,
                                created = now()

                        ;";
            if(!mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception ("DBインサート失敗".$sql);
            }
            return true;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 招待IDを取得
    *
    * @accsess public
    * @param string $login_id
    * @return boolean
    */
    public function getInvitationID($login_id){
        try{
            //ユーザーID取得
            if(!$user_id = $this -> getUserID($login_id)){
                 throw new Exception ("ユーザーIDの取得に失敗しました");
            }
            $sql = "SELECT
                            invitation_id
                    FROM
                            data_invitations
                    WHERE
                            user_id = '".$user_id."' AND
                            deleted = 0 limit 1 ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $result_count = mysql_num_rows($result);
            if($result_count==1){
                $data['notice'] = "used";
                $this->displayJsonArray($data);
                die();
            } else if($result_count==0){
                return true;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 招待IDが存在するかチェック
    *
    * @accsess public
    * @param string $invitation_id 
    * @return boolean
    */
    public function checkInvitationsID($invitation_id){
        try{
           $sql = "SELECT 
                            user_id,
                            login_id,
                            login_key
                   FROM 
                            data_users 
                   WHERE
                            invitation_id = '".$invitation_id."' AND 
                            deleted = 0 limit 1 ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception (mysql_error().$sql);
            } 
            $result_count = mysql_num_rows($result);
            if($result_count == 0){
                $data['notice'] = "is not invitation_id";
                $this->displayJsonArray($data);
                die();
            } else if($result_count > 0){
                $row = mysql_fetch_assoc($result);
                return $row;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}//End Class
