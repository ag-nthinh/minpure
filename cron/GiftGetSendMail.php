<?php
          //  echo "../logs/notice/".date("Y-m-d").".log";
             $text = file_get_contents(dirname(__FILE__)."/../logs/notice/".date("Y-m-d",(strtotime("-1 day"))).".log",true);  
             if($text ==""){
                 $text = "本日は新規案件がございません。";
             }
             mb_language("Japanese");
             mb_internal_encoding("UTF-8");
             mb_send_mail("dev@agentgate.jp,hoonuma@agentgate.jp" ,"ギフトGET【新規案件の追加】",$text, "From:giftget@agentgate.jp");
