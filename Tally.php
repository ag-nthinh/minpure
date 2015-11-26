<?php
/**
* Tallyクラス
* 集計クラス
* @package Tally
* @author PHP 5.4.8
* @sice $id: Tally.php v 1.0 2013/3/26Exp $
*/
Class Tally extends CommonBase{
    private $interesting_array;
    private $appdriver_array;
    private $careward_array;
    private $camobile_array;
    private $zucksreward_array;
    private $gmotech_array;
    private $gree_array;
    private $sales_array;
    private $metaps_array;
    private $hangout_array;
    private $phalanx_array;
    /*
    コンストラクタ
        */
    function __construct(){
        try{
            if(!$this->connectDataBase()) {
                throw new Exception("DB接続失敗で異常終了しました。");
            }
            $this->appdriver_array = array();
            $this->careward_array = array();
            $this->camobile_array = array();
            $this->zucksreward_array = array();
            $this->gmotech_array = array();
            $this->interesting_array = array();
            $this->gree_array = array();
            $this->sales_array = array();
            $this->metaps_array = array();
            $this->hangout_array = array();
            $this->phalanx_array = array();
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
        }
    }
    /**
    * メール関数
    *
    */
    public function tallySendMail($subject,$message){
        //メッセージ内容をメールで送信する
       //$host="masadon0621@gmail.com";
       $host = "dev@agentgate.jp, tkitagawa@agentgate.jp";
       //$host = "ssaruwatari@agentgate.jp";
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        mb_send_mail($host, $subject, $message, "From:giftget@agentgate.jp");
    }
    public function speedTallyLog(){
        try{
            $message="～～～～～～～～～～メール本文～～～～～～～～～～～";
            //0時から現在の時間までの集計データ
            $now_time = date("Y-m-d H:i:s");
            $term = " created >= date(now()) AND created <= '".$now_time."' ";
            $message .= "\n【".date("H時i分")." 速報】";
            $message .= $this->TallyData($term);
            $subject = "【ギフトGET】売上速報（時速）"; 
            $this->tallySendMail($subject,$message);
        } catch(Exception $e){
           CreateLog::putErrorLog(get_class()." ".$e->getMessage()); 
        }
    }
    /**
    * 日別・月別で集計データをとりメール送信
    * 
    */
    public function tallyLog($type = 1){
        try{

            $message="～～～～～～～～～～メール本文～～～～～～～～～～～";
            //dailyの集計
            $now_time = date("Y-m-d H:i:s");
            switch($type){
                case 1:
                    $type = "AND updated < date(now()) ";
                    $term = " updated >= date(current_timestamp - interval 1 day) AND updated < date(now()) ";
                   // $term = " created >= '2013-08-1'AND created < '2013-08-02' ";
                    break;
                case 2:
                //    $type = "AND created <= '".$now_time."' ";
                    $term = " updated >= date(now()) AND updated <= '".$now_time."' ";
                    break;
            }
            $message .= "\n".date("【m月d日",strtotime("-1 day"))." 速報】";
         //   $term = " created >= date(current_timestamp - interval 1 day) ".$type;
            $this->sales_array['new_app']=0;
            $this->sales_array['new_web']=0;
            $message .= $this->TallyData($term);
            $message .="\n～～～～～～～～～～～～～～～～～～～～～～～～～～";
            $message .= "\n".$this->sales_array['sale'];
            $message .= "\n".$this->sales_array['totalcost'];
            $message .= "\n".$this->sales_array['incentive'];
            $message .= "\n".$this->sales_array['sns_point'];
            $message .= "\n".$this->sales_array['invitation_point'];
            $message .= "\n".$this->sales_array['user_invitations'];
            $message .= "\n".$this->sales_array['review_point'];
            $message .= "\n".$this->sales_array['special_point'];
            $message .= "\n".$this->sales_array['login_stamp'];
            $message .= "\n".$this->sales_array['gros_margin'];
            $message .= "\n".$this->sales_array['gros_margin_rate']/100;
            $message .= "\n".$this->sales_array['gacha_cnt'];
            $message .= "\n".$this->sales_array['new_app'];
            $message .= "\n".$this->sales_array['new_web'];
            $message .= "\n".$this->sales_array['action_app'];
            $message .= "\n".$this->sales_array['action_web'];
            $message .= "\n".$this->sales_array['unique_install'];
            //monthlyの集計
            $message .= "\n\n【".date("n月分累計 速報】",strtotime("-1 day"));
            $term = " updated >= DATE_FORMAT(now(),'%Y-%m-01') AND updated < date(now()) ";
            //$term = " created >='2013-09-01' and created <'2013-10-01' ";
            $this->sales_array['new_app']=0;
            $this->sales_array['new_web']=0;
            $message .= $this->TallyData($term);

            $message .="\n～～～～～～～～～～～～～～～～～～～～～～～～～～";
            $message .= "\n".$this->sales_array['sale'];             
            $message .= "\n".$this->sales_array['totalcost'];
            $message .= "\n".$this->sales_array['incentive'];
            $message .= "\n".$this->sales_array['sns_point'];
            $message .= "\n".$this->sales_array['invitation_point'];
            $message .= "\n".$this->sales_array['user_invitations'];
            $message .= "\n".$this->sales_array['review_point'];
            $message .= "\n".$this->sales_array['gros_margin'];
            $message .= "\n".$this->sales_array['gros_margin_rate']/100;
            $message .= "\n".$this->sales_array['gacha_cnt'];
            $message .= "\n".$this->sales_array['new_app'];
            $message .= "\n".$this->sales_array['new_web'];
            $message .= "\n".$this->sales_array['action_app'];
            $message .= "\n".$this->sales_array['action_web'];
            $message .= "\n".$this->sales_array['unique_install'];
          
            $subject = "【ギフトGET】売上速報（日別・月別）";
            //集計データをメールで送る
            $this->tallySendMail($subject,$message);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 日別、月別の集計データをとる
    *
    * @param string $term 日別又は月別
    * @return boolean false
    * @return string message
    */
    public function TallyData($term){
        try{
            $message="";
            $sales_data = array();
            //デイリー又は月刊の売上、粗利、原価、粗利率
            $sales_data = $this->grosMargin($term);
            $user_invitations = $this->invitations($term);     
            $invitation_point = $this->frendInvitationPoint($term,0);
            $review_point = $this->frendInvitationPoint($term,99999);
            $special_point = $this->cost($term,99);
            $sales_data['totalcost'] += $special_point;

            $sum_present = $this->sumPresent($term);
            $sum_code=0;
            foreach($sum_present as $key => $value){
                $sum_code += $value;
            }
            $gift_cost = $sum_code * 1.08;
            $message .= "\n原価一覧";
            $message .= "\n[1]Amazon300:".CommonBase::putMoney($sum_present[1]);
            $message .= "\n[2]Amazon500:".CommonBase::putMoney($sum_present[3]);
            $message .= "\n[3]iTunes500:".CommonBase::putMoney($sum_present[2]);
            $message .= "\n[4]PeX3000:".CommonBase::putMoney($sum_present[4]);
            $message .= "\n[5]PeX5000:".CommonBase::putMoney($sum_present[5]);
            $message .= "\n[6]Vプリカ500:".CommonBase::putMoney($sum_present[6]);
            $message .= "\n[7]手数料:".CommonBase::putMoney($sum_code*0.08);
            $message .="\n\n[1]+[2]+[3]+[4]+[5]+[6]+[7]=".CommonBase::putMoney($gift_cost)."(>原価合計)";

            $message .="\n\n売上:".CommonBase::putMoney($sales_data['sale']);
            $message .="\n原価:".CommonBase::putMoney($gift_cost);
            $gros_margin=$sales_data['sale']-$gift_cost;
            $message .="\n粗利:".CommonBase::putMoney($gros_margin);
            //粗利0の場合
            if($gros_margin < 0){
                $gros_margin_rate = $gift_cost/$sales_data['sale'] *100;
            } else{
                $gros_margin_rate =  100*round(1 - ($gift_cost/$sales_data['sale']),2);
            }
            $message .="\n粗利率:".$gros_margin_rate."%";
            ////////////////////集計用////////////////////////////////////////
            $this->sales_array['sale']=$sales_data['sale'];
            $this->sales_array['totalcost']=$sales_data['totalcost'];
            $this->sales_array['incentive']=$sales_data['incentive'];
            $this->sales_array['sns_point']=$sales_data['sns_point'];
            $this->sales_array['game_point']=$sales_data['game_point'];
            $this->sales_array['invitation_point']=$invitation_point;
            $this->sales_array['user_invitations']=$user_invitations[1];
            $this->sales_array['review_point']=$review_point;
            $this->sales_array['special_point']=$special_point;;
            $this->sales_array['login_stamp']=$sales_data['login_stamp'];
            $this->sales_array['gros_margin']=$gros_margin;
            $this->sales_array['gros_margin_rate']=$gros_margin_rate;
            
            //////////////////////////////////////////////////////////////////
 
            $message .= "\n\n".$this->dailyGacha($term);
            //tweet数取得
            if (!($fp = fopen (dirname(__FILE__)."/../logs/tweet_cnt/".date("Y-m-d",strtotime("-1 day")).".log", "r" ))) {
               throw new Exception("数取得失敗"); 
            }
            $load = fgets($fp);
            $message .="\n".$load;
 //           $message .= "\n".$this->contributionSNS($term);
           //デイリー又は月間の案件DL数（アプリとWEB）
            $message .= $this->dlAdvertisements($term)."\n";
            //案件をインストールしてくれたユーザーの数(合計)
            $start_time = " updated >='".date("Y-m")."' ";
           // $start_time = " created >='2013-04-24 17:00:00' ";
            $all_unique_install=$this->uniqueCntInstall($start_time);
            //案件をインストールしてくれたユーザーの数(期間)
            $unique_install=$this->uniqueCntInstall($term);
            $message.="\n当月の稼働人数：".$all_unique_install."人";
            $message.="\n日別の稼働人数：".$unique_install."人";

            $message .= "\n".$this->quantityCode(1);//Ama券
            $message .= "\n".$this->quantityCode(2);//iTunes
           
            $this->sales_array['unique_install']=$unique_install; 
            $message .="\n";
            $message .= "\n\nポイントはき出し一覧";
            $message .= "\n[1]デイリーガチャ:".$sales_data['sns_point']."P";
            $message .= "\n[2]PAGすごろく:".$sales_data['game_point']."P";
            $message .= "\n[3]インセンティブ:".$sales_data['incentive']."P";
            //$message .= "\n[3]友達招待:".$invitation_point."P";
            //$message .= "\n[4]友達招待ID使用:".$user_invitations[1]."P";
            //$message .= "\n[5]レビュー:".$review_point."P";
            $message .= "\n[4]特別付与ポイント:".$special_point."P";
            $message .= "\n[5]ログインスタンプ付与ポイント:".$sales_data['login_stamp']."P";
            $message .="\n\n[1]+[2]+[3]+[4]+[5]=".$sales_data['totalcost']."P （ポイント吐き出し合計）";
            $message .="\n";
           /*
            * デイリー又は月間の新規案件追加数
            * $i = 1 インタレスティング
            * $i = 2 AppDriver
            * $i = 3 CAReward
            * $i = 4 ZucksReward
            * $i = 5 SmaAD
            * $i = 6 InfoNear
            * $i = 7 GREE Ads Reward
            * $i = 8 問い合わせ案件
            * $i = 9 自社問い合わせ案件
            * $i = 10 Metaps
            * $i = 11 MacroLine
            * $i = 12 いいね案件
            * $i = 14 camad
            * $i = 15 ファイブエニー
            * $i = 16 affiTown
            * $i = 17 PAG!
            */

           $message .= $this -> newAdvertisements(1,$term)."\n";
           $message .= $this -> newAdvertisements(2,$term)."\n";
           $message .= $this -> newAdvertisements(3,$term)."\n";
           $message .= $this -> newAdvertisements(4,$term)."\n";
           $message .= $this -> newAdvertisements(5,$term)."\n";
           $message .= $this -> newAdvertisements(7,$term)."\n";
           $message .= $this -> newAdvertisements(10,$term)."\n";
           $message .= $this -> newAdvertisements(11,$term)."\n";
           $message .= $this -> newAdvertisements(12,$term)."\n";
           $message .= $this -> newAdvertisements(14,$term)."\n";
           $message .= $this -> newAdvertisements(17,$term)."\n";
                        
            return $message;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 案件をインストールしてくれたユーザー数
    *
    * @param int $term
    * @return string $unique_install
    */
    private function uniqueCntInstall($term){
        try{
            $sql="SELECT
                        count(*)
                  FROM          
                        data_summaries 
                  WHERE     
                        ".$term."
                        AND status=2 
                        group by user_id ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $cnt = mysql_num_rows($result);
            return $cnt;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * Amazon&iTunesギフトコード待ちのユーザー
    *
    * @param int $present_id 
    * @return string $cnt
    */
    private function  waitingGiftCord($present_id){
        try{
            $sql="SELECT
                        count(*)
                  FROM  
                        data_distributions
                  WHERE 
                        status = 0 AND
                        present_id = ".$present_id.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            return $row['count(*)'];
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }    
    /**
    * Amazon&iTunesギフトコードの残数
    *
    * @param string $term 日別又は月別
    * @param string $code 1:Amazon/2:iTunesGift
    * @return string $num 
    */

    private function quantityCode($code){

        try{
           $sql = "SELECT
                        count(giftcord_id)
                   FROM 
                        data_giftcord
                   WHERE    
                        present_id = ".$code."
                        and status = 0 ;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            } 
            $num = mysql_fetch_row($result);
            $message = "";
            if($code == '1'){//Amazon
                $waitAmazon=$this->waitingGiftCord($code);
                $message .= "\nAmazonギフトコード待ちのユーザー数 :".$waitAmazon;
                $message .= "\nAmazonギフトコード残数 : ".$num[0];                 

            }else if($code == '2'){//iTunes
                $waitiTunes=$this->waitingGiftCord($code);
                $message .= "\niTunesギフトコード待ちのユーザー数 : ".$waitiTunes;
                $message .= "\niTunesギフトコード残数 : ".$num[0];
            }
            return $message;

        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
   
     /**
    * 招待コード利用数取得
    * @param string $term 日別又は月別 
    * @return string $num
    */

    private function invitations($term){
        try{
            $sql = "SELECT 
                        count(invitation_id) ,
                        count(invitation_id)*30 
                    FROM    
                        data_invitations
                    WHERE   
                        status = 1 
                        and ".$term.";";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $num = mysql_fetch_row($result);

            $message = "";
            
            $data[0]=$num[0];
            $data[1]=$num[1];
            return $num;
        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 友達招待数での掃出しポイント
    * @param string $term 日別又は月別
    * @return string $num
    */
    private function frendInvitationPoint($term,$summary_id){
        try{
            $sql = "SELECT
                        sum(app_point)
                    FROM
                        log_bankbooks
                    WHERE
                        point_type = 3 and
                        summary_id = ".$summary_id." and
                        ".$term.";";
        if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
            throw new Exception(mysql_error());
        }

        $num = mysql_fetch_assoc($result);
        return $num["sum(app_point)"];

        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 友達招待数取得（1ユーザー3人まで）
    * @param string $term 日別又は月別
    * @return string $num
    */
    private function frendInvitations($term){
        try{
            $sql = "SELECT
                        count(bankbook_id)
                    FROM        
                        log_bankbooks
                    WHERE   
                        point_type = 3 and  
                        summary_id = 0 and
                        ".$term.";";
            
            
        if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }

        $num = mysql_fetch_row($result);
        $message = "";
        $message .= "・友達招待数 : ".$num[0]."人";
       // $message .= "\n・友達招待ポイント+レビューポイント : ".$num[1]."ポイント";
        return $message;

        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    
    /**
    * デイリーガチャ使用回数取得（1ユーザー2回まで）
    * @param string $term 日別又は月別
    * @return string $num
    */

    private function dailyGacha($term){

        try{
            $sql = "SELECT
                        count(bankbook_id)
                    FROM
                        log_bankbooks
                    WHERE   
                        point_type = 2 and
                        ".$term.";";
           

        if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }

        $num = mysql_fetch_row($result);
        $message = "";
        $message .= "デイリーガチャ使用回数 : ".$num[0]."回";
        $this->sales_array['gacha_cnt']=$num[0];
        return $message;
        }catch(Exception $e){
            CreateLog::putErrorLog(get_class." ".$e->getMessage());
            die();
        }
    
    }

     /**
    * SNS投稿回数取得（1ユーザー1回まで）
    * @param string $term 日別又は月別
    * @return string $num
    */

    public function contributionSNS($term){

        try{
            $sql = "SELECT
                        count(sns_id)
                    FROM
                        data_snsflgs
                    WHERE
                        sns_flg = 0 and
                        ".$term.";";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }

            $num = mysql_fetch_assoc($result);
            $message = "";
            $message .= "Twitter投稿数 : ".$num['count(sns_id)']."回";
            $this->sales_array['twitter_cnt']=$num['count(sns_id)'];
            return $message; 
    
        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }

    }

     /**
    * 広告タイプの取得
    *
    * @param string $advertisement_key 
    * @param int $campany_type ASPの種類
    * @return boolean false
    * @return string message
    */
    private function typeAdvertisements($advertisement_key,$campany_type){
         try{
            $sql = "SELECT
                            advertisement_type
                    FROM
                            master_advertisements
                    WHERE
                            advertisement_key = '".$advertisement_key."' AND
                            campany_type = '".$campany_type."' LIMIT 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            return $row["advertisement_type"];
         } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 案件の種類ごとにDLされた数をカウント 
    *
    * @param string $term 日別又は月別
    * @param int $campany_type ASPの種類
    * @return array $data アプリとWEBの獲得数と売上
    */
    private function cntAdvertisements($term,$campany_type){
        try{
            $message="";
            $sales=0;
            $app_cnt=0;
            $web_cnt=0;
            
            $sql ="SELECT
                        advertisement_id, serialize_data
                   FROM
                        data_summaries
                   WHERE
                         ".$term." AND
                        status=2 AND
                        campany_type=".$campany_type.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
                if(!$advertisement_data=$this->getAdvertisementData($row['advertisement_id'])){
                    // 案件がDBにない＝PAG案件
                    $serialize_data = unserialize($row['serialize_data']);
                    $advertisement_data['margin'] = $serialize_data['payment'];
                    $advertisement_data['advertisement_type'] = 1;
                }
                $sales=$sales+$advertisement_data['margin'];
                switch($advertisement_data['advertisement_type']){
                    case 1:
                        $web_cnt++;
                        break;
                    case 2:
                        $app_cnt++;
                        break;
                    case 3:
                        $web_cnt++;
                        break;
                    case 4:
                        $app_cnt++;
                        break;
                }
            }
            $data['app_cnt']=$app_cnt;
            $data['web_cnt']=$web_cnt;
    //      $this->sales_array['new_web']=$web_cnt;
    //      $this->sales_array['new_app']=$app_cnt;

            if($campany_type==2 || $campany_type==5 || $campany_type==7 || $campany_type==11 || $campany_type==14){
                $sales = floor($sales/1.08);
            }
            $data['sales']=$sales;
            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * デイリー又は月間の新規案件追加数(ASPごとに)
    *
    * @param string $term 日別又は月別
    * @param int $campany_type ASPの種類
    * @return string message
    */
    private function newAdvertisements($campany_type,$term){
       try{
            $app_cnt=0;
            $web_cnt=0;
            $message="";
            $sql ="SELECT 
                            advertisement_type
                   FROM 
                            master_advertisements
                   WHERE 
                            ".$term." AND 
                            campany_type = '".$campany_type."' ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
                if($row['advertisement_type'] == 2 || $row['advertisement_type'] == 4){
                    $app_cnt++;
                } else{
                    $web_cnt++;
                }
            }
           
            $data = $this -> cntAdvertisements($term,$campany_type);
            //AppDriver案件の場合
            if($campany_type == 1){
                $message .= "\n【インタレスティング】";
                $this->appdriver_array['sales']=$data['sales'];
                $this->appdriver_array['app_dl']=$data['app_cnt'];
                $this->appdriver_array['web_dl']=$data['web_cnt'];
                $this->appdriver_array['app_cnt']=$app_cnt;
                $this->appdriver_array['web_cnt']=$web_cnt;
            //AppDriver案件の場合
            } else if($campany_type == 2){
                $message .= "\n【AppDriver(アドウェイズ)】";
                $this->appdriver_array['sales']=$data['sales'];
                $this->appdriver_array['app_dl']=$data['app_cnt'];
                $this->appdriver_array['web_dl']=$data['web_cnt'];
                $this->appdriver_array['app_cnt']=$app_cnt;
                $this->appdriver_array['web_cnt']=$web_cnt;
            //CA案件の場合
            } else if($campany_type == 3){
                $message .= "\n【CAReward(CAリワード)】";
                $this->careward_array['sales']=$data['sales'];
                $this->careward_array['app_dl']=$data['app_cnt'];
                $this->careward_array['web_dl']=$data['web_cnt'];
                $this->careward_array['app_cnt']=$app_cnt;
                $this->careward_array['web_cnt']=$web_cnt;
            //Zucks案件の場合
            } else if($campany_type == 4){
                $message .= "\n【ZucksAffiliate(Zucks)】";
                $this->zucksreward_array['sales']=$data['sales'];
                $this->zucksreward_array['app_dl']=$data['app_cnt'];
                $this->zucksreward_array['web_dl']=$data['web_cnt'];
                $this->zucksreward_array['app_cnt']=$app_cnt;
                $this->zucksreward_array['web_cnt']=$web_cnt;
            } else if($campany_type == 5){
                $message .= "\n【GMO SmaAD Reward(GMO TECH)】";
                $this->gmotech_array['sales']=$data['sales'];
                $this->gmotech_array['app_dl']=$data['app_cnt'];
                $this->gmotech_array['web_dl']=$data['web_cnt'];
                $this->gmotech_array['app_cnt']=$app_cnt;
                $this->gmotech_array['web_cnt']=$web_cnt;
            } else if($campany_type == 7){
                $message .= "\n【GREE Ads Reward(Glossom)】";
                $this->gree_array['sales']=$data['sales'];
                $this->gree_array['app_dl']=$data['app_cnt'];
                $this->gree_array['web_dl']=$data['web_cnt'];
                $this->gree_array['app_cnt']=$app_cnt;
                $this->gree_array['web_cnt']=$web_cnt;
            } else if($campany_type == 10){
                $message .= "\n【Metaps】";
                $this->metaps_array['sales']=$data['sales'];
                $this->metaps_array['app_dl']=$data['app_cnt'];
                $this->metaps_array['web_dl']=$data['web_cnt'];
                $this->metaps_array['app_cnt']=$app_cnt;
                $this->metaps_array['web_cnt']=$web_cnt;
            } else if($campany_type == 11){
                $message .= "\n【マクロライン(ハングアウト)】";
                $this->hangout_array['sales']=$data['sales'];
                $this->hangout_array['app_dl']=$data['app_cnt'];
                $this->hangout_array['web_dl']=$data['web_cnt'];
                $this->hangout_array['app_cnt']=$app_cnt;
                $this->hangout_array['web_cnt']=$web_cnt;
            } else if($campany_type == 12){
                $message .= "\n【うちナビいいね】";
                $this->hangout_array['sales']=$data['sales'];
                $this->hangout_array['app_dl']=$data['app_cnt'];
                $this->hangout_array['web_dl']=$data['web_cnt'];
                $this->hangout_array['app_cnt']=$app_cnt;
                $this->hangout_array['web_cnt']=$web_cnt;
            } else if($campany_type == 14){
                $message .= "\n【camad(シー・エー・モバイル)】";
                $this->camobile_array['sales']=$data['sales'];
                $this->camobile_array['app_dl']=$data['app_cnt'];
                $this->camobile_array['web_dl']=$data['web_cnt'];
                $this->camobile_array['app_cnt']=$app_cnt;
                $this->camobile_array['web_cnt']=$web_cnt;
            } else if($campany_type == 17){
                $message .= "\n【PAG!(ファランクス)】";
                $this->phalanx_array['sales']=$data['sales'];
                $this->phalanx_array['app_dl']=$data['app_cnt'];
                $this->phalanx_array['web_dl']=$data['web_cnt'];
                $this->phalanx_array['app_cnt']=$app_cnt;
                $this->phalanx_array['web_cnt']=$web_cnt;
           }
            $this->sales_array['new_app']+=$app_cnt;
            $this->sales_array['new_web']+=$web_cnt;
            $message .= "\n ・売上 : ".CommonBase::putMoney($data['sales']);
            $message .= "\n ・アプリ案件Action数： ".$data['app_cnt'];
            $message .= "\n ・WEB案件Action数： ".$data['web_cnt'];
            $message .= "\n ・新規アプリ案件追加数 : ".$app_cnt;
            $message .= "\n ・新規WEB案件追加数 : ".$web_cnt;
            return $message;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }    
    }
    /**
    * デイリー又は月間の案件DL数（アプリとWEB）
    * @param string $term 日別又は月別
    * @return string message
    */
    private function dlAdvertisements($term){
        try{
            $app_cnt=0;
            $web_cnt=0;
            $sql = "SELECT 
                            advertisement_id
                    FROM 
                            data_summaries
                    WHERE 
                            status=2 and  
                            ".$term."
                    ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
             //   $serialize_data = unserialize($row['serialize_data']);
                //アプリかWEBか判定
         //       CreateLog::putErrorLog("advertisement_id".$row['advertisement_id']); 
                if(!$type = $this->judgeAdvertisementType($row['advertisement_id'])){
                    throw new Exception("アプリ、WEBの判定失敗");
                }
                if($type == 2 || $type == 4){
                    $app_cnt++;     
                } else{
                    $web_cnt++;
                }              
            }
            $message = "\nアプリ案件Action数 : ".$app_cnt;
            $message .= "\nWEB案件Action数 : ".$web_cnt;

            $this->sales_array['action_app']=$app_cnt;
            $this->sales_array['action_web']=$web_cnt; 
            
            return $message;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * アプリ、WEBの判定
    *
    * @return boolean false
    * @return string 1 アプリ
    * @return string 2 WEB
    */
    private function judgeAdvertisementType($advertisement_id){
        try{
            if(substr($advertisement_id,0,3)=='PAG'){
                return 1;
            }
            $sql = "select 
                            advertisement_type 
                    from 
                            master_advertisements 
                    where 
                            advertisement_id = ".$advertisement_id."  limit 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            
            $row = mysql_fetch_assoc($result);
            return $row['advertisement_type'];
            
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * デイリー又は月刊の粗利、原価、粗利率
    *
    * @return boolean false
    * @return string message
    */
    private function grosMargin($term){
        try{
            $message="\n";
            //インセンティブポイント合計
            $incentive = $this -> cost($term,0);
            if($incentive == ""){
                $incentive = 0;
            }
            //SNSポイント合計
            $sns_point = $this -> cost($term,2);
            if($sns_point == ""){
                $sns_point = 0;
            }

            //すごろくポイント合計
            $game_point = $this -> cost($term,6);
            if($game_point == ""){
                $game_point = 0;
            }
 
            //ログインスタンプ
            $login_stamp = $this->cost($term,5);
            if($login_stamp==""){
                $login_stamp=0;
            }
 
            //売上合計
            $sale = $this -> sales($term);

            $cost = $incentive + $game_point + $sns_point; 

            //粗利
            $gros_margin = $sale - $cost;
            //粗利0の場合
            if($gros_margin < 0){
                $gros_margin_rate = ($sale-$cost)/$cost *100;
            } else{
                $gros_margin_rate =  100*round(1 - $cost / $sale,2);
            }

            $invitation_point = $this->frendInvitationPoint($term,0);
            $review_point =$this->frendInvitationPoint($term,99999);
            $user_invitations= $this->invitations($term);
            $totalcost = $cost+$invitation_point+$review_point+$user_invitations[1]+$login_stamp;

            $data['sale']=$sale;//売上
            $data['gros_margin']=$gros_margin;//粗利
            $data['gros_margin_rate']=$gros_margin_rate;//粗利率
            $data['totalcost']=$totalcost;//原価
            $data['sns_point']=$sns_point;//ガチャ
            $data['game_point']=$game_point;//すごろく
            $data['incentive']=$incentive;//インセンティブ
            $data['login_stamp']=$login_stamp;//ログインスタンプ
            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * デイリー又は月間の原価(SNS、インセンティブ)
    *
    * @return boolean false
    * @return string message
    */
    private function cost($term,$point_type){
        try{
            $sql = "select 
                        sum(app_point) as cost 
                    from 
                        log_bankbooks
                    where
                        ".$term." AND
                        point_type = '".$point_type."';";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            
            return $row['cost'];
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }

     /**
    * デイリー又は月間のギフトコード発行額
    *
    * @return boolean false
    * @return string message
    */
    private function sumPresent($term){
        try{
            $sql = "select
                        present_id, count(*) cnt
                    from
                        data_distributions
                    where
                        ".$term." AND
                        status > 0
                        group by present_id;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }

            $data = array();
            while($row = mysql_fetch_assoc($result)){
                $data[$row['present_id']] = $row['cnt'];
            }
           
            $sql2 = "select * from master_presents;";
            if(!$result2 = mysql_query($sql2,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }

            $data2 = array();
            while($row2 = mysql_fetch_assoc($result2)){
                $data2[$row2['present_id']] = $data[$row2['present_id']] * $row2['cost'];
            }

            return $data2;
        } catch(Exception $e){
            return false;
        }
    }

    /**
    * デイリー又は月間の売上
    *
    * @return boolean false
    * @return string message
    */
    private function sales($term){
        try{
            $payment_sum=0;
            $sql = "select 
                        *
                    from 
                        data_summaries
                    where 
                        ".$term." AND
                        status=2; ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
                if(!$advertisement_data=$this->getAdvertisementData($row['advertisement_id'])){
                    // 案件がDBにない＝PAG案件
                    $serialize_data = unserialize($row['serialize_data']);
                    $advertisement_data['margin'] = $serialize_data['payment'];
                }
                $payment_sum=$payment_sum+$advertisement_data['margin']; 
            }
            return $payment_sum;
        } catch(Exception $e){
            return false;
        }
    }
    /**
    * 案件データ取得
    *
    * @return boolean false
    * @return array
    */
    public function getAdvertisementData($advertisement_id){
        try{
            $sql = "SELECT 
                            *
                    FROM        
                            master_advertisements
                    WHERE 
                            advertisement_id=".$advertisement_id." limit 1;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row=mysql_fetch_assoc($result);
            return $row;
        } catch(Exception $e){
            return false;
        }
    }    


    /**
    * Push通知のトータル送信数と送信結果
    * 
    * @return string message
    */

    public function push_summary(){

        try{
            //トータル送信数
            $sql = "SELECT
                        count(*)
                    FROM
                        data_users;";

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }
            $count = mysql_fetch_array($result);
            echo $count[0];
    
            //実際に届いた数
            
            //require_once("/home/www/ApnsPHP-r100/sample_feedback.php");

        }catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }

    }
    /**
    * 1時間ごとの案件インストール数をログ出し
    *
    * @return boolean false
    */
    public function installAdvertisesPutLog($day){
        try{
            $message="";
            //$day=27;
           // $next_day=$day+1;
        //    $day = $argv[1]; 
            $next_day=$day+1;
            $ymd="Y-m-".$day." ";
            $next_ymd="Y-m-".$next_day." ";
            for($i=0;$i<24;$i++){
                $j=$i+1;
                if($j!=24){
                    $now_time = date($ymd.$i.":00:00");
                    $one_hour_ago =  date($ymd.$j.":00:00");
                    $message.=$this->searchInstall($now_time,$one_hour_ago);
                    $message.="\n";
                }else{
                    $j=0;
                    $now_time =  date($ymd.$i.":00:00");
                    $one_hour_ago = date($next_ymd.$j.":00:00",strtotime("+1 day"));
                    $message.= $this->searchInstall($now_time,$one_hour_ago);
                    $message.="\n";
                }
            }
            echo $message ;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 案件インストール数を取得
    *
    * @return boolean false
    */
    private function searchInstall($now_time,$one_hour_ago){
        try{
            $sql ="SELECT
                        count(*)
                   FROM     
                        data_summaries      
                   WHERE       
                        created >= '".$now_time."' AND
                        status=2 AND
                        created <= '".$one_hour_ago."'";
           if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
           }
           $row = mysql_fetch_assoc($result); 
            return $row["count(*)"];
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 流入別の売上、DL数、課金人数、課金率、ARPPU、1DL/売上
    *
    * @return boolean false
    */
    public function tallyLog2($type){
        try{
            $now_time = date("Y-m-d H:i:s");
            $message="～～～～～～～～～～メール本文～～～～～～～～～～～";
            $host="dev@agentgate.jp, tkitagawa@agentgate.jp";
            //$host="ssaruwatari@agentgate.jp";
           // $host="masadon0621@gmail.com";
            $subject="流入別売上速報（日別/累計6/27～）";
            $from=GIFTGET_MAIL_ADDRESS_FROM;
            $term = " created >= date(current_timestamp - interval 1 day) AND created < date(now()) ";
       //     $term = " created >= '2013-07-25' AND created < '2013-07-26' ";
            $term2 =" created >= '2013-06-27' AND created < date(now()) ";
           // $term2 =" created >= '2013-06-27' AND created < '2013-07-26' ";
            //$term3 =" created >= '2013-08-01' AND created < '2013-09-01' ";
            $term3 = " created >= DATE_FORMAT(now(),'%Y-%m-01') AND created < date(now()) ";
            $message .= "\n".date("【m月d日",strtotime("-1 day"))." 速報】";
          //  $message .= "\n【7月01日速報】";
            $num=0;
            $array = array();//日別
            $array2 = array();//累計
            $array3 = array();//月別

            // 流入別ユーザ集計
            while($num<=60){
                if($num==17){ // パズドラ1回目
                    $array[17]=$this->tallyData2($term." AND created<'2014-01-15' ",$num);
                    $array2[17]=$this->tallyData2($term2." AND created<'2014-01-15' ",$num);
                    $array3[17]=$this->tallyData2($term3." AND created<'2014-01-15' ",$num);
                } else if($num==51){ //パズドラ2回目
                    $array[51]=$this->tallyData2($term." AND created>'2014-01-15' ",17);
                    $array2[51]=$this->tallyData2($term2." AND created>'2014-01-15' ",17);
                    $array3[51]=$this->tallyData2($term3." AND created>'2014-01-15' ",17);
                } else {
                    $array[$num]=$this->tallyData2($term,$num);
                    $array2[$num]=$this->tallyData2($term2,$num);
                    $array3[$num]=$this->tallyData2($term3,$num);
                }
                if(empty($array[$num]['install_cnt'])){
                    $array[$num]['install_cnt']=0;
                } 
                if(empty($array[$num]['unique_install'])){
                    $array[$num]['unique_install']=0;
                }
                if(empty($array2[$num]['install_cnt'])){
                    $array2[$num]['install_cnt']=0;
                }
                if(empty($array2[$num]['unique_install'])){
                    $array2[$num]['unique_install']=0;
                }
                if(empty($array3[$num]['install_cnt'])){
                    $array3[$num]['install_cnt']=0;
                }
                if(empty($array3[$num]['unique_install'])){
                    $array3[$num]['unique_install']=0;
                }
                $num++;
            }

            //全ユーザ売上集計
            if(!$sum_array=$this->sumSales($term)){
                throw new Exception("売上失敗1");
            }
            if(!$sum_array2=$this->sumSales($term2)){
                throw new Exception("売上失敗2");
            }
            if(!$sum_array3=$this->sumSales($term3)){
                throw new Exception("売上失敗3");
            }

            //課金人数計算
            $cnt_portal_array=$this ->getUniqueCnt($term);
            $cnt_portal_array2=$this ->getUniqueCnt($term2);
            $cnt_portal_array3=$this ->getUniqueCnt($term3);
            $num=0;
            while($num <= 60){
                if($num!=3 && $num!=11 && $num !=12){
                    //課金者数はこっちに代入
                    $array[$num]['unique_install']=$cnt_portal_array[$num];
                    $array2[$num]['unique_install']=$cnt_portal_array2[$num];
                    $array3[$num]['unique_install']=$cnt_portal_array3[$num];

                    $array[$num]['conversion_rate']=100*round($cnt_portal_array[$num]/$array[$num]['install_cnt'],2);
                    $array[$num]['arppu']=round($sum_array[$num]/$cnt_portal_array[$num],1);
                    $array[$num]['unique_margin']=round($sum_array[$num]/$array[$num]['install_cnt'],1);
                    $array2[$num]['conversion_rate']=100*round($cnt_portal_array2[$num]/$array2[$num]['install_cnt'],2);
                    $array2[$num]['arppu']=round($sum_array2[$num]/$cnt_portal_array2[$num],1);
                    $array2[$num]['unique_margin']=round($sum_array2[$num]/$array2[$num]['install_cnt'],1);
                    $array3[$num]['conversion_rate']=100*round($cnt_portal_array3[$num]/$array3[$num]['install_cnt'],2);
                    $array3[$num]['arppu']=round($sum_array3[$num]/$cnt_portal_array3[$num],1);
                    $array3[$num]['unique_margin']=round($sum_array3[$num]/$array3[$num]['install_cnt'],1);
                }
                if(empty($array[$num]['conversion_rate'])){
                    $array[$num]['conversion_rate']=0;
                }
                if(empty($array[$num]['arppu'])){
                    $array[$num]['arppu']=0;
                }
                if(empty($array[$num]['unique_margin'])){
                    $array[$num]['unique_margin']=0;
                }
                if(empty($array2[$num]['conversion_rate'])){
                    $array2[$num]['conversion_rate']=0;
                }
                if(empty($array2[$num]['arppu'])){
                    $array2[$num]['arppu']=0;
                }
                if(empty($array2[$num]['unique_margin'])){
                    $array2[$num]['unique_margin']=0;
                }
                if(empty($array3[$num]['conversion_rate'])){
                    $array3[$num]['conversion_rate']=0;
                }
                if(empty($array3[$num]['arppu'])){
                    $array3[$num]['arppu']=0;
                }
                if(empty($array3[$num]['unique_margin'])){
                    $array3[$num]['unique_margin']=0;
                }
                $num++;
            } 
            $message.= "\n※（日別/累計6/27～）";
            $message.= "\n■Twitter";
            $message.= "\nDL数:".$array[4]['install_cnt']."DL/".$array2[4]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[4]['unique_install']."人/".$array2[4]['unique_install']."人";
            $message.= "\n売上:".$sum_array[4]."円/".$sum_array2[4]."円";
            $message.= "\n課金率:".$array2[4]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[4]['arppu']."円/".$array2[4]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[4]['unique_margin']."円";
            $message.= "\n\n■ロックジョイ";
            $message.= "\nDL数:".$array[6]['install_cnt']."DL/".$array2[6]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[6]['unique_install']."人/".$array2[6]['unique_install']."人";
            $message.= "\n売上:".$sum_array[6]."円/".$sum_array2[6]."円";
            $message.= "\n課金率:".$array2[6]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[6]['arppu']."円/".$array2[6]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[6]['unique_margin']."円";
            $message.= "\n\n■SmaAD（アドネットワーク）";
            $message.= "\nDL数:".$array[5]['install_cnt']."DL/".$array2[5]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[5]['unique_install']."人/".$array2[5]['unique_install']."人";
            $message.= "\n売上:".$sum_array[5]."円/".$sum_array2[5]."円";
            $message.= "\n課金率:".$array2[5]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[5]['arppu']."円/".$array2[5]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[5]['unique_margin']."円";
            $message.= "\n\n■SmaAD（リワード）";
            $message.= "\nDL数:".$array[10]['install_cnt']."DL/".$array2[10]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[10]['unique_install']."人/".$array2[10]['unique_install']."人";
            $message.= "\n売上:".$sum_array[10]."円/".$sum_array2[10]."円";
            $message.= "\n課金率:".$array2[10]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[10]['arppu']."円/".$array2[10]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[10]['unique_margin']."円";
            $message.= "\n\n■SmaAD（リワード2回目）";
            $message.= "\nDL数:".$array[13]['install_cnt']."DL/".$array2[13]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[13]['unique_install']."人/".$array2[13]['unique_install']."人";
            $message.= "\n売上:".$sum_array[13]."円/".$sum_array2[13]."円";
            $message.= "\n課金率:".$array2[13]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[13]['arppu']."円/".$array2[13]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[13]['unique_margin']."円";
            $message.= "\n\n■SmaAD（リワード3回目）";
            $message.= "\nDL数:".$array[25]['install_cnt']."DL/".$array2[25]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[25]['unique_install']."人/".$array2[25]['unique_install']."人";
            $message.= "\n売上:".$sum_array[25]."円/".$sum_array2[25]."円";
            $message.= "\n課金率:".$array2[25]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[25]['arppu']."円/".$array2[25]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[25]['unique_margin']."円";
            $message.= "\n\n■Formula(フィラ―)";
            $message.= "\nDL数:".$array[7]['install_cnt']."DL/".$array2[7]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[7]['unique_install']."人/".$array2[7]['unique_install']."人";
            $message.= "\n売上:".$sum_array[7]."円/".$sum_array2[7]."円";
            $message.= "\n課金率:".$array2[7]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[7]['arppu']."円/".$array2[7]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[7]['unique_margin']."円";
            $message.= "\n\n■Formula(通常)";
            $message.= "\nDL数:".$array[8]['install_cnt']."DL/".$array2[8]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[8]['unique_install']."人/".$array2[8]['unique_install']."人";
            $message.= "\n売上:".$sum_array[8]."円/".$sum_array2[8]."円";
            $message.= "\n課金率:".$array2[8]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[8]['arppu']."円/".$array2[8]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[8]['unique_margin']."円";
            $message.= "\n\n■CAモバイル様店舗";
            $message.= "\nDL数:".$array[16]['install_cnt']."DL/".$array2[16]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[16]['unique_install']."人/".$array2[16]['unique_install']."人";
            $message.= "\n売上:".$sum_array[16]."円/".$sum_array2[16]."円";
            $message.= "\n課金率:".$array2[16]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[16]['arppu']."円/".$array2[16]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[16]['unique_margin']."円";
            $message.= "\n\n■パズドラ攻略サイト1回目";
            $message.= "\nDL数:".$array[17]['install_cnt']."DL/".$array2[17]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[17]['unique_install']."人/".$array2[17]['unique_install']."人";
            $message.= "\n売上:".$sum_array[17]."円/".$sum_array2[17]."円";
            $message.= "\n課金率:".$array2[17]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[17]['arppu']."円/".$array2[17]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[17]['unique_margin']."円";
            $message.= "\n\n■パズドラ攻略サイト2回目";
            $message.= "\nDL数:".$array[51]['install_cnt']."DL/".$array2[51]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[51]['unique_install']."人/".$array2[51]['unique_install']."人";
            $message.= "\n売上:".$sum_array[51]."円/".$sum_array2[51]."円";
            $message.= "\n課金率:".$array2[51]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[51]['arppu']."円/".$array2[51]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[51]['unique_margin']."円";
            $message.= "\n\n■自然流入";
            $message.= "\nDL数:".$array[9]['install_cnt']."DL/".$array2[9]['install_cnt']."DL";
            $message.= "\n課金人数:".$array[9]['unique_install']."人/".$array2[9]['unique_install']."人";
            $message.= "\n売上:".$sum_array[9]."円/".$sum_array2[9]."円";
            $message.= "\n課金率:".$array2[9]['conversion_rate']."%";
            $message.= "\nARPPU:".$array[9]['arppu']."円/".$array2[9]['arppu']."円";
            $message.= "\n1DL/売上:".$array2[9]['unique_margin']."円";
            $message.= "\n\n■AppDriver";
            $message.= "\n課金人数:".$array[1]['unique_install']."人/".$array2[1]['unique_install']."人";
            $message.= "\n売上:".$sum_array[1]."円/".$sum_array2[1]."円";
            $message.= "\nARPPU:".$array[1]['arppu']."円/".$array2[1]['arppu']."円";
            $message.= "\n\n■CAReward";
            $message.= "\n課金人数:".$array[2]['unique_install']."人/".$array2[2]['unique_install']."人";
            $message.= "\n売上:".$sum_array[2]."円/".$sum_array2[2]."円";
            $message.= "\nARPPU:".$array[2]['arppu']."円/".$array2[2]['arppu']."円";
            $message.="\n～～～～～～～～～～～集計用(日別)～～～～～～～～～～～～"; 
            $message.="\n".$array[4]['install_cnt'];
            $message.="\n".$array[4]['unique_install'];
            $message.="\n".$sum_array[4];
            $message.="\n";
            $message.="\n".$array[4]['arppu'];
            $message.="\n";
            $message.="\n".$array[6]['install_cnt'];
            $message.="\n".$array[6]['unique_install'];
            $message.="\n".$sum_array[6];
            $message.="\n";
            $message.="\n".$array[6]['arppu'];
            $message.="\n";
            $message.="\n".$array[5]['install_cnt'];
            $message.="\n".$array[5]['unique_install'];
            $message.="\n".$sum_array[5];
            $message.="\n";
            $message.="\n".$array[5]['arppu'];
            $message.="\n";
            $message.="\n".$array[10]['install_cnt'];
            $message.="\n".$array[10]['unique_install'];
            $message.="\n".$sum_array[10];
            $message.="\n";
            $message.="\n".$array[10]['arppu'];
            $message.="\n";
            $message.="\n".$array[7]['install_cnt'];
            $message.="\n".$array[7]['unique_install'];
            $message.="\n".$sum_array[7];
            $message.="\n";
            $message.="\n".$array[7]['arppu'];
            $message.="\n";
            $message.="\n".$array[8]['install_cnt'];
            $message.="\n".$array[8]['unique_install'];
            $message.="\n".$sum_array[8];
            $message.="\n";
            $message.="\n".$array[8]['arppu'];
            $message.="\n";
            $message.="\n".$array[9]['install_cnt'];
            $message.="\n".$array[9]['unique_install'];
            $message.="\n".$sum_array[9];
            $message.="\n";
            $message.="\n".$array[9]['arppu'];
            $message.="\n";
            $message.="\n".$array[1]['unique_install'];
            $message.="\n".$sum_array[1];
            $message.="\n".$array[1]['arppu'];
            $message.="\n".$array[2]['unique_install'];
            $message.="\n".$sum_array[2];
            $message.="\n".$array[2]['arppu'];
            $message.="\n".$array[13]['install_cnt'];
            $message.="\n".$array[13]['unique_install'];
            $message.="\n".$sum_array[13];
            $message.="\n";
            $message.="\n".$array[13]['arppu'];
            $message.="\n～～～～～～～～～～～集計用(月別)～～～～～～～～～～～～";    
            $message.="\n".$array3[4]['install_cnt'];
            $message.="\n".$array3[4]['unique_install'];
            $message.="\n".$sum_array3[4];
            $message.="\n";
            $message.="\n".$array3[4]['arppu'];
            $message.="\n";
            $message.="\n".$array3[6]['install_cnt'];
            $message.="\n".$array3[6]['unique_install'];
            $message.="\n".$sum_array3[6];
            $message.="\n";
            $message.="\n".$array3[6]['arppu'];
            $message.="\n";
            $message.="\n".$array3[5]['install_cnt'];
            $message.="\n".$array3[5]['unique_install'];
            $message.="\n".$sum_array3[5];
            $message.="\n";
            $message.="\n".$array3[5]['arppu'];
            $message.="\n";
            $message.="\n".$array3[10]['install_cnt'];
            $message.="\n".$array3[10]['unique_install'];
            $message.="\n".$sum_array3[10];
            $message.="\n";
            $message.="\n".$array3[10]['arppu'];
            $message.="\n";
            $message.="\n".$array3[7]['install_cnt'];
            $message.="\n".$array3[7]['unique_install'];
            $message.="\n".$sum_array3[7];
            $message.="\n";
            $message.="\n".$array3[7]['arppu'];
            $message.="\n";
            $message.="\n".$array3[8]['install_cnt'];
            $message.="\n".$array3[8]['unique_install'];
            $message.="\n".$sum_array3[8];
            $message.="\n";
            $message.="\n".$array3[8]['arppu'];
            $message.="\n";
            $message.="\n".$array3[9]['install_cnt'];
            $message.="\n".$array3[9]['unique_install'];
            $message.="\n".$sum_array3[9];
            $message.="\n";
            $message.="\n".$array3[9]['arppu']; 
            $message.="\n";
            $message.="\n".$array3[1]['unique_install'];
            $message.="\n".$sum_array3[1];
            $message.="\n".$array3[1]['arppu'];
            $message.="\n".$array3[2]['unique_install'];
            $message.="\n".$sum_array3[2];
            $message.="\n".$array3[2]['arppu'];
            $message.="\n".$array3[13]['install_cnt'];
            $message.="\n".$array3[13]['unique_install'];
            $message.="\n".$sum_array3[13];
            $message.="\n";
            $message.="\n".$array3[13]['arppu'];
            $message.="\n～～～～～～～～～～～集計用(累計)～～～～～～～～～～～～";
            $message.="\n".$array2[4]['install_cnt'];
            $message.="\n".$array2[4]['unique_install'];
            $message.="\n".$sum_array2[4];
            $message.="\n".$array2[4]['conversion_rate'];
            $message.="\n".$array2[4]['arppu'];
            $message.="\n".$array2[4]['unique_margin'];
            $message.="\n".$array2[6]['install_cnt'];
            $message.="\n".$array2[6]['unique_install'];
            $message.="\n".$sum_array2[6];
            $message.="\n".$array2[6]['conversion_rate'];
            $message.="\n".$array2[6]['arppu'];
            $message.="\n".$array2[6]['unique_margin'];
            $message.="\n".$array2[5]['install_cnt'];
            $message.="\n".$array2[5]['unique_install'];
            $message.="\n".$sum_array2[5];
            $message.="\n".$array2[5]['conversion_rate'];
            $message.="\n".$array2[5]['arppu'];
            $message.="\n".$array2[5]['unique_margin'];
            $message.="\n".$array2[10]['install_cnt'];
            $message.="\n".$array2[10]['unique_install'];
            $message.="\n".$sum_array2[10];
            $message.="\n".$array2[10]['conversion_rate'];
            $message.="\n".$array2[10]['arppu'];
            $message.="\n".$array2[10]['unique_margin'];
            $message.="\n".$array2[7]['install_cnt'];
            $message.="\n".$array2[7]['unique_install'];
            $message.="\n".$sum_array2[7];
            $message.="\n".$array2[7]['conversion_rate'];
            $message.="\n".$array2[7]['arppu'];
            $message.="\n".$array2[7]['unique_margin'];
            $message.="\n".$array2[8]['install_cnt'];
            $message.="\n".$array2[8]['unique_install'];
            $message.="\n".$sum_array2[8];
            $message.="\n".$array2[8]['conversion_rate'];
            $message.="\n".$array2[8]['arppu'];
            $message.="\n".$array2[8]['unique_margin'];
            $message.="\n".$array2[9]['install_cnt'];
            $message.="\n".$array2[9]['unique_install'];
            $message.="\n".$sum_array2[9];
            $message.="\n".$array2[9]['conversion_rate'];
            $message.="\n".$array2[9]['arppu'];
            $message.="\n".$array2[9]['unique_margin'];
            $message.="\n".$array2[1]['unique_install'];
            $message.="\n".$sum_array2[1];
            $message.="\n".$array2[1]['arppu'];
            $message.="\n".$array2[2]['unique_install'];
            $message.="\n".$sum_array2[2];
            $message.="\n".$array2[2]['arppu'];
            $message.="\n".$array2[13]['install_cnt'];
            $message.="\n".$array2[13]['unique_install'];
            $message.="\n".$sum_array2[13];
            $message.="\n".$array2[13]['conversion_rate'];
            $message.="\n".$array2[13]['arppu'];
            $message.="\n".$array2[13]['unique_margin'];
            $this->sendMail($host,$subject,$message,$from);
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            die();
        }
    }
    /**
    * 日別、累計の流入別集計データをとる
    *
    * @return boolean false
    * @return array install_cnt:インストール数 unique_install：課金人数 
    */
    private function tallyData2($term,$portal){
        try{
            /*                                                                          *
            * インストール数の取得                                                      *
            *                                                                           * 
            * 0:再インストールユーザー 1:AppDriver 2:CAReward 4:Twitter                 *
            * 5:SmaAD（ADNW）6:ロックジョイ 7: Formula（フィラー）8: Formula（通常掲載）*
            * 9:自然流入 10: SmaAD（リワード）                                          *
            *                                                                          */                                
             $array['install_cnt']=$this->totalInstallCnt($term,$portal,0);
            /*                                                     *
            * 応募したユーザーのユーザーIDを取得                   *
            * ユーザーIDからどこ経由で流入したかをそれぞれカウント *
            *                                                     */
            $user_array=array();
            $user_array = $this->totalInstallCnt($term,$portal,1);
            $array['unique_install']=0;
            $array['sale']=0;
            return $array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * インストール数(0:一度インストールして再インストール 1:AppDriver 2:CAR 
    *               3:Tapjoy 4:Twitter 5:ロックジョイ 6:SmaAD(アドネットワーク) 
    *               7:Formula(フィラ―) 8:Formula(通常配信)9:自然流入 10:SmaAD(Reward)))
    *
    * @return boolean false
    */
    private function totalInstallCnt($term,$portal,$flg){
        try{
            if($flg==0){
                $group = "group by udid";
            } else if($flg==1){
                $group="";
            }
             $sql ="SELECT
                        *
                    FROM
                        data_users
                    WHERE
                        ".$term." AND
                        portal=".$portal."
                        ".$group." ; ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $num=0;
            if($flg==1){
                while($row = mysql_fetch_assoc($result)){
                    $user_id[$num]=$row['user_id'];
                    
                    $num++;
                }
                return $user_id;
            } else{
                $result_cnt = mysql_num_rows($result);
                return $result_cnt;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 成果の情報の広告IDを取得（日別・累計）
    *
    * @return array $advertisement_array 広告ID
    */
    private function getPortalUniqueSummaries($term,$user_id){
        try{
            $sql="SELECT
                        *
                  FROM
                        data_summaries
                  WHERE
                        ".$term." AND
                        user_id = ".$user_id."
                        ;";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $num=0;
            $result_cnt = mysql_num_rows($result);
            if($result_cnt==0){
                return false;
            } else{ 
                while($row = mysql_fetch_assoc($result)){
                     $advertisement_array[$num]=$row['advertisement_id'];
                }
                return $advertisement_array;
            }
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 流入別売上計算
    *
    * @return array(int) $sum_array 流入別の売上合計
    */
    private function sumSales($term){
        try{
            $sql="SELECT
                        *
                  FROM       
                        data_summaries
                  WHERE      
                        ".$term." AND
                        status=2 ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }  
            for($i=0;$i<60;$i++){
                $sum_array[$i]=0;
            }
            while($row = mysql_fetch_assoc($result)){
               if(!$advertisement_data=$this->getAdvertisementData($row['advertisement_id'])){
                   throw new Exception("案件margin取得失敗");
               }
               $portal=$this->getPortal($row['user_id']);
               $sum_array[$portal]+=$advertisement_data['margin'];
            }
            return $sum_array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * ユーザーテーブルからportal取得
    *
    * @return int portal 
    */
    private function getPortal($user_id){
        try{
             $sql="SELECT
                        *
                   FROM
                        data_users
                   WHERE        
                        user_id = ".$user_id.";";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            $row = mysql_fetch_assoc($result);

            if($row['portal']==17 && $row['created']>'2014-01-15'){
                return 51;// パズドラ2回目ユーザ
            }
            return $row['portal'];
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
    /**
    * 流入別の課金人数取得
    *
    * @return int $cnt_portal_array 流入別の課金人数
    */
    private function getUniqueCnt($term){
        try{
            $sql="SELECT
                        *
                 FROM
                        data_summaries
                 WHERE
                        ".$term." AND
                        status=2 
                        GROUP BY user_id ";
            if(!$result = mysql_query($sql,$this->getDatabaseLink())) {
                throw new Exception(mysql_error());
            }
            for($i=0;$i<60;$i++){
                $cnt_portal_array[$i]=0;
            }
            while($row = mysql_fetch_assoc($result)){
               $portal=$this->getPortal($row['user_id']);
               $cnt_portal_array[$portal]++;
            }
            return $cnt_portal_array;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return false;
        }
    }
}
