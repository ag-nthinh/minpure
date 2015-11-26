<?php
/**
* RegisterUdidクラス
* UDIDと流入情報を登録するクラス
* @package RegisterUdid
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: v 1.0 2013/05/22Exp $
*/
Class RegisterUdid extends CommonBase {
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
    /**
    * UDIDとportalの保存
    *
    * @accsess public
    * @return boolean
    */
    public function registerUdid($login_id){
        try {

            // 以下のUDIDは強制終了
            // 2013/10/14～20で10回以上新規登録されている
            $ng_udid = array(

                    );

            foreach ($ng_udid as $value){
                if($value == $_POST['udid']){
                    echo "NG ".$value;
                    exit();
                }
            }


            $ng_wifi = array(

                   );


            foreach ($ng_wifi as $value){
                if($value == $_POST['wifi']){
                    echo "NG ".$value;
                    exit();
                }
            }


            $ng_mac = array(
"non_array"
                    );

            foreach ($ng_mac as $value){
                if($value == $_POST['mac_address']){
                    echo "NG ".$value;
                    exit();
                }
            }

            
            $where2="";
            if($_POST['udid']){
                $where2 .= " OR udid='".$_POST['udid']."'";
            }
            if($_POST['wifi']){
                $where2 .= " OR wifi='".$_POST['wifi']."'";
            }
            if($_POST['mac_address']){
                $mr = str_replace(":,", "", $_POST['mac_address']); //計測したMACアドレスから :, (計測値なし)を除く
                if(strpos($mr, ':')){ // : が文字列内にある ＝ 計測値がある
                    $where2 .= " OR mac_address='".$_POST['mac_address']."'";
                }
            }

            $sql2="SELECT
                        user_id
                    FROM
                        data_users
                    WHERE login_id='".$login_id."'".$where2.";";

            if(!$result = mysql_query($sql2,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $duplicate_id="";
            while($row = mysql_fetch_assoc($result)){
                $duplicate_id .= $row['user_id'].",";
            }
            $duplicate_id = trim($duplicate_id, ",");
            $ip = $_SERVER["REMOTE_ADDR"];

            $sql="UPDATE
                        data_users
                  SET
                        udid='".$_POST['udid']."',
                        wifi='".$_POST['wifi']."',
                        mac_address='".$_POST['mac_address']."',
                        ip_address='".$ip."',
                        duplicate_id='".$duplicate_id."',
                        portal=".$_POST['portal']."
                  WHERE
                        login_id='".$login_id."';";
            if(!mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
}
