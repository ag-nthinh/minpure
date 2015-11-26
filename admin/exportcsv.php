<?php
require_once(dirname(__FILE__).'/../conf/ini.php');

$obj = new Sales();
if(!$filename = $obj -> exportFile($_GET['m'])){
	echo "出力失敗しました。";
}

?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="./css/thickbox.css" type="text/css" />
<link rel="stylesheet" href="../css/common.css" type="text/css" />
<link rel="stylesheet" href="../css/jquery-ui-1.8.16.custom.css" type="text/css" />
<link rel="stylesheet" href="acbox/css/jquery.ajaxComboBox.css" type="text/css" />

<title>CSV</title>
</head>
<body>
        <input type="button" value="閉じる" onclick="window.close()">
<script language="javascript">
        location.href = "<?php echo $filename; ?>";
</script>
</body>
</html>
