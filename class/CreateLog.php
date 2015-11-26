<?php
/**
* CreateLogクラス
* ログの書出し
* @package CreateLog
* @author 金丸 祐治
* @since PHP 5.4.8
* @version $Id: CreateLog.php,v 1.0 2012/11/16Exp $
*/
Class CreateLog {
    /**
    * エラーログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putErrorLog($message){
        $log_path = LOG_DIRECTORY_PATH."error/".date('Y-m-d').".log";
        self::putLogFile($log_path,$message);
    }
    /**
    * 実行ログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putNoticeLog($message){
        $log_path = LOG_DIRECTORY_PATH."notice/".date('Y-m-d').".log";
        self::putLogFile($log_path,$message);
    }
    /**
    * デバッグログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putDebugLog($message){
        if(DEBUG_LOG_FLG){
            $log_path = LOG_DIRECTORY_PATH."debug/".date('Y-m-d').".log";
            self::putDebugLogFile($log_path,$message);
        }
    }
    /**
    * Tweetログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putTweetCntLog($message){
        $log_path = LOG_DIRECTORY_PATH."tweet_cnt/".date('Y-m-d').".log";
        self::putTweetCntLogFile($log_path,$message);
    }
    /**
    * clickログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putClickLog($message){
        $log_path = LOG_DIRECTORY_PATH."click/".date('Y-m-d').".log";
        self::putTweetCntLogFile($log_path,$message);
    }
    /**
    * giftcodeログ出力
    *
    * @accsess public
    * @param string $message ログに書き出す文章
    */
    public static function putCodeLog($message){
        $log_path = LOG_DIRECTORY_PATH."code/".date('Y-m-d').".log";
        self::putCodeLogFile($log_path,$message);
    }
    /**
    * ログ出力
    *
    * @accsess private
    * @param string $log_path ログファイルのパス
    * @param string $message  ログに書き出す文章
    */
    private static function putLogFile($log_path,$message){

        global $_COLLECT_CODE;      //一連の処理をまとめるグローバル関数
        $remort_addr = @$_SERVER["REMOTE_ADDR"];
        $user_agent  = @$_SERVER['HTTP_USER_AGENT'];
        if(empty($remort_addr)){
            $remort_addr = "0.0.0.0";
        }
        if(empty($user_agent)){
            $user_agent = "-";
        }
        $fp = fopen($log_path, "a");
            $log = date('Y-m-d H:i:s')." ".$_COLLECT_CODE." ".$remort_addr." ".$message." ".$user_agent;
        fwrite( $fp, $log."\n" );
        fclose( $fp );
    }
    /**
    * デバッグログ出力
    *
    * @accsess private
    * @param string $log_path ログファイルのパス
    * @param string $message  ログに書き出す文章
    */
    private static function putDebugLogFile($log_path,$message){
        global $_LOG_CNT;
        $fp = fopen($log_path, "a");
        $log = date('Y-m-d H:i:s')." [".$_LOG_CNT++."]".$message;
        fwrite( $fp, $log."\n" );
        fclose( $fp );
    }
    /**
    * Tweetログ出力
    *
    * @accsess private
    * @param string $log_path ログファイルのパス
    * @param string $message  ログに書き出す文章
    */
    private static function putTweetCntLogFile($log_path,$message){
        $fp = fopen($log_path, "a");
        $log =$message;
        fwrite( $fp, $log."\n" );
        fclose( $fp );
    }
    /**
    * clickログ出力
    *
    * @accsess private
    * @param string $log_path ログファイルのパス
    * @param string $message  ログに書き出す文章
    */
    private static function putClickLogFile($log_path,$message){
        $fp = fopen($log_path, "a");
        $log =$message;
        fwrite( $fp, $log."\n" );
        fclose( $fp );
    }
    /**
    * giftcodeログ出力
    *
    * @accsess private
    * @param string $log_path ログファイルのパス
    * @param string $message  ログに書き出す文章
    */
    private static function putCodeLogFile($log_path,$message){
        $fp = fopen($log_path, "a");
        $log =$message;
        fwrite( $fp, $log."\n" );
        fclose( $fp );
    }

}
