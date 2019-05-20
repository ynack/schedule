<?php
	session_start();

	if(!isset($_SESSION["staffid"]))
	{
		header("Location:../login.php");
	}
	else
	{
		$staffid = $_SESSION["staffid"];

		/* result.phpからPOSTされてきたデータ	*/
		$date = $_POST["reg_date"];
		$work_start = $_POST["start"];
		$work_finish = $_POST["finish"];
		$allday = $_POST["AllDay"];
		$amflg = $_POST["am"];
		$pmflg = $_POST["pm"];
		$work_content = $_POST["work"];
		$op = $_POST["operation"];
		$oldwork = $_POST["oldwork"];

		$evweek = $_POST["evweek"];
		$evoweek = $_POST["evoweek"];
		$term = $_POST["term"];
		$reuse_yr = $_POST["r_year"];
		$reuse_mnt = $_POST["r_month"];
		$reuse_dy = $_POST["r_day"];

		$_SESSION["reg_date"] = $_POST["reg_date"];
		$_SESSION["start"] = $_POST["start"];
		$_SESSION["finish"] = $_POST["finish"];
		$_SESSION["AllDay"] = $_POST["AllDay"];
		$_SESSION["work"] = $_POST["work"];
		$_SESSION["am"] = $_POST["am"];
		$_SESSION["pm"] = $_POST["pm"];

		if(!strcmp($op,"mail"))
		{
			/* 申請メール対応から来た時の処理	*/
			$s_year = $_POST["s_year"];
			$s_month = $_POST["s_month"];
			$s_day = $_POST["s_day"];
			$e_year = $_POST["e_year"];
			$e_month = $_POST["e_month"];
			$e_day = $_POST["e_day"];

			$role = $_POST["role"];

			$sdate = $s_year."-".$s_month."-".$s_day;
			$edate = $e_year."-".$e_month."-".$e_day;

			if(!strcmp($role,"main"))
			{
				$main = 1;
				$sub = 0;
			}
			else
			{
				$main = 0;
				$sub = 1;
			}
		}

		if(!strcmp($op,"del"))
		{
			$deldate = $_POST["d_date"];
			$delwork = $_POST["d_work"];
		}

		if(isset($_POST["mng_reg"]))
		{
			$reg_id = $_POST["mng_reg"];
		}
		else
		{
			$reg_id = "";
		}

		/* re_regist.phpからのみPOSTされてくるデータ	*/
		if(isset($_POST["year"]))
		{
			$yr = $_POST["year"];
		}

		if(isset($_POST["month"]))
		{
			$mn = $_POST["month"];
		}

		if(isset($_POST["day"]))
		{
			$dy = $_POST["day"];
		}

		if(!empty($yr) || !empty($mn) || !empty($dy) )
		{
			$date = $yr."-".$mn."-".$dy;
		}

		/*	期間指定した場合の年月日	*/
		if(isset($_POST["r_year"]))
		{
			$reuse_yr = $_POST["r_year"];
		}

		if(isset($_POST["r_month"]))
		{
			if($_POST["r_month"] >= 1 && $_POST["r_month"] <= 9)
			{
				$reuse_mnt = "0".$_POST["r_month"];
			}
			else
			{
				$reuse_mnt = $_POST["r_month"];
			}
		}

		if(isset($_POST["r_day"]))
		{
			if($_POST["r_day"] >= 1 && $_POST["r_day"] <= 9)
			{
				$reuse_dy = "0".$_POST["r_day"];
			}
			else
			{
				$reuse_dy = $_POST["r_day"];
			}
		}

		if(!empty($reuse_yr) || !empty($reuse_mnt) || !empty($resue_dy))
		{
			$cont_date = $reuse_yr."-".$reuse_mnt."-".$reuse_dy;
		}

		if($allday == "on")
		{
			$ad = "終日";
		}
		if($amflg == "on")
		{
			$am = "午前";
		}

		if($pmflg == "on")
		{
			$pm = "午後";
		}

		/*	出力チェック	*/
		/*
		echo "s:".$staffid."<br />";
		echo "d:".$date."<br />";
		echo "ws:".$work_start."<br />";
		echo "wf".$work_finish."<br />";
		echo "ad:".$allday."<br />";
		echo "am:".$amflg."<br />";
		echo "pm".$pmflg."<br />";
		echo "wc:".$work_content."<br />";
		echo "op:".$op."<br />";
		echo "ow:".$oldwork;
		*/
		include("../sec/acc.php");

		try
		{
			$pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

			if(!strcmp($op,"add"))
			{
				$stmt = $pdo->prepare("insert into schedule values(:id,:date,:start,:finish,:allflg,:amflg,:pmflg,:work,:applo)");
			}
			else if(!strcmp($op,"update"))
			{
				$stmt = $pdo->prepare("update schedule set starttime=:start,finishtime=:finish,allday=:allflg,am=:amflg,pm=:pmflg,work=:work,apploval=:applo where staffid=:id and date=:date and work=:owork");
			}
			else if(!strcmp($op,"del"))
			{
				$stmt = $pdo->prepare("delete from schedule where staffid=:id and date=:date and work=:work");			
			}
			else if(!strcmp($op,"mail"))
			{
				$sql = "insert into mail_work values(:id,:date,:main,:sub)";
				$stmt = $pdo->prepare($sql);
			}

			if(!strcmp($op,"add") || !strcmp($op,"update"))
			{
				if(!strcmp($evweek,"on"))
				{
					/*	毎週登録	*/
					$datetime1 = new DateTime($date);
					$datetime2 = new DateTime($cont_date);
					$intvl = $datetime1->diff($datetime2);
					$term_num = $intvl->format("%a");

					$s_date = new DateTime($date);
					for($i = 0; $i <= $term_num; $i=$i+7)
					{
						if(strcmp($reg_id,""))
						{
							$stmt->bindParam(':id',$reg_id,PDO::PARAM_INT);
							$app = 0;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						else
						{
							$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
							$app = 1;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						$stmt->bindParam(':date',$s_date->format('Y-m-d'),PDO::PARAM_STR);
						$stmt->bindParam(':start',$work_start,PDO::PARAM_STR);
						$stmt->bindParam(':finish',$work_finish,PDO::PARAM_STR);
						$stmt->bindParam(':allflg',$allday,PDO::PARAM_STR);
						$stmt->bindParam(':amflg',$amflg,PDO::PARAM_STR);
						$stmt->bindParam(':pmflg',$pmflg,PDO::PARAM_STR);
						$stmt->bindParam(':owork',$oldwork,PDO::PARAM_STR);

						if(!strcmp($op,"update"))
						{
							$stmt->bindParam(':work',$work_content,PDO::PARAM_STR);
						}

						$stmt->execute();

						$s_date->modify('+7 days');	//14日追加
					}
				}
				else if(!strcmp($evoweek,"on"))
				{
					/*	隔週で登録	*/
					$datetime1 = new DateTime($date);
					$datetime2 = new DateTime($cont_date);
					$intvl = $datetime1->diff($datetime2);
					echo $term_num = $intvl->format("%a");

					$s_date = new DateTime($date);
					for($i = 0; $i <= $term_num; $i=$i+14)
					{
						if(strcmp($reg_id,""))
						{
							$stmt->bindParam(':id',$reg_id,PDO::PARAM_INT);
							$app = 0;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						else
						{
							$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
							$app = 1;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						$stmt->bindParam(':date',$s_date->format('Y-m-d'),PDO::PARAM_STR);
						$stmt->bindParam(':start',$work_start,PDO::PARAM_STR);
						$stmt->bindParam(':finish',$work_finish,PDO::PARAM_STR);
						$stmt->bindParam(':allflg',$allday,PDO::PARAM_STR);
						$stmt->bindParam(':amflg',$amflg,PDO::PARAM_STR);
						$stmt->bindParam(':pmflg',$pmflg,PDO::PARAM_STR);
						$stmt->bindParam(':work',$work_content,PDO::PARAM_STR);

						if(!strcmp($op,"update"))
						{
							$stmt->bindParam(':owork',$oldwork,PDO::PARAM_STR);
						}

						$stmt->execute();

						$s_date->modify('+14 days');	//14日追加
					}
				}
				else if(!strcmp($term,"on"))
				{
					/*	期間を指定して登録	*/
					if(!strcmp($op,"mail"))
					{
						$datetime1 = new DateTime($sdate);
						$datetime2 = new DateTime($edate);
					}
					else
					{
						$datetime1 = new DateTime($date);
						$datetime2 = new DateTime($cont_date);
					}
					
					$intvl = $datetime1->diff($datetime2);
					$term_num = $intvl->format("%a");

					if(!strcmp($op,"mail"))
					{
						$s_date = new DateTime($sdate);
					}
					else
					{
						$s_date = new DateTime($date);
					}

					for($i = 0; $i <= $term_num; $i++)
					{
						if(strcmp($reg_id,""))
						{
							$stmt->bindParam(':id',$reg_id,PDO::PARAM_INT);
							$app = 0;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						else
						{
							$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
							$app = 1;
							$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
						}
						
						$stmt->bindParam(':date',$s_date->format('Y-m-d'),PDO::PARAM_STR);
						$stmt->bindParam(':start',$work_start,PDO::PARAM_STR);
						$stmt->bindParam(':finish',$work_finish,PDO::PARAM_STR);
						$stmt->bindParam(':allflg',$allday,PDO::PARAM_STR);
						$stmt->bindParam(':amflg',$amflg,PDO::PARAM_STR);
						$stmt->bindParam(':pmflg',$pmflg,PDO::PARAM_STR);
						$stmt->bindParam(':work',$work_content,PDO::PARAM_STR);

						if(!strcmp($op,"update"))
						{
							$stmt->bindParam(':owork',$oldwork,PDO::PARAM_STR);
						}

						$stmt->execute();

						$s_date->modify('+1 days');	//1日追加
					}			
				}
				else
				{
					/* 一日分だけ登録	*/
					if(strcmp($reg_id,""))
					{
						$stmt->bindParam(':id',$reg_id,PDO::PARAM_INT);
						$app = 0;
						$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
					}
					else
					{
						$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
						$app = 1;
						$stmt->bindParam(':applo',$app,PDO::PARAM_INT);
					}
					
					$stmt->bindParam(':date',$date,PDO::PARAM_STR);
					$stmt->bindParam(':start',$work_start,PDO::PARAM_STR);
					$stmt->bindParam(':finish',$work_finish,PDO::PARAM_STR);
					$stmt->bindParam(':allflg',$allday,PDO::PARAM_STR);
					$stmt->bindParam(':amflg',$amflg,PDO::PARAM_STR);
					$stmt->bindParam(':pmflg',$pmflg,PDO::PARAM_STR);
					$stmt->bindParam(':work',$work_content,PDO::PARAM_STR);
					

					if(!strcmp($op,"update"))
					{
						$stmt->bindParam(':owork',$oldwork,PDO::PARAM_STR);
					}

					$stmt->execute();
				}
			}
			else if(!strcmp($op,"mail"))
			{
				/*	期間を指定して登録	*/
				$datetime1 = new DateTime($sdate);
				$datetime2 = new DateTime($edate);
				
				$intvl = $datetime1->diff($datetime2);
				$term_num = $intvl->format("%a");

				$s_date = new DateTime($sdate);

				for($i = 0; $i <= $term_num; $i++)
				{
					if(isset($reg_id))
					{
						$stmt->bindParam(':id',$reg_id,PDO::PARAM_INT);
					}
					else
					{
						$stmt->bindParam(':id',$staffid,PDO::PARAM_INT);
					}
					
					$stmt->bindParam(':date',$s_date->format('Y-m-d'),PDO::PARAM_STR);
					$stmt->bindParam(':main',$main,PDO::PARAM_INT);
					$stmt->bindParam(':sub',$sub,PDO::PARAM_INT);
					$stmt->execute();

					$s_date->modify('+1 days');	//1日追加
				}
			}
			else if(!strcmp($op,"del"))
			{
				$stmt->bindParam(':id',$staffid,PDO::PARAM_STR);
				$stmt->bindParam(':date',$deldate, PDO::PARAM_STR);
				$stmt->bindParam(':work',$delwork,PDO::PARAM_STR);

				$stmt->execute();
			}
			
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<title>勤怠管理　試用版</title>
		<script src="../js/jquery-3.3.1.min.js"></script>
		<link rel="stylesheet" href="../css/Clndr/header.css" />
		<link rel="stylesheet" href="../css/Clndr/main.css" />
		<style>
			table
			{
				border: solid 1px;
				border-collapse: collapse;
				width:90%;
				height:520px;
				margin-right: auto;
				margin-left: auto;
			}

			tr
			{
				border: solid 1px;
			}

			td
			{
				border: solid 1px;
				text-align: right;
				vertical-align: top;
				width:14%;
			}

			th
			{
				border: solid 1px;
				height:20px;
			}

			a.days
			{
				display: block;
				width: 100%;
				height:100%;

				text-decoration: none;
			}
		</style>
	</head>
	<body>
		<header>
		<?php
			include("./CntHeader/header.php");
		?>
		</header>
		<div style="margin-top:50px;"></div>
		<div class="title-md">
<?php

		if(!strcmp($op,"add") || !strcmp($op,"mail"))
		{
			echo "<h3>以下の内容で登録しました。</h3>\n";
		}
		else if(!strcmp($op,"update"))
		{
			echo "\t\t\t<h3>以下の内容で変更しました。</h3>\n";
		}
		else if(!strcmp($op,"del"))
		{
			echo "<h3>以下の内容を削除しました。</h3>\n";
		}
?>		
		</div>
		<div class="content-md">
<?php
		if(!strcmp($op,"add") || !strcmp($op,"update") || !strcmp($op,"mail"))
		{
			/*	期間指定があれば終了期間も表示	*/
			if(!strcmp($evweek,"on"))
			{
				echo $date."から".$cont_date."まで毎週<br />";
			}
			else if(!strcmp($evoweek,"on"))
			{
				echo $date."から".$cont_date."まで隔週<br />";
			}
			else if(!strcmp($term,"on"))
			{
				echo $date."から".$cont_date."まで<br />";
			}
			else if(!strcmp($op,"mail"))
			{
				echo $sdate."から".$edate."まで<br />";
			}
			else
			{
				echo $date."<br />";
			}

			if($work_start)
			{
				echo $work_start."<br />";
			}

			if($work_finish)
			{
				echo $work_finish."<br />";
			}

			if(!strcmp($allday,"on"))
			{
				echo $ad."<br />";
			}
			else if(!strcmp($amflg,"on"))
			{
				echo $am."<br />";
			}
			else if(!strcmp($pmflg,"on"))
			{
				echo $pm."<br />";
			}

			echo $work_content."<br />";
		}
		else if(!strcmp($op,"del"))
		{
			echo $deldate."<br />";
			echo $delwork."<br />";
		}
	?>
		</div>
		<div class="button_form-lg">
	<?php
		if(strcmp($op,"del"))
		{
	?>
			<div style="margin-bottom:15px;">
				<button type="button" name="reuse" class="btn" onclick="location.href='./re_regist.php'">同じ内容を別日に登録する</button>
			</div>
	<?php
		}
	?>
			<button type="button" name="return" class="btn" onclick="location.href='./schedule.php'">戻る</button>
		</div>
	</body>
</html>
<?php
	}
?>
