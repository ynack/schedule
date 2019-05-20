<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery-3.3.1.js"></script>
		<?php
			$ua = $_SERVER["HTTP_USER_AGENT"];
			if(strpos($ua,"iPhone"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_header.css\" />\n";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/iphone_main.css\" />\n";
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
		<style>
			.LoginForm
			{
				width:200px;
				height:180px;
				//border:1px solid;
				margin-left: auto;
				margin-right: auto;
			}
			.text-form
			{
				width:210px;
				height:30px;
				//border:1px solid;
				margin-left: 18px;
				margin-right: auto;
			}
		</style>
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
		<div style="margin-top:60px";></div>
		<div class="title-lg">
			fmt メディア事業 勤怠予定表 ユーザ登録
		</div>
		<div class="LoginForm">
			<form name="reg" action="./user_registration.php" method="POST" onsubmit="return reg_check()">
				<div style="margin-top:15px;" class="text-form">
					<input type="text" name="staffid" class="input-text input-text_usercr" placeholder="社員番号">
					<div class="attention">※必須</div>
				</div>
				<div style="margin-top:8px;margin-bottom:12px;" class="text-form">
					<input type="text" name="name"class="input-text input-text_fln" placeholder="姓" style="display:inline;margin-left:0px;">
					<input type="tetx" name="f_name" class="input-text input-text_fln" placeholder="名"  style="margin-left: 9px;"/>
					<div class="attention">※必須</div>
				</div>
				<div  class="text-form" style="margin-bottom:12px;">
					<select name="division" class="select_ucr classic">
						<script src="../js/div.js"></script>
					</select>
				</div>
	<!--			<div>
					<select name="place">
						<script src="./js/place.js"></script>
					</select>
				</div>-->
				<div style="margin-bottom:15px;" class="text-form">
					<input type="password" name="pass" class="input-text input-text_usercr" placeholder="ログインパスワード">
					<div class="attention">※必須</div>
				</div>
				<div class="button_form-hg">
					<button type="submit" class="btn">登録</button>
					<button type="reset" class="btn">入力取消</button>
					<button type="button" class="btn" onclick="location.href='../login.php'">戻る</button>
				</div>
				<input type="hidden" name="operation" value="add" />
			</form>
		</div>
	</body>
</html>