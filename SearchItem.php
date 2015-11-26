<?php
/**
* SearchItemクラス
* Item検索
* @package SearchItem
* @author 平野 雅也
* @since PHP 5.4.8
* @version $Id: SearchItem,v 1.0 2013/8/16 Exp $
*/
class SearchItem extends CommonBase {
    /*
    * コンストラクタ
    */
    public function __construct(){
        try {
            parent::__construct();
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            
        }
    }
    /**
    * 案件リストの表示
    *
    * @accsess public
    * @param int $type 広告の種類
    * @param string $advertisement_id 広告キー
    * @param int $campany_type リワード種類
    *
    * @return boolean
    */
    public function getItemList($type,$advertisement_id){
        try {
            //不正チェック
            if(!$login_id = CommonValidate::validateLoginId($_POST['login_id'])) {
                throw new Exception("IDチェック失敗");
            }
            //日付取得
            $now_date = date('Y-m-d h:i:s');
            $advertisement_type ="";
            
            if($advertisement_id != ""){//案件詳細画面
                $sql = "
                        SELECT 
                                serialize_data,
                                price,
                                point,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction 
                        FROM 
                                master_advertisements 
                        WHERE 
                                advertisement_id ='".$advertisement_id."' AND
                                deleted = 0 LIMIT 1;";
            } else{//案件一覧  

                //ログイン数取得
                $login_count=$this->setLoginCount($login_id);

                $secret_item=""; 

            $item_lists = array();

            $splash_obj=new Splash();
            //チュートリアル表示フラグ 1:ON 0:OFF
            if(TUTORIAL_FLG){
                if(!$tutorial_flg=$splash_obj->searchTutorialFlgs($login_id)){
                    throw new Exception("チュートリアルフラグの取得失敗");
                }
                if($tutorial_flg=="not_tutorial_flg"){
                    if(!$splash_obj->updateTutorialFlgs($login_id)){
                        throw new Exception("チュートリアルのアップデート失敗");
                    }
                }
            }
            // スプラッシュ表示フラグ 1:ON 0:OFF
            if(SPLASH_FLG){
                if(!$splash_flg=$splash_obj->searchSplashFlgs($login_id)){
                    throw new Exception("スプラッシュフラグの取得失敗");
                }
                if($splash_flg=="not_flg"){
                    $item_lists['splash'] = SPLASH_FLG;
                    if(!$splash_obj->updateSplashFlgs($login_id)){
                        throw new Exception("スプラッシュフラグのアップデート失敗");
                    }
                } else{
                    $item_lists['splash'] = 0;
                }
            } else{
                $item_lists['splash'] = 0;
            }
            //チュートリアルフラグ
            $item_lists['tutorial'] = TUTORIAL_FLG;
            //ログインカウント
            $item_lists['login_count'] = $login_count;

            //プルダウンの文言
            $item_lists['type_0'] =PULLDOWN_TEXT_0;
            $item_lists['type_1'] =PULLDOWN_TEXT_1;
            $item_lists['type_2'] =PULLDOWN_TEXT_2;
            $item_lists['type_3'] =PULLDOWN_TEXT_3;
            $item_lists['type_4'] =PULLDOWN_TEXT_4;
            $item_lists['type_5'] =PULLDOWN_TEXT_5;
            $item_lists['type_6'] =PULLDOWN_TEXT_6;
 
                //表示させる案件の件数を計算
                if(!isset($_POST['add'])){
                    $add=0;
                } else{
                    $add=$_POST['add'];
                }
                $begin =LINE_NUMBER*$add;
                $end = LINE_NUMBER; 
                $limit = "limit ".$begin.",".$end;
                //指定したユーザーのみ取得できる案件
                if($login_id!=STRONG_LOGINID){
                //$secret_item2="AND advertisement_id!=".UCHINAVI_ADVERTISEMENT_ID." ";
                    $secret_item2="";
                //$secret_item2="AND advertisement_id!=7477 ";
                } else{
                    $secret_item2="";
                }

                $duplicate_id = $this->getDuplicateId($login_id);
                // 成果済み案件
                $off_advertisements_array = $this->getConversionItems($duplicate_id);
                $off_advertisements = "";
                //CreateLog::putDebugLog(print_r($off_advertisements_array, true));

                if($off_advertisements_array){
                    foreach($off_advertisements_array as $value){
                        $off_advertisements .= "AND NOT (campany_type=".$value['campany_type']." AND advertisement_key='".$value['advertisement_key']."' ) ";
                    }
                }
                //CreateLog::putDebugLog($off_advertisements);
                                
                //カテゴリーの選定  
                $advertisement_type=$this->selectCategory($type);
                $sql = "
                        SELECT
                                serialize_data,
                                point,
                                price,
                                advertisement_id,
                                advertisement_key,
                                advertisement_type,
                                outcome_id,
                                name,
                                img_url,
                                campany_type,
                                landing_url,
                                package_name,
                                priority,
                                market,
                                UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."')-UNIX_TIMESTAMP(newly_arrived) as  time_subtraction 
                        FROM 
                                master_advertisements 
                        WHERE
                                status = 1 AND 
                                deleted = 0 AND 
                                start_time <= '".date("Y-m-d H:i:s")."' AND 
                                end_time >= '".date("Y-m-d H:i:s")."'
                                ".$off_advertisements."
                                ".$advertisement_type."
                                ".$secret_item."
                                ".$secret_item2."
                        ORDER BY priority DESC, newly_arrived DESC ".$limit.";";
            }
            //CreateLog::putDebugLog($sql);

            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                throw new Exception(mysql_error());
            }

            $result_count = mysql_num_rows($result);
            if($result_count == 0){
            //JSON掃出し
            $this->displayJsonArray($item_lists);
                exit("");//案件が0の場合空で返す
            }

            $data = array();
            $num = 0;
            $obj = new AddSummaries();
            while($row = mysql_fetch_assoc($result)){
                //成果済みの案件か検索
                if(!$outcome_data=$this -> searchOutcomeData($login_id,$row['advertisement_id'],$row['outcome_id'],$row['campany_type'])){
                    $item_lists[$num]['outcome_data'] = 0;//成果済みでない
                } else if($outcome_data==1) {
                    $item_lists[$num]['outcome_data'] = 1;//成果済
                } else if($outcome_data==2 && $row['campany_type'] == 8 && $row['campany_type'] == 9){
                    $item_lists[$num]['outcome_data'] = 2;//承認待ち
                } else if($outcome_data==3) {
                    $item_lists[$num]['outcome_data'] = 3;//未起動
                } else if($outcome_data==9) {
                    $item_lists[$num]['outcome_data'] = 9;//応募不可
                } else {
                    $item_lists[$num]['outcome_data'] = 0;
                }
                //案件情報セット
                $data = unserialize($row['serialize_data']);
                $item_lists[$num]['market'] = $data['market'];
                $item_lists[$num]['url'] = $row['package_name'];
                $item_lists[$num]['advertisement_id'] = $row['advertisement_id'];
                $item_lists[$num]['advertisement_key'] = $row['advertisement_key'];
                $item_lists[$num]['advertisement_type'] = $row['advertisement_type'];
                $item_lists[$num]['name'] = stripslashes($row['name']);
                $item_lists[$num]['point'] = $obj -> castPoint($row);
                $item_lists[$num]['img_url'] = $row['img_url'];
                $item_lists[$num]['campany_type'] = $row['campany_type'];

                //ランディングURLと案件詳細画面の種類を選定
                $tmp=$this->setLandingUrlAndItemDetailType($row,$login_id);
                $item_lists[$num]['landing_url'] =$tmp['landing_url'];

                //案件に付けるラベルの種類を決める
                if($row['priority'] > PRIORITY_LABEL){
                    $item_lists[$num]['label'] = 1;//おすすめ
                } else if($row['time_subtraction'] < NEWLY_ARRIVED_TIME){
                    $item_lists[$num]['label'] = 2;//新着
                } else {
                    $item_lists[$num]['label'] = 0;//なにもなし
                }
                //スマパ案件か判定
                if($row['market'] == 4){
                    $item_lists[$num]['smartpass'] = 1;//スマパス案件
                } else{
                    $item_lists[$num]['smartpass'] = 0;//スマパス案件ではない
                }
                //案件のラベルの情報をセット
                switch($row['market']){
                    case 3://DOCOMO
                        $item_lists[$num]['status_text'] = DOCOMO_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = RED_COLOR_CORD;
                        break;
                    case 4://スマパス
                        $item_lists[$num]['status_text'] = AU_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = ORANGE_COLOR_CORD;
                        break;
                    case 6://カード発行案件
                        $item_lists[$num]['status_text'] = CARD_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = CARD_COLOR_CORD;
                        break;
                    default:
                        $item_lists[$num]['status_text'] = "";
                        $item_lists[$num]['status_color'] = "";
                }


                
                if($advertisement_id != ""){//案件詳細画面
                    $item_lists[$num]['price'] = $row['price'];
                    $item_lists[$num]['remark'] = stripslashes(strip_tags($data['remark']));
                    $item_lists[$num]['detail'] = stripslashes(strip_tags($data['detail']));
                    //案件詳細のボタンの文言をセット
                    $item_lists[$num]['button_message'] = 0;//今はなにもなし
                
                } else {//案件一覧画面

                    $item_lists[$num]['advertisement_name'] = stripslashes($data['name']);
                    $item_lists[$num]['item_detail'] = $tmp['item_detail'];


                    //案件のラベルの情報をセット
                    switch($row['market']){
                        case 3://DOCOMO
                            $item_lists[$num]['status_text'] = DOCOMO_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = RED_COLOR_CORD;
                            break;
                        case 4://スマパス
                            $item_lists[$num]['status_text'] = AU_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = ORANGE_COLOR_CORD;
                            break;
                        case 6://カード発行案件
                            $item_lists[$num]['status_text'] = CARD_LABEL_TEXT;
                            $item_lists[$num]['status_color'] = CARD_COLOR_CORD;
                            break;
                        default:
                            $item_lists[$num]['status_text'] = "";
                            $item_lists[$num]['status_color'] = "";
                    }

                    if(!$item_lists[$num]['point']){
                        $item_lists[$num]['status_text'] = NONINCENTIVE_LABEL_TEXT;
                        $item_lists[$num]['status_color'] = GRAY_COLOR_CORD;
                    }


                }
                $num++;
            }


            //JSON掃出し
            $this->displayJsonArray($item_lists);
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            $data['error'] = "false";
            $this->displayJsonArray($data);
        }
    }
    /**
    * ログインカウントのセット
    *
    * @accsess private
    *
    * @return int login_count
    */    
    private function setLoginCount($login_id){
        try{
            //ログインスタンプのインスタンス生成
            $login_obj=new LoginStamp();
            //ログインスタンプ
            if(!$login_count=$login_obj->actionLoginStamp($login_id)){
                throw new Exception("ログインスタンプ失敗");
            }
            if($login_count=="end"){
                $login_count=0;
            }
            return $login_count;
        } catch(Exception $e) {
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return 0;//とりあえず0で返せば止まらない
        }
    }
    /**
    * 過去に別IDとして成果済と思しき案件を取得
    *
    * @accsess private
    * @param type
    *
    * @return array
    */
    private function getConversionItems($duplicate_id){
        try{
            //$data = array();
            // $duplicate_id は a,b,c,d, ... という形式で読み込まれる
            if(!$duplicate_id){
                //重複なし
                return "";
            }
            $sql = "SELECT DISTINCT
                        campany_type,
                        advertisement_key
                    FROM
                        data_summaries
                    WHERE
                        user_id in (".$duplicate_id.") AND
                        status in (2,9);";
        
            if(!$result = mysql_query($sql,$this->getDatabaseLink())){
                    throw new Exception(mysql_error());
            }
            while($row = mysql_fetch_assoc($result)){
                $data[] = $row; 
            }
 
            return $data;
        } catch(Exception $e){
            CreateLog::putErrorLog(get_class()." ".$e->getMessage());
            return "";
        }
    }
 
    /**
    * 案件のカテゴリ選定
    *
    * @accsess private
    * @param type
    *
    * @return string
    */
    private function selectCategory($type){
        /*
        *  
        *  type=1 : 全案件
        *  type=2 : au スマパス
        *  type=3 : スゴ得
        *  type=4 : 会員登録
        *  type=5 : 高単価
        *  type=6 : カード発行案件
        */
        switch($type){
            case 1:
                $advertisement_type = "AND advertisement_type in (2,4) AND price = 0 AND market = 1";
                break;
            case 2:
                $advertisement_type = "AND market = 4 ";
                break;
            case 3:
                $advertisement_type = "AND market = 3";
                break; 
            case 4:
                $advertisement_type = "AND advertisement_type in (1,3)";
                break;
            case 5:
                $advertisement_type = "AND point > 200";
                break;
            case 6:
                $advertisement_type = "AND market = 6 ";
                break;
            default:
                $advertisement_type = "";
        }
        return $advertisement_type;     
    }
    /**
    * ランディングURL生成
    *
    * @accsess private
    * @param array row  login_id
    *
    * @return landing_url
    */    
    private function setLandingUrlAndItemDetailType($row,$login_id){
        $code = Utils::str_encrypt($login_id);
        switch($row['campany_type']){
                    case 1://自社案件
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url'];
                            if($row['advertisement_type']==1 || $row['advertisement_type']==3){ // WEB
                                if(strstr($row['landing_url'], "?")){
                                    $item_lists['landing_url'] .= "&";
                                } else {
                                    $item_lists['landing_url'] .= "?";
                                }
                                $item_lists['landing_url'] .= "sid=".$row['advertisement_key']."&pid=".$login_id;
                                $item_lists['item_detail'] =1;//WEB
                            }
                            break;
                    case 2://AppDriver
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url'].$code;
                            break;
                    case 3://CAReward
                            $crypt = sha1($code.API_KEY);
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url'].$code."&crypt=".$crypt;        
                            break;
                    case 4://Zucks
                            $item_lists['item_detail'] =0;//ネイティブ
                            $verify =hash("sha256",$login_id.ZUCKS_SECURITY_CODE);
                            $item_lists['landing_url'] = $row['landing_url']."&uid=".$login_id."&did=".$_POST['zucks_uuid']."&v=".$verify;   
                            break;
                    case 5://GMOTECH
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url']."&u=".$code;
                            break;
                    case 6://InforNear
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url'].$login_id;
                            break;
                    case 7://GREE
                            $item_lists['item_detail'] =1;//WEB
                            $identifer="&identifier=".$code;
                            $hash=hash("sha256",$row['advertisement_key'].";".$code.";745;38547e343f63729fa0e9bc3890973a48");
                            $campaign_id = "&campaign_id=".$row['advertisement_key'];
                            $digest="&digest=".$hash;
                            $item_lists['landing_url'] = $row['landing_url'].$campaign_id.$identifer.$digest;
                            break;
                    case 8://問い合わせ案件
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] =  $row['landing_url']."?login_id=".$login_id."&advertisement_id=".$row['advertisement_id']."&advertisement_key=".$row['advertisement_key'];
                            break;
                    case 10://Metaps
                            $item_lists['item_detail'] =2;//2はmetaps専用
                            $item_lists['landing_url'] = "";
                            break;
                    case 11://HangOut
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] = $row['landing_url']."&pt=".$code;
                            break;
                    case 12://いいね案件
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url']."?login_id=".$login_id."&advertisement_id=".$row['advertisement_id']."&advertisement_key=".$row['advertisement_key'];
                            break;
                     case 14://CAモバイル ノンインセンティブ広告
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url']."&suid=".$code;
                            break;
                     case 16://affitown
                            $item_lists['item_detail'] =1;//WEB
                            $item_lists['landing_url'] = $row['landing_url'].$login_id;
                            break;
                    default://一応用意
                            $item_lists['item_detail'] =0;//ネイティブ
                            $item_lists['landing_url'] ="";
                            break;
        }    
        return $item_lists;
    }
}//End of class
