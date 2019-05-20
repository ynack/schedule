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
		<script src="../js/jquery-3.3.1.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_main.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_main.css\" />";
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />";
			}
		?>
		<style>
			.out_title
			{
				width:150px;
				margin-top:60px;
			}

			.return_btn
			{
				width:156px;

			}
		</style>
	</head>
	<body>
		<div class="title-sm out_title">
				ログアウトしました
		</div>
		<div class="button_form return_btn">
			<button type="button" class="btn" onclick="location.href='../login.php'">ログイン画面に戻る</button>
		</div>
	</body>
</html>
