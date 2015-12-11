<?php
  require_once(dirname(__FILE__).'/../class/dbconnect.php');
  
  try{
    $sql_all = "SELECT * FROM company_details";
    if(!$result = mysql_query($sql_all, $link)){
      throw new Exception($sql_all);
    }
    //$data = mysql_fetch_assoc($result);
  } catch(Exception $e){
    //CreateLog::putErrorLog(get_class()." ".$e->getMessage());
  }  
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title><?php echo SITE_NAME ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link id="bs-css" href="css/bootstrap-weed.css" rel="stylesheet">
	<link rel="stylesheet" href="css/table.css" type="text/css">
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="content-script-type" content="text/javascript" />
  <meta http-equiv="content-style-type" content="text/css" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta name="language" content="ja" />
  <meta name="robots" content="index,follow" />
  <meta name="description" content="" />
  <title>FXで勝つ為に！最新の人気FXを徹底比較</title>
  <link href="css/common.css?x=140605" media="all" rel="stylesheet" type="text/css" />
  <link href="http://fx-manual.net/" rel="canonical" />
  <script type="text/javascript" src="js/jquery.min.1.5.2.js">
  </script>
  <script type="text/javascript" src="js/tools.js">
  </script>
  <script type="text/javascript" src="js/swfobject.js">
  </script>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
  $(document).ready(function() {
    $("#sortedtable").tablesorter({ sortlist: [0,0] });
  });
  </script>
  <style type="text/css">
    #sortedtable thead th {
      color: #f00;
      font-weight: bold;
      text-decoration: underline;
    }
  </style>
	
</head>
<body>
<?php require_once('header.php'); ?>
<div id="wrapper">
  <div id="mainBox">
    <div class="itemSortBlock">
      <table id="sortedtable" class="itemSortTable"> 
      <thead>
      <tr class="headingFirst">
      <th rowspan="2">FX会社</th>
      <th colspan="4" class="sorter-false">スプレッド</th>
      <th rowspan="2">キャッシュ<br />
      バック<br />
      金額</th>
      <th rowspan="2" class="sorter-false">&nbsp;</th>
      </tr>
      <tr class="headingSecond">
      <th>
      <p>米ドル<br />
      /円</p>
      <th>
      <p>ユーロ<br />
      /円</p>
      <th>
      <p>ユーロ<br />
      /米ドル</p>
      <th>
      <p>豪ドル<br />
      /円</p>
      </tr>
      </thead>
      <tbody>
      <?php
        if (mysql_num_rows($result) > 0) {
          // output data of each row
          while($row = mysql_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td class=\"companyCol\">";
            echo "<p class=\"companyName\"><a target=\"_self\" href=\"http://ad2.trafficgate.net/t/r/";
            echo $row['url'];
            echo "/280656_349591/\"><img width=\"120\" height=\"60\" alt=\"";
            echo $row['name'];
            echo "\" src=\"http://srv2.trafficgate.net/t/b/";
            echo $row['img_url'];
            echo "/280656_349591/\" /><br />";
            echo "<span>".$row['name']."</span></a></p>";
            echo "</td>";
            echo "<td>";
            echo $row['usd_jpy'];
            echo "<em class=\"note\">原則固定</em></td>";
            echo "<td>";
            echo $row['euro_jpy'];
            echo "<em class=\"note\">原則固定</em></td>";
            echo "<td>";
            echo $row['euro_usd'];
            echo "<em class=\"note\">原則固定</em></td>";
            echo "<td>";
            echo $row['aud_jpy'];
            echo "<em class=\"note\">原則固定</em></td>";
            echo "<td>最大<br />";
            echo $row['money'];
            echo "</td>
            <td>";
            echo "<p><a href=\"edit.php?id=";
            echo $row['id'];
            echo "\">EDIT</a></p>";
            echo "<br><p><a href=\"delete.php?id=";
            echo $row['id'];
            echo "\">DELETE</a></p>";
            echo "</td>";
            echo "</tr>";
          }
        }
      ?>
      </tbody>
      </table>
    </div>
  </div>
</div>