<?php
 Class SendMail extends CommonBase{
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

   public function sendMail($array){
        try {
            CreateLog::putDebugLog("name ".$array['name']);
            CreateLog::putDebugLog("inquiry_type ".$array['inquiry_type']);
            CreateLog::putDebugLog("mail ".$array['mail']);
            CreateLog::putDebugLog("identifier ".$array['identifier']);
            CreateLog::putDebugLog("text ".$array['text']);

            //$to1 = "agsaruwatari@gmail.com";
            $to1 = "dig@clubnets.jp";
            $subject1 = "【ユーザー問い合わせ】ポイントDIG";
            $from1 = "From: dig@clubnets.jp";
            $body1  = $array['name']."様より、". $array['inquiry_type']."がありました。\n\n";
            $body1 .= "メールアドレス：".$array['mail']."\n";
            $body1 .= "ログインID：".$array['identifier']."\n";
            $body1 .= "------------------ 以下、送信内容 ------------------\n";
            $body1 .= $array['text'];

            mb_language("Japanese");
            mb_internal_encoding("UTF-8");
            mb_send_mail($to1, $subject1, $body1, $from1);

            $to2 = $array['mail']; //宛先
            $from2 = "From: dig@clubnets.jp";//送り主
            $subject2 = "【ポイントDIG】お問い合わせありがとうございます";//タイトル
            $body2 = "＜自動返信メール＞\n\n";
            $body2 .= $array['name']."様\n\n";
            $body2 .= "この度は、「ポイントDIG」にお問い合わせ頂き、誠にありがとうございます。\n";
            $body2 .= "お問い合せ内容を確認しておりますので、今しばらくお待ちくださいますよう、よろしくお願い申し上げます>。\n";
            $body2 .= "今後ともポイントDIGをよろしくお願い致します。\n";
            $body2 .= "ポイントDIG担当事務局\n";
            $body2 .= "------------------ 以下、送信内容 ------------------\n";
            $body2 .= $array['text'];

            mb_send_mail($to2, $subject2, $body2, $from2);

            return true;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
   }
}

