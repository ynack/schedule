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
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone/layout.css\" />"; 
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/layout.css\" />"; 
			}
			else if(strpos($ua,"Windows"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/layout.css\" />"; 
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/ui.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/table.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/layout.css\" />"; 
			}
		?>	
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
