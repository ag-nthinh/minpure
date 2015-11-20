<?php
    require_once(dirname(__FILE__).'/../conf/ini.php');
    $obj=new Tally();
   // $term = " created >= '2013-07-26' AND created < '2013-07-27' ";
    $term = " created < date(current_timestamp + interval 1 day) AND created >= date(now()) ";
    $message=$obj->contributionSNS($term);
    CreateLog::putTweetCntLog($message);
    
    
