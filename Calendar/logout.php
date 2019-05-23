<?php
	session_start();
	
	unset($_SESSION["staffid"]);

	session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery.js"></script>
		<?php	include("../include_php/ua_css.php"); ?>
		<link rel="stylesheet" href="../css/font/style.css" />
	</head>
	<body>
		<div class="top-space"></div>
		<div class="title-sm">
				ログアウトしました
		</div>
		<div class="button_form-md">
			<button type="button" class="btn" onclick="location.href='../login.php'">ログイン画面に戻る</button>
		</div>
	</body>
</html>
