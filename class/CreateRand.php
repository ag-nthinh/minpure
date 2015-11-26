<?php
/**
* CreateRandクラス
* ランダム関連
* @package CreateRand
* @author 金丸 祐治
* @since PHP 5.4.3
* @version $Id: CreateLog.php,v 1.0 2012/11/16Exp $
*/
Class CreateRand {
    /**
    * ランダムな文字列を生成する
    *
    * @param int $length 必要な文字列長。省略すると20文字
    * @return String ランダムな文字列
    */
    public static function getRandomString($length = 20){
        $char_list = "0123456789abcdefghijklmnopqrstuvwxyz";
        mt_srand();
        $sRes = "";
        for($i = 0; $i < $length; $i++)
            $sRes .= $char_list{mt_rand(0, strlen($char_list) - 1)};
        return $sRes;
    }
    
    public static function getRandomInvitationId($length){
        $char_list = "023456789abcdefghijkmnopqrstuvwxyz";
        mt_srand();
        $sRes = "";
        for($i = 0; $i < $length; $i++)
            $sRes .= $char_list{mt_rand(0, strlen($char_list) - 1)};
        return $sRes;
    }

}//End of class
