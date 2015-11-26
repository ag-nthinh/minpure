<?php
    /**
     * utils.php 
     *  便利関数
     *
     * @author donguri
     * @version 1.0
     */
class Utils {


    /**
     * 暗号化関数
     *  mcryptが使用できることが前提
     *  $strに対して$crypt_keyをキーに3DESで暗号化。その後base64エンコードを施す。
     * @param $str 文字列
     * @param $crypt_key キー
     * @param $mode モード 'plain':プレーンなBase64エンコードを行う／'url':URLセーフなBase64エンコードを行う
     * @return 暗号化された文字列
     */
    public static function str_encrypt($str,$crypt_key=SECRET_KEY,$mode='url') {
        $crypt_key=substr($crypt_key,0,24);
        $type="tripledes";
        $td = mcrypt_module_open($type, '', 'ecb', '');
        if ($td==false) {
            return false;
        }
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $crypt_key, $iv);
        if($mode=='plain'){
            $cryptdata=base64_encode(mcrypt_generic($td, $str));
        }elseif($mode=='url'){
            $cryptdata=self::base64url_encode(mcrypt_generic($td, $str));
        }
        //App::writeFileLog( "debug_log","str_encrypt($str -> $cryptdata)",DEBUG_LEVEL_DEVELOP);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $cryptdata;
    }
    
    /**
     * 複合化関数
     *  mcryptが使用できることが前提
     *  $crepted_strに対してbase64デコードした後、$crypt_keyをキーに3DESで復号化。
     * @param $crypted_str 暗号化された文字列
     * @param $crypt_key キー
     * @param $mode モード 'plain':プレーンなBase64デコードを行う／'url':URLセーフなBase64デコードを行う
     * @return 複合化された文字列
     */
    public static function str_decrypt($crypted_str,$crypt_key=SECRET_KEY,$mode='url') {
        $crypt_key=substr($crypt_key,0,24);
        $type="tripledes";
        $td = mcrypt_module_open($type, '', 'ecb', '');
        //App::writeFileLog( "debug_log","str_decrypt($crypted_str)",DEBUG_LEVEL_DEVELOP);
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $result=mcrypt_generic_init($td, $crypt_key, $iv);
        if($mode=='plain'){
            $decrypted_data=mdecrypt_generic($td,base64_decode($crypted_str));
        }elseif($mode=='url'){
            $decrypted_data=mdecrypt_generic($td,self::base64url_decode($crypted_str));
        }
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //App::writeFileLog( "debug_log","str_decrypted($decrypted_data)",DEBUG_LEVEL_DEVELOP);
        return trim($decrypted_data);
    }

    /**
     * 文字列をURLセーフにBase64エンコードする。
     * パディングは削除する。
     * @param $str 文字列
     * @return エンコードされた文字列
     */
    public static function base64url_encode($str){
        $str=str_replace(array('+','/','='), array('-','_',''), base64_encode($str));   //パディング削除
        return $str;
    }
    
    /**
     * URLセーフなBase64文字列をデコードする。
     * パディングは文字列の長さに応じて付加する。
     * @param $str 文字列
     * @return デコードされた文字列
     */
    public static function base64url_decode($str){
        $str=$str.str_repeat("=",strlen($str) % 4); //パディング
        $str=base64_decode(str_replace(array('-','_'), array('+','/'), $str));
        return $str;
    }

}


