<?php
	session_start();	//セッション開始

	if(!isset($_SESSION["staffid"]))
	{
		header("Location:./login.php");	//セッション変数に値が入っていなければログイン画面に戻す
	}
	else
	{
		/* ログインページから社員番号を保持しておく	*/
		/* セッションで保持する	*/
		$staffid = $_SESSION["staffid"]; 
		$u_ag = $_SERVER['HTTP_USER_AGENT'];	//ブラウザのユーザエージェントを取得

		$week_jp = ['（日）','（月）','（火）','（水）','（木）','（金）','（土）'];	//	曜日対応（数字から日本語にする）

		$get_display_date = $_GET["first_day_of_month"];	//URLから年月を取得(GET変数)
		if($get_display_date == null)
		{
			$display_date =  date("Y-m-01");	//年月が指定されていなければ今日が属する月の1日を表示年月日とする
		}
		else
		{
			$display_date = $get_display_date;	//年月が指定されていればその日を表示年月日とする
		}

		$yn = explode("-", $display_date);	//取得した年月日を"-"で分割して配列に格納
		$year = $yn[0];						//分割した年月日の「年」
		$month = $yn[1];					//分割した年月日の「月」

		$last_day = date('j',mktime(0,0,0,$month + 1, 0, $year));	//指定した年月の末日を取得

		/*	年間祝日用配列の読み込み	*/
		include("./holiday.php");

		/* データベースの接続処理	*/
		include("../sec/acc.php");	//データベース接続
		include("../sec/spid.php");	//管理職でも全体スケジュールに表示・上部メニューで個人スケジュールを先に表示するアカウント
		
		try
		{
			$pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		}
		catch (PODException $e)
		{
			exit('データベース接続に失敗しました'.$e->getMessage());
		}

		if(count($spid) > 0)
		{
			//全体に表示したい管理職アカウントがあった場合
			//sql文を追加
			$mngvw = " or staff.staffid = ".$spid[0];

			if(count($spid) > 1)
			{
				//全体に表示したい管理職アカウントが2つ以上あった場合
				for( $j = 1; $j < count($spid); $j++)
				{
					//アカウント数分のsql文を追加
					$mngvw = $mngvw." or staff.staffid = ".$spid[$j];
				}			
			}
			$sql = "select distinct account.staffid from account join staff on account.staffid = staff.staffid where staff.mngflg = 0".$mngvw." order by staffid";
		}
		else
		{
			$sql = "select distinct account.staffid from account join staff on account.staffid = staff.staffid where staff.mngflg = 0 order by staffid";
		}

		$RwCnt = $pdo->query($sql);
		$RwCnt->execute();
		$count=$RwCnt->rowCount();
		$name = $RwCnt->fetchAll();

		/* ログインしているstaffidが管理者かどうかチェックするのにmngflgの値を取得	*/
		include("./php/mngck.php");
//		$mngsql = "select mngflg from staff where staffid = ".$staffid;
//		$mngck = $pdo->query($mngsql);
//		$mngck->execute();
//		$mngacc = $mngck->fetch();
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf8" />
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
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/font/style.css\" />";
			}
			else if(strpos($ua,"Android"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/android/main.css\" />";
			}
			else if(strpos($ua,"Windows"))
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/win/main.css\" />";
			}
			else
			{
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/header.css\" />";
				echo "<link rel=\"stylesheet\" href=\"../css/Clndr/main.css\" />";
			}
		?>	
		<style>
			a.days
			{
				display: block;
				width: 100%;
				height:100%;

				text-decoration: none;
			}

			.def_a
			{
				display:inline;
			}

			.str_right
			{
				text-align: right;
			}
		</style>
		
	</head>
	<body>
		<header>
		<?php include("../include_php/device_header.php");?>
		</header>
		<div class="top-space"></div>
		<div style="text-align:center; margin-bottom:-10px;">
			<h3>
				<a href="manage.php?first_day_of_month=<?php echo date('Y-m-01',strtotime("-1 month",strtotime($display_date))); ?>" class="def_a"><<</a>
				<?php echo date('Y-n',strtotime($display_date)); ?>
				<a href="manage.php?first_day_of_month=<?php echo date('Y-m-01',strtotime("+1 month",strtotime($display_date))); ?>" class="def_a">>></a>
			</h3>
		</div>
		<div class="mail_work">
			<span style="font-size:12px;">■申請メール対応：</span>
			<span class="mail_position" style="font-size:12px;">【<span style="color:#33ff99;font-size:14px;">■</span>:メイン <span style="color:#ffcc99;font-size:14px;">■</span>:サブ】-></span>
			<span class="textposition" style="font-size:11px;text-decoration: underline;"><a href="./mail_regist.php">申請メール対応登録</a></span>
		</div>

		<table id="mngtbl">
			<?php
				/*	月と名前行	*/
				echo "<tr>\n";
				echo "<td class='str_right' style='width:1%;'>\n";
				echo str_replace('0','',$month)."月&nbsp;&nbsp;";
				echo "</td>\n";

				foreach($name as $id)
				{					
					$sql = "select account.name from account join staff on account.staffid = staff.staffid where (staff.mngflg=0".$mngvw." ) and account.staffid=".$id['staffid'];
					$sName = $pdo->query($sql);
					$sName->execute();
					$usr = $sName->fetch();

					echo "<td style='text-align:center; width:15%;'>".$usr['name']."</td>\n";
				}
			
				echo "</tr>\n";

				for($j = 1; $j <= $last_day; $j++)
				{
					echo "<tr>\n";

					if( $j >=1 && $j <= 9)
					{
						$days = date('Y-m',strtotime($display_date))."-0".$j;
					}
					else
					{
						$days = date('Y-m',strtotime($display_date))."-".$j;
					}

					/*日付と曜日の出力	*/
					$dt = mktime(0,0,0,$month,$j,$year);
					$dotw = date("w",$dt);
					$clr = 0;

					if($dotw == 0)
					{
						echo "<td class='str_right' bgcolor='#ffe5e5' nowrap >\n";
					}
					else if($dotw == 6)
					{
						echo "<td class='str_right' bgcolor='#e5e5ff' nowrap >\n";
					}
					else
					{
						$holiflg = 0;
						for($h = 0; $h < count($holiday); $h++)
						{
							if(!strcmp($days,$holiday[$h])){	$holiflg = 1;	}
							if($holiflg == 1){	break;	}
						}
						if($holiflg == 1)
						{
							echo "<td style='background-color:#ffe5e5;'>\n";
						}
						else
						{
							echo "<td class='str_right'>\n";
						}
					}
					
					echo $j;				
					echo $week_jp[$dotw];
					echo "</td>\n";

					for($k = 0; $k < $count; $k++)
					{
						/* スケジュール */
						$stmt = $pdo->prepare("select work,apploval from schedule where staffid = :staffid and date=:date");
						$stmt->bindParam(':staffid',$name[$k][0]);					
						$stmt->bindParam(':date',$days);
						$stmt->execute();
						$wrkCnt = $stmt->rowCount();
						//$rs = $stmt->fetch();
						$rs = $stmt->fetchAll();

						if($dotw == 0)
						{
							echo "<td bgcolor='#ffe5e5'>\n";
						}
						else if($dotw == 6)
						{
							echo "<td bgcolor='#e5e5ff'>\n";
						}
						else
						{
							for($h = 0; $h < count($holiday); $h++)
							{
								if(!strcmp($days,$holiday[$h]))
								{
									$holiflg = 1;
								}

								if($holiflg == 1)
								{
									break;
								}
							}
							if($holiflg == 1)
							{
								echo "<td style='background-color:#ffe5e5;'>\n";
							}
							else
							{
								/*
								for($i = 0; $i < $wrkCnt; $i++)
								{
									if(!strcmp($rs[$i]['work'],"mail_main"))	 //$rs[$i]["work"]にmail_mainが含まれている
									{
										$mail_work = 1;
									}
									else if(!strcmp($rs[$i]['work'],"mail_sub"))	//$rs[$i]["work"]にmail_subが含まれている
									{
										$mail_work = 2;
									}
									if($mail_work == 1 || $mail_work == 2)	//$rs[$i]["work"]にmail_mainもmail_subが含まれていない
									{
										break;
									}
								}
								if($mail_work == 1)
								{
									echo "<td style='background-color:#c1ffc1;'>\n";
								}
								else if($mail_work == 2)
								{
									echo "<td style='background-color:#ffe0c1;'>\n";
								}
								else
								{
									echo "<td style='vartical-align:middle;'>\n";
								}
								$mail_work = 0;	
								*/
								echo "<td>";
							}
							$holiflg = 0;
						}

						if($mngacc["mngflg"] == 1)	//管理職フラグが1の場合はカラムクリックでスケジュール登録に移動可とする
						{
							echo "<a class='days' href='./result.php?regDay=".$days."&id=".$name[$k][0]."'>";
						}

						if(empty($rs[0]['work']) && ($dotw == 0 || $dotw == 6))	//業務登録がなく、曜日値が0（日）か6（土）だったら「休」
						{
							echo "休";
						}
						else if(empty($rs[0]['work'])&& ($dotw >= 1 || $dotw <=5))
						{
							for($h = 0; $h < count($holiday); $h++)
							{
								if(!strcmp($days,$holiday[$h]))	//祝日用配列の要素と$daysの値が一緒だったら祝日フラグを1に
								{
									$holiflg = 1;
								}

								if($holiflg == 1)	//祝日フラグが立ったらfor分からbreak;
								{
									break;
								}
							}
							if($holiflg == 1)	//祝日フラグが1の場合はデフォルトが「休」
							{
								echo "休";
							}
							else
							{
								echo "出";
							}
						}
						else
						{
							for($i = 0; $i < $wrkCnt; $i++)
							{
								if($rs[$i]["apploval"] == 0)	//未承認の場合は赤文字にする
								{
									echo "<span style='color:red'>";
								}
								/*
								if(strcmp($rs[$i]['work'],"mail_main") && strcmp($rs[$i]["work"],"mail_sub"))
								{
									echo $rs[$i]["work"];
								}
								*/

								if(!strcmp($rs[$i]["work"],"CX電話番"))
								{
									echo "<i class='icon-phone' style='font-size:14px;vertical-align:middle;'></i>";
								}
								else if(!strcmp($rs[$i]['work'],"mail_main"))
								{
									echo "<i class='icon-mail_main' style='font-size:16px;margin-right:12px;margin-left:8px;'></i>";
								}
								else if(!strcmp($rs[$i]['work'],"mail_sub"))
								{
									echo "<i class='icon-mail_sub' style='font-size:16px;margin-right:12px;margin-left:8px;'></i>";
								}
								else
								{
									echo $rs[$i]["work"];
								}
								if($rs[$i]["apploval"] == 0){	echo "</span>";	}
								if($i != $wrkCnt - 1)
								{
									if(strcmp($rs[$i]['work'],"mail_main") && strcmp($rs[$i]["work"],"mail_sub"))
									{
										echo "<br />";
									}
								}
							}
						}
						echo "</a></td>\n";	
					}				
					echo "</tr>";		
				}
			?>			
		</table>
	</body>
</html>
<?php
	}
?>