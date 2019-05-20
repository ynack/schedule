<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="./js/jquery.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/main.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/ui.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/iphone/layout.css\" />\n";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android_header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/android_main.css\" />\n";				
			}
			else if(strpos($ua,"Window"))
			{
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win_header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/win_main.css\" />\n";				
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"./css/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"./css/Clndr/main.css\" />\n";
			}
		?>
		
	</head>
	<body>
		<header>
		</header>
		<div class="top-space"></div>
		<div class="title-nm">
			fmt メディア事業 勤怠管理
		</div>
		<div class="LoginForm">
			<form action="./sec/auth.php" method="post">
				<div style="margin-top:15px;" class="text-form">
					<input type="text" name="staffid" class="input-text_login" placeholder="社員番号">
				</div>
				<div class="text-form">
					<input type="password" name="passwd" class="input-text_login" placeholder="パスワード">
				</div>
				<div class="button_form-md">
					<button type="submit" class="btn">ログイン</button>
					<button type="reset" class="btn">入力取消</button>
				</div>
			</form>
			<div class="reg_link">
				初めて使用する場合-><a href="./sec/create_user.php">ユーザ登録</a><br />
				パスワード忘れ・変更-><a href="./sec/change_pass.php">パスワード変更</a>
			</div>
		</div>
	</body>
</html>