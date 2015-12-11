<?php
  require_once(dirname(__FILE__).'/../class/dbconnect.php');
  
  $status = "";
  if(isset($_POST['new']) && $_POST['new']==1)
  {
    $name =$_REQUEST['name'];
    $url  =$_REQUEST['url'];
    $img_url =$_REQUEST['img_url'];
    $money =$_REQUEST['money'];
    $usd_jpy =$_REQUEST['usd_jpy'];
    $euro_usd =$_REQUEST['euro_usd'];
    $euro_jpy =$_REQUEST['euro_jpy'];
    $aud_jpy =$_REQUEST['aud_jpy'];
    $graph1 =$_REQUEST['graph1'];
    $graph2 =$_REQUEST['graph2'];
    $graph3 =$_REQUEST['graph3'];
    $graph4 =$_REQUEST['graph4'];
    $graph5 =$_REQUEST['graph5'];
    $graph6 =$_REQUEST['graph6'];
    $point_text1 =$_REQUEST['point_text1'];
    $point_text2 =$_REQUEST['point_text2'];
    $point_text3 =$_REQUEST['point_text3'];
    $point =$_REQUEST['point'];
    $description =$_REQUEST['description'];
    $redirect_link =$_REQUEST['redirect_link'];
    $short_name =$_REQUEST['short_name'];
    $sql_insert = "INSERT INTO `company_details` (`name`,`url`,`img_url`,`money`,`usd_jpy`,`euro_usd`,`euro_jpy`,`aud_jpy`,`graph1`,`graph2`,`graph3`,`graph4`,`graph5`,`graph6`,`point_text1`,`point_text2`,`point_text3`,`point`,`description`,`redirect_link`,`short_name`) values ('$name','$url','$img_url','$money','$usd_jpy','$euro_usd','$euro_jpy','$aud_jpy','$graph1','$graph2','$graph3','$graph4','$graph5','$graph6','$point_text1','$point_text2','$point_text3','$point','$description','$redirect_link','$short_name')";
    // die(var_dump($sql_insert));
    mysql_query($sql_insert) or die(mysql_error());
    $status = "New Record Inserted Successfully.";
    echo "<script>";
    echo "alert($status)";
    echo "</script>";
    header('Location:company.php');
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
</head>
<body>
<?php require_once('header.php'); ?>
<div class="form">
  <div align="center">
  <h1>Insert New FX Company</h1>
  <form name="form" method="post" action="" text-align="center"> 
    <input type="hidden" name="new" value="1" />
    <p><input type="text" name="name" placeholder="Enter Name" required /></p>
    <p><input type="text" name="url" placeholder="Enter Url" required /></p>
    <p><input type="text" name="img_url" placeholder="Enter Image Url" required /></p>
    <p><input type="text" name="money" placeholder="Enter Money Pack" required /></p>
    <p><input type="number" step="0.01" min=0 name="usd_jpy" placeholder="Enter USD/JPY rate" required /></p>
    <p><input type="number" step="0.01" min=0 name="euro_usd" placeholder="Enter EURO/USD rate" required /></p>
    <p><input type="number" step="0.01" min=0 name="euro_jpy" placeholder="Enter EURO/JPY rate" required /></p>
    <p><input type="number" step="0.01" min=0 name="aud_jpy" placeholder="Enter AUD/JPY rate" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph1" placeholder="Enter Graph value 1" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph2" placeholder="Enter Graph value 2" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph3" placeholder="Enter Graph value 3" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph4" placeholder="Enter Graph value 4" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph5" placeholder="Enter Graph value 5" required /></p>
    <p><input type="number" step="0.01" min=0 name="graph6" placeholder="Enter Graph value 6" required /></p>
    <p><input type="text" name="point_text1" placeholder="Enter the first Point" required /></p>
    <p><input type="text" name="point_text2" placeholder="Enter the second Point" required /></p>
    <p><input type="text" name="point_text3" placeholder="Enter the third Point" required /></p>
    <p><input type="number" min=0 name="point" placeholder="Enter Point rate" required /></p>
    <p><input type="text" name="description" placeholder="Enter the description" required /></p>
    <p><input type="text" name="redirect_link" placeholder="Enter the short redirect link name" required /></p>
    <p><input type="text" name="short_name" placeholder="Enter the short view name" required /></p>
    <p>
      <input name="submit" type="submit" value=" Submit " style="color:#FF0000;" />
      <input name="reset" type="reset" value=" Reset " style="color:#FF0000;" />
      <button><a href="company.php"> Cancel </a></button>
    </p>
  </form>
  <p style="color:#FF0000;"><?php echo $status; ?></p>
  </div>
</div>
</body>
</html>