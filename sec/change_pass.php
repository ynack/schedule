<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
				echo "<link rel=\"stylesheet\" href=\"../css/font/style.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android_main.css\" />\n";				
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />\n";
			}
		?>

		<script>
			/*	submit前にアラート	*/
			function reg_check()
			{
				var staffid = document.reg.staffid.value;	/*	社員番号	*/
				var name = document.reg.name.value;	/* 名前	*/
				var div = document.reg.division.value;	/* 所属	*/
				//var place = document.reg.place.value;	/* 勤務場所 */
				var pass = document.reg.pass.value;	/* パスワード	*/
				
				if(div == "0051A")
				{
					div = "メディア事業";
				}

				var str = "";
				var cnt = pass.length;


				for(var a=1; a<=cnt; a++)
				{
					str = str + "*";
				}

				var data = staffid + "\n" + name + "\n" + div + "\n" + place + "\n" + str + "\n";
				
				if(window.confirm("以下の内容で登録します\n\n"+data))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		</script>
	</head>
	<body>
		<header>
		</header>
		<div class="top-space"></div>
		<div class="title-hg">
			fmt メディア事業 勤怠管理 パスワード変更
		</div>
		
		<form name="reg" action="./user_registration.php" method="POST" onsubmit="return reg_check()">
			<div class="content-inner-bg" style="height:100px;">
				<div>
					<input type="text" name="staffid" class="input-text input-text_usercr" placeholder="社員番号">
					<div class="attention">※必須</div>
				</div>
				<div class="text-position1">
					新しいパスワード
				</div>	
				<div>
					<input type="password" name="pass" class="input-text input-text_usercr" placeholder="ログインパスワード">
				</div>
			</div>
			<div class="button_form-hg">
				<button type="submit" class="btn">変更</button>
				<button type="reset" class="btn">入力取消</button>
				<button type="button" class="btn" onclick="location.href='../login.php'">戻る</button>
			</div>
			<input type="hidden" name="operation" value="update" />
		</form>
	</body>
</html>