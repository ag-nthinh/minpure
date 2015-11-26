<?php
/**
  * AdminLoginクラス
  * 管理画面のログイン周りをまとめたクラス
  * @package AdminLogin
  * @author 氏名 平野　雅也
  * @since PHPバージョン
  * @version $Id: ファイル名,v 1.0 2013年/8月/11日Exp $
 */

Class AdminLogin extends CommonBase{
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
            die("false");
        }
    }
    /**
    * ログイン処理
    *
    * @accsess public
    */
    public function checkAuth(){
        if (isset($_POST["l_id"]) and isset($_POST["l_pass"])) {
            if ($_POST["l_id"] == ADMIN_LOGIN_ID and $_POST["l_pass"] == ADMIN_LOGIN_PASS) {
                $_SESSION["auth"] = ADMIN_SESSION;
                return true;
            } else {
                return false;
            } 
        } else {
            if (!isset($_SESSION["auth"])) {
                return false;
            } else {
                if ($_SESSION["auth"] == ADMIN_SESSION) {
                    return true;
                } else {
                    return false;
                }
            }
        }

    }
}
