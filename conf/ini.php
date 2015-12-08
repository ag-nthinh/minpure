<?php
        require_once('ini_strings.php');
        require_once('ini_advertisements.php');
        require_once('ini_mail.php');
        date_default_timezone_set('Asia/Tokyo');
        //DB設定
        define('DB_HOST','192.168.20.200');
        define('DB_USER','ggg');
        define('DB_PASS','X4trEn7C');
        define('DB_NAME','fxe_v1_production');
        //ログディレクトリ設定
        define('LOG_DIR','/var/www/html/ggg_v1/public/logs/');
        define('LOG_DIRECTORY_PATH','/var/www/html/ggg_v1/public/logs/');
        //特に使ってないかも
        define('MAGIC_CODE','nise4649');
        //メンテナンス中でもアクセスできるログインID
        define('STRONG_LOGINID','5ATPrzdUPBQ7R0_IwucnosevdIk2cxbp');//HTCJ
        //define('STRONG_LOGINID','oWNz6h61tOeo7rkEWdMoZWkYTcyGpb2a');//YA7SE
        //define('STRONG_LOGINID','AloU0n5Vb5Z1TwiJefxsHASPFlPEPDOh');//正野端末
        //画像ディレクトリ設定
        define('IMAGE_PASS','/var/www/html/ggg_v1/public/web/image/');
        //URL設定
        define('PROTOCOL','http://');
        define('SERVER_DOMAIN','minpure.com');
        define('ROOT_PATH','/ggg');
        define('ERROR_PAGE_PATH','/error/error.php');
        //ZucksのPUBLISHER_CODE
        define("ZUCKS_PUBLISHER_CODE","giftget");
        //Zucksのシークレットコード
        define("ZUCKS_SECURITY_CODE","c9301020bc9cd342c53df1bd62fcc125");
        //認証用キー
        define('SECRET_KEY','ZwJIo05WKuvJX582iHXlfn6oL6INfyeG');
        //デイリーガチャ確率
        define('PROBABILITY_GACHA','2');
        //tweet後のガチャ確率
        define('PROBABILITY_GACHA2','5');
        //tweet後のガチャポイント
        define('GACHA_POINT2','2');
        //ガチャポイント
        define('GACHA_POINT','2');
        //memcashed設定
        define('MEM_HOST','192.168.20.150');
        define('MEM_PORT','11212');
        define('MEM_TIME','259200');
        //バージョン
        define('VERSION','31');
        //緊急事態フラグ 0:メンテ中 1:通常稼働
        define('EMERGENCY','1');
        //ポイントレート
        define('DEFAULT_RATE','0.6');
        define('DEFAULT_RATE_OLD','0.5');
        //レビューポイント
        define('REVIEW_POINT','10');
        //レビューURL
        define('REVIEW_URL','market://details?id=com.giftget');
        //ギフトGETのグーグルプレイURL
        define('GIFTGET_URL','market://details?id=com.giftget');
        //チュートリアルフラグ 0:OFF 1:ON
        define('TUTORIAL_FLG','1');
        //スプラッシュフラグ 0:OFF 1:ON
        define('SPLASH_FLG','1');
        //CAReword APIキー
        define('API_KEY','311510279543eb25');
        //push通知のAPIキー
        define('API_KEY1','AIzaSyBl6xPsL-cxEP3H2dTYcnuwxudN4zPkojc');
        //案件新着期間 現在5日間新着ラベルがつく
        define('NEWLY_ARRIVED_TIME','432000');
        //招待ポイント
        define('INVITATION_POINT','30');
        //おすすめラベル この値以上のpriorityの案件に張られる
        define('PRIORITY_LABEL','20');
        //一度の表示させる件数
        define('LINE_NUMBER','10'); 
        //デバッグログフラグ 1:ログ出力 0:ログ未出力
        define('DEBUG_LOG_FLG','1');
         //管理画面のログインID
        define('ADMIN_LOGIN_ID','admin');
        //管理画面のログインPASS
        define('ADMIN_LOGIN_PASS','f@5s*CV%N\'\xCW`z8)C/of73`<.Z|y[<');
        //define('ADMIN_LOGIN_PASS',"f@5s*CV%N\'\xCW`z8)C/of73`<.Z|y[<");
        //管理画面のsession
        define('ADMIN_SESSION','123');
        //Pex発券APIURL(テスト用)
     //   define('PEX_API_URL','https://api.pex.jp.dev.pex.jp/gift/1.0/pull');
        //Pex発券APIURL(本番用)
        //define('PEX_API_URL','https://api.pex.jp/gift/1.0/pull'); //旧
        define('PEX_API_URL','https://api.giftondemand.jp/v1/gift'); //新
        //Pex照会APIURL
        //define('PEX_CHECK_API_URL','https://api.pex.jp/gift/1.0/inquiry'); //旧
        define('PEX_CHECK_API_URL','https://api.giftondemand.jp/v1/gift/transaction'); //新
        //Pexパートナーコード
        define('PEX_PARTNER_CODE','giftget');
        //Pexシークレットコード
        define('PEX_SECRET_KEY','P3vScGXqGgtVcf8Ay26y');
        //AmazonURL
        define('AMAZON_URL','https://www.amazon.co.jp/gp/css/gc/payment/view-gc-balance?code=');
        //PeXAPIフラグ 1:ON 0:OFF
        define('PEX_API_FRG','1');
        //itunes在庫切れフラグ　1:在庫あり 0:在庫なし
        define('ITUNES_IMAGE_FRG','1');
        //itunes画像URL
        define('ITUNES_IMAGE','http://minpure.com/ggg/images/banner_itunes_soldout.jpg');
        //pex在庫切れフラグ　1:在庫あり 0:在庫なし
        define('PEX_IMAGE_FRG','1');
        //pex画像URL
        define('PEX_IMAGE','http://minpure.com/ggg/images/banner_pex_soldout.jpg');
        //Vプリカ在庫切れフラグ　1:在庫あり 0:在庫なし
        define('VPRECA_IMAGE_FRG','1');
        //Vプリカ画像URL
        define('VPRECA_IMAGE','http://minpure.com/ggg/images/banner_pex_soldout.jpg');
        //一日のギフトコード交換上限回数
        define('CHANGE_CNT','2');
        // 一日の申請総額上限
        define('DAILY_GIFT_AMOUNT','100000');
        // 一月の申請総額上限
        define('MONTHLY_GIFT_AMOUNT','2500000');

        /* Classのオートロード */
        spl_autoload_register(function($class) {
//            require_once(dirname(__FILE__).'/../class/' . $class . '.php');
        });
/*
        spl_autoload_register(function($class) {
            require_once('/usr/share/pear/HTTP/' . $class . '.php');
        });
*/
        //サニタイズショートカット
        function h($string){
                return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
        function s($string){
                return mysql_real_escape_string($string);
        }
        /*
        *フォールバック
        *指定のページへリダイレクトする
        *error.php=なんかエラーページ
        */
        function fallback($location='error.php'){
                header('Location: '.$location);
                exit();
        }
