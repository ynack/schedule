<?php
	session_start();

	if(!isset($_SESSION["staffid"]))
	{
		header("Location:../login.php");
	}
	else
	{
		/* ログインページから社員番号を保持しておく	*/
		$staffid = $_SESSION["staffid"];

		$first_date = $_SESSION["reg_date"];
		$work_start = $_SESSION["start"];
		$work_finish = $_SESSION["finish"];
		$allday = $_SESSION["AllDay"];
		$work_content = $_SESSION["work"];
		$am = $_SESSION["am"];
		$pm = $_SESSION["pm"];

		$regist_date = $_GET["regDay"];

		if($regist_date == null)
		{
			$display_date =  date("Y-m-01");
		}
		else
		{
			$display_date = $regist_date;
		}

		$yn = explode("-", $first_date);
		$year = $yn[0];
		$month = $yn[1];

		if(!strcmp(substr($month,0,1),'0'))
		{
			$month = str_replace("0","",$month);
		}

		$last_day = date('j',mktime(0,0,0,$month + 1, 0, $year));
		$calender = array();
		$j = 0;

		for($i = 1; $i < $last_day + 1; $i++)
		{
			$week = date('w', mktime(0,0,0,$month,$i,$year));
			if($i == 1)
			{
				for($s = 1; $s <= $week; $s++)
				{
					$calender[$j]['day'] = '';
					$j++;
				}
			}

			$calender[$j]['day'] = $i;
			$j++;

			if($i == $last_day)
			{
				for($e = 1; $e <= 6 - $week; $e++)
				{
					$calender[$j]['day'] = '';
					$j++;
				}
			}
		}

		include("../sec/acc.php");
		
		try
		{
			$pdo = new PDO($dsn,$user,$pass);
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}

		$stmt = $pdo->query("select * from staff where staffid = ".$staffid);
		foreach($stmt as $row)
		{
			$name = $row["name"];
		}

		$stmt2 = $pdo->query("select * from schedule where staffid = ".$staffid." && date = '".$regist_date."'");
		foreach($stmt2 as $row)
		{
			$today_start = $row["start"];
			$today_finish = $row["finish"];
			$today_allday = $row["allday"];
			$today_work = $row["work"];
		}
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery-3.3.1.js"></script>
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
		</style>
		<script>
			/* チェックボックスでセレクトボックスの有効化/無効化	*/
			function AllDayChk(selectid1,selectid2,check1,check2,ischecked)
			{
				if(ischecked == true)
				{
					document.getElementById(selectid1).disabled = true;
					document.getElementById(selectid2).disabled = true;
					document.getElementById(check1).checked = false;
					document.getElementById(check2).checked = false;
				}
				else
				{
					document.getElementById(selectid1).disabled = false;
					document.getElementById(selectid2).disabled = false;
				}
			}

			/*	submit前にアラート	*/
			function reg_check()
			{

				var start = document.workplan.start.value;	/*	開始時間	*/
				var finish = document.workplan.finish.value;	/* 終了時間	*/
				//var dt = document.workplan.reg_date.value;	/* 日付	*/
				var work = document.workplan.work.value;	/* 業務内容 */
				var ad = document.workplan.AllDay.value;	/* 終日フラグ	*/

				var yr = document.workplan.year.value;
				var mn = document.workplan.month.value;
				var dy = document.workplan.day.value;

				var dt = yr + "-" + mn + "-" + dy;

				var data = dt + "\n" + work + "\n";

				if( ad == "on")
				{
					data = data + "終日";
				}
				else
				{
					data = data + start + " - " + finish;
				}
				
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
		<?php include("../include_php/device_header.php"); ?>
		</header>
		<div class="top-space";></div>
		<form name="workplan" action="./work_regist.php" method="POST" onsubmit="return reg_check()">
			<div class="title-hg">
				<select name="year" id="yr">
					<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
					<script src="../js/year.js"></script>
				</select>
				-
				<select name="month" id="mon">
					<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
					<script src="../js/month.js"></script>
				</select>
				-
				<select name="day" id="dy">
					<script src="../js/day.js"></script>
				</select>
				のスケジュール
			</div>

			<div class="content-inner-bg">
				<div>
					■開始予定時刻：
					<?php 
						if($allday == "on")
						{
							echo "<select name=\"start\" id=\"strt\" disabled>";
						}
						else
						{
							echo "<select name=\"start\" id=\"strt\">";
						}
					?>
						<option src="<?php echo $work_start;?>"><?php echo $work_start;?></option>
						<script src="../js/time.js"></script>
					</select>
				</div>
				<div>
					■終了予定時刻：
					<?php 
						if($allday == "on")
						{
							echo "<select name=\"finish\" id=\"fin\" disabled>";
						}
						else
						{
							echo "<select name=\"finish\" id=\"fin\">";
						}
					?>
						<option src="<?php echo $work_finish;?>"><?php echo $work_finish;?></option>
						<script src="../js/time.js"></script>
					</select>
				</div>
				<div>
					<?php
						if($allday == "on")
						{
							echo "<input type=\"checkbox\" name=\"AllDay\" checked='checked'\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />終日&nbsp;&nbsp;";
						}
						else
						{
							echo "<input type=\"checkbox\" name=\"AllDay\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />終日&nbsp;&nbsp;";
						}

						if($am == "on")
						{
							echo "<input type=\"checkbox\" name=\"am\" checked='checked'\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />午前&nbsp;&nbsp;";
						}
						else
						{
							echo "<input type=\"checkbox\" name=\"am\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />午前&nbsp;&nbsp;";
						}

						if($pm == "on")
						{
							echo "<input type=\"checkbox\" name=\"pm\" checked='checked'\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />午後";
						}
						else
						{
							echo "<input type=\"checkbox\" name=\"pm\" onclick=\"AllDayChk('strt','fin','am','pm',this.checked);\" />午後";
						}
					?>
					
				</div>
				<div>
					業務内容：
				</div>
				
					<textarea name="work" rows="3" cols="30"><?php echo $work_content;?></textarea>
					<input type="hidden" name="reg_date" value="<?php echo $regist_date; ?>" />
					<input type="hidden" name="operation" value="add" />
				<div class="button_form-md">
					<button type="button" class="btn" onclick="history.back();">戻る</button>
					<button type="submit" class="btn">登録</button>		
				</div>
			</div>
		</form>
	</body>
</html>
<?php
	}
?>
