<?php
include("includes/core.php");

$content .= "<h1>Admin</h1>";

if($_SESSION['admin']){
	$AdminSessionKey = $_SESSION['admin'];
	$UserDB = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '12' LIMIT 1")->fetch_assoc()['value'];
	$PwDB = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '13' LIMIT 1")->fetch_assoc()['value'];
	$DatabaseAdminKey = "Admin_".$UserDB."_Password_".$PwDB;
	if($AdminSessionKey != $DatabaseAdminKey){ unset($_SESSION['admin']); header("Location: admin.php"); die; }

	switch($_GET['p']){
		default:
		// Total Stats

		$TotalClaims = $mysqli->query("SELECT COUNT(id) FROM faucet_transactions WHERE type = 'Payout'")->fetch_row()[0];
		$TotalClaimed = $mysqli->query("SELECT SUM(amount) FROM faucet_transactions WHERE type = 'Payout'")->fetch_row()[0];
		$TotalClaimed = $TotalClaimed ? $TotalClaimed : 0;

		// 24 Hours stats

		$Last24Hours = time() - 86400;
		$Last24HoursClaims = $mysqli->query("SELECT COUNT(id) FROM faucet_transactions WHERE type = 'Payout' AND timestamp > '$Last24Hours'")->fetch_row()[0];
		$Last24HoursClaimed = $mysqli->query("SELECT SUM(amount) FROM faucet_transactions WHERE type = 'Payout' AND timestamp > '$Last24Hours'")->fetch_row()[0];
		$Last24HoursClaimed = $Last24HoursClaimed ? $Last24HoursClaimed : 0;

		$content .= "<h2>Stats</h2>
		<div class='row'>
		<div class='col-md-12'>
			<h3>All time</h3>
		</div>
		<div class='col-md-6'>
			<h4>Total Claims</h4>
			<b>".$TotalClaims."</b>
		</div>
		<div class='col-md-6'>
			<h4>Total Claimed</h4>
			<b>".toSatoshi($TotalClaimed)."</b><br />Satoshi
		</div>
		<div class='col-md-12'>
			<h3>Last 24 Hours</h3>
		</div>
		<div class='col-md-6'>
			<h4>Claims</h4>
			<b>".$Last24HoursClaims."</b>
		</div>
		<div class='col-md-6'>
			<h4>Claimed</h4>
			<b>".toSatoshi($Last24HoursClaimed)."</b><br />Satoshi
		</div>
		</div><br /><h2>Configuration</h2>
		<a class='btn btn-default' href='?p=as'>Standard settings</a><br />
		<a class='btn btn-default' href='?p=ps'>Page settings</a><br />
		<a class='btn btn-default' href='?p=ads'>Advertising settings</a><br />";
		break;

		case("as"):
		$content .= "<a href='admin.php'>Back</a><br>
		<h3>Admin Settings</h3><h4>Change Admin login datas</h4>";

		$Username = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '12' LIMIT 1")->fetch_assoc()['value'];

		if($_GET['c'] == 1){
		if(isset($_POST['username']) AND isset($_POST['password'])){
			if($_POST['username'] AND $_POST['password']){
				$username = $mysqli->real_escape_string($_POST['username']);
				$password = $mysqli->real_escape_string(hash("sha256", $_POST['password']));
				$mysqli->query("UPDATE faucet_settings Set value = '$username' WHERE id = '12'");
				$mysqli->query("UPDATE faucet_settings Set value = '$password' WHERE id = '13'");
				$content .= alert("success", "Username and Password was changed successfully.");
			} else if($_POST['username']){
				$content .= alert("danger", "Please fill all forms.");
			}
		}
		}

		$content .= "<form method='post' action='?p=as&c=1'>
		<div class='form-group'>
			<label>Username</label>
			<center><input class='form-control' type='text' name='username' style='width: 225px;' value='$Username' placeholder='Username ...'></center>
		</div>

		<div class='form-group'>
			<label>Password</label>
			<center><input class='form-control' type='password' name='password' style='width: 225px;' placeholder='Password ...'></center>
			<span class='help-block'>Can't be shown because it's encoded.</span>
		</div>

		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";

		$content .= "<h3>Faucet settings</h3><h4>Change Faucet name</h4>";

		$Faucetname = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '1' LIMIT 1")->fetch_assoc()['value'];

		if($_GET['c'] == 2){
			if(!$_POST['faucetname']){
				$content .= alert("danger", "Faucetname can't be blank.");
			} else {
				$Faucetname = $mysqli->real_escape_string($_POST['faucetname']);
				$mysqli->query("UPDATE faucet_settings Set value = '$Faucetname' WHERE id = '1'");
				$content .= alert("success", "Faucetname was changed successfully.");
			}
		}

		$content .= "<form method='post' action='?p=as&c=2'>
		<div class='form-group'>
			<label>Faucetname</label>
			<center><input class='form-control' type='text' name='faucetname' style='width: 225px;' value='$Faucetname' placeholder='Faucetname ...'></center>
		</div>
		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";

		$content .= "<h3>Keys settings</h3><h4>Faucethub Key</h4>";

		$faucethubkey = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '10' LIMIT 1")->fetch_assoc()['value'];

		if($_GET['c'] == 5){
			if(!$_POST['faucethubkey']){
				$content .= alert("danger", "Key can't be blank.");
			} else {
				$faucethubkey5 = $mysqli->real_escape_string($_POST['faucethubkey']);

				$mysqli->query("UPDATE faucet_settings Set value = '$faucethubkey5' WHERE id = '10'");
				$content .= alert("success", "Faucethub Key was changed successfully.");
				$faucethubkey = $faucethubkey5;
			}
		}

		$content .= "<form method='post' action='?p=as&c=5'>
		<div class='form-group'>
			<label>Faucethub Key</label>
			<center><input class='form-control' type='text' name='faucethubkey' style='width: 275px;' value='$faucethubkey' placeholder='FaucetHub Key'></center>
		</div>
		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";

		$content .= "<h4>CoinHive Keys</h4>";

		$captcha_secret_key = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '8' LIMIT 1")->fetch_assoc()['value'];
		$captcha_site_key = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '9' LIMIT 1")->fetch_assoc()['value'];

		if($_GET['c'] == 6){
			if(!$_POST['captcha_site_key'] OR !$_POST['captcha_secret_key']){
				$content .= alert("danger", "CoinHive Keys can't be blank.");
			} else {
				$captcha_secret_key5 = $mysqli->real_escape_string($_POST['captcha_secret_key']);
				$captcha_site_key5 = $mysqli->real_escape_string($_POST['captcha_site_key']);
				$mysqli->query("UPDATE faucet_settings Set value = '$captcha_secret_key5' WHERE id = '8'");
				$mysqli->query("UPDATE faucet_settings Set value = '$captcha_site_key5' WHERE id = '9'");
				$content .= alert("success", "CoinHive Keys was changed successfully.");
				$captcha_secret_key = $mysqli->real_escape_string($_POST['captcha_secret_key']);
				$captcha_site_key = $mysqli->real_escape_string($_POST['captcha_site_key']);
			}
		}

		$content .= "<form method='post' action='?p=as&c=6'>
		<div class='form-group'>
			<label>CoinHive Secret Key</label>
			<center><input class='form-control' type='text' value='".$captcha_secret_key."' name='captcha_secret_key' style='width: 375px;' value='$captcha_secret_key' placeholder='CoinHive Secret Key'></center>
		</div>
		<div class='form-group'>
			<label>CoinHive Site Key</label>
			<center><input class='form-control' type='text' value='".$captcha_site_key."' name='captcha_site_key' style='width: 375px;' value='$captcha_site_key' placeholder='CoinHive Site Key'></center>
		</div>
		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";

		$content .= "<h3>Claim settings</h3>";

		//Coinhive Profit %

		$coinhive_profit_percent = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '19' LIMIT 1")->fetch_assoc()['value'];

		if ($_GET['c']=="r") {
			if (!$_POST['profit']) {
				$content .= alert("danger", "Profit % can't be blank!");
			} else {
				if (!is_numeric($_POST['profit'])) {
					$content .= alert("danger", "Profit % must be numeric!");
				} else {
					$profitPercent = $mysqli->real_escape_string($_POST['profit']);

					$mysqli->query("UPDATE faucet_settings Set value = '$profitPercent' WHERE id = '19'");

					$content .= alert("success", "Profit % was changed successfully.");
				}
			}
		}

		$content .= "<form method='post' action='?p=as&c=r'>
		<div class='form-group'>
		<label>Profit %</label>
		<center><input class='form-control' type='number' name='profit' style='width: 225px;' value='$coinhive_profit_percent' placeholder='25'></center>
		<span class='help-block'>Enter without percent.<br />This is to increase the number of hashes per satoshi required by a certain percentage.
		</div>
		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";


		//Minimum Payout

		$min_payout = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '20' LIMIT 1")->fetch_assoc()['value'];

		if ($_GET['c']=="p") {
			if (!$_POST['min_payout']) {
				$content .= alert("danger", "Minimum Payout can't be blank!");
			} else {
				if (!is_numeric($_POST['min_payout'])) {
					$content .= alert("danger", "Minimum Payout must be numeric!");
				} else {
					$min_payout = $mysqli->real_escape_string($_POST['min_payout']);

					$mysqli->query("UPDATE faucet_settings Set value = '$min_payout' WHERE id = '20'");

					$content .= alert("success", "Minimum Payout was changed successfully.");
				}
			}
		}

		$content .= "<form method='post' action='?p=as&c=p'>
		<div class='form-group'>
		<label>Minimum Payout</label>
		<center><input class='form-control' type='number' name='min_payout' style='width: 225px;' value='$min_payout' placeholder='20'></center>
		<span class='help-block'>Minimum Payout in Satoshi</span>
		</div>
		<button type='submit' class='btn btn-primary'>Change</button>
		</form><br />";

		// Auto Withdraw

		$content .= "<h4>Auto Withdraw</h4>
		<p>Enable this feature for auto withdrawal after payout to Faucethub</p>";

		$reverseProxyStatus = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '18' LIMIT 1")->fetch_assoc()['value'];

		if($reverseProxyStatus == "yes"){
			if($_GET['auwi'] == "n"){
				$mysqli->query("UPDATE faucet_settings Set value = 'no' WHERE id = '18'");
				$content .= alert("success", "Auto Withdraw is disabled.");
				$content .= "<a href='?p=as&auwi=y' class='btn btn-default'>Enable Auto Withdraw</a>";
			} else {
				$content .= "<a href='?p=as&auwi=n' class='btn btn-default'>Disable Auto Withdraw</a>";
			}
		} else if($reverseProxyStatus == "no"){
			if($_GET['auwi'] == "y"){
				$mysqli->query("UPDATE faucet_settings Set value = 'yes' WHERE id = '18'");
				$content .= alert("success", "Auto Withdraw is enabled.");
				$content .= "<a href='?p=as&auwi=n' class='btn btn-default'>Disable Auto Withdraw</a>";
			} else {
				$content .= "<a href='?p=as&auwi=y' class='btn btn-default'>Enable Auto Withdraw</a>";
			}
		}

		break;

		case("ps"):
		$content .= "<h3>Page settings</h3><h4>Create new Page</h4>";

		if($_GET['cr'] == "y"){
			if(!$_POST['name']){
				$content .= alert("danger", "Pagename can't be blank.");
			} else {
				$name = $mysqli->real_escape_string($_POST['name']);
				$timestamp = time();
				$mysqli->query("INSERT INTO faucet_pages (name, content, timestamp_created) VALUES ('$name', '', '$timestamp')");
				$content .= alert("success", "Page was successfully created.");
			}
		}

		$content .= "<form method='post' action='?p=ps&cr=y'>
		<div class='form-group'>
			<label>Name</label>
			<center><input type='text' name='name' style='width:225px;' class='form-control' placeholder='Name ...'></center>
		</div>

		<button type='submit' class='btn btn-primary'>Add Page</button>
		</form><br /><h4>Change Pages</h4>";

		$content .= "<script type='text/javascript'>
		$('#myTabs a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		});
		</script>";

		if(isset($_GET['ch'])){
			if(!$_GET['ch'] OR !is_numeric($_GET['ch']) OR !$_POST['content']){
				$content .= alert("danger", "Please fill all forms.");
			} else {
				$pageContent = $mysqli->real_escape_string($_POST['content']);
				$pageID = $mysqli->real_escape_string($_GET['ch']);
				$mysqli->query("UPDATE faucet_pages Set content = '$pageContent' WHERE id = '$pageID'");
				$content .= alert("success", "Content was changed successfully.");
			}
		}

		if(isset($_GET['del'])){
			if(!$_GET['del'] OR !is_numeric($_GET['del'])){
				$content .= alert("danger", "Please fill all forms.");
			} else {
				$delid = $mysqli->real_escape_string($_GET['del']);
				$mysqli->query("DELETE FROM faucet_pages WHERE id = '$delid'");
				$content .= alert("success", "Page was deleted successfully.");
			}
		}

		$Navtabs = "";

		$PageNameTabs = $mysqli->query("SELECT id, name FROM faucet_pages");

		while($Tab = $PageNameTabs->fetch_assoc()){
			$Navtabs .= "<li role=\"presentation\"><a href=\"#pn".$Tab['id']."\" aria-controls=\"pn".$Tab['id']."\" role=\"tab\" data-toggle=\"tab\">".$Tab['name']."</a></li>";
		}

		$PageConf = "";

		$PageConf1 = $mysqli->query("SELECT id, name, content FROM faucet_pages");

		while($PageConf2 = $PageConf1->fetch_assoc()){
			$PageConf .= "<div role=\"tabpanel\" class=\"tab-pane\"  id=\"pn".$PageConf2['id']."\">
			<form method='post' action='?p=ps&ch=".$PageConf2['id']."'>
			<textarea class='form-control' cols='65' rows='10' name='content'>".$PageConf2['content']."</textarea><br />
			<button type='submit' class='btn btn-success btn-lg'>Change</button>
			</form><br />
			<hr />
			<a href='?p=ps&del=".$PageConf2['id']."' class='btn btn-danger'>Delete Page</a>
			</div>";
		}



		$content .= "

		<div>

  <!-- Nav tabs -->
  <ul class=\"nav nav-tabs\" role=\"tablist\">
    $Navtabs
  </ul><br />

  <!-- Tab panes -->
  <div class=\"tab-content\">
    $PageConf
  </div>

</div>";
	break;

	case("ads"):

	$content .= "<a href='admin.php'>Back</a><br>
	<h3>Admin Settings</h3><h4>Advertising settings</h4>";

	$content .= "<h3>Space top</h4>";

	$Spacetop = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '2' LIMIT 1")->fetch_assoc()['value'];

	if($_GET['c'] == 1){
		if(!isset($_POST['spacetop'])){
			$content .= alert("danger", "Error.");
		} else {
			$Spacetop = $mysqli->real_escape_string($_POST['spacetop']);
			$mysqli->query("UPDATE faucet_settings Set value = '$Spacetop' WHERE id = '2'");
			$content .= alert("success", "HTML Code 'Space top' changed successfully.");
			$Spacetop = $_POST['spacetop'];
		}
	}

	$content .= "<form method='post' action='?p=ads&c=1'>
	<textarea class='form-control' cols='65' rows='10' name='spacetop'>".$Spacetop."</textarea><br />
	<button type='submit' class='btn btn-success btn-lg'>Change</button>
	</form><br />";

	$content .= "<h3>Space left</h4>";

	$Spaceleft = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '3' LIMIT 1")->fetch_assoc()['value'];

	if($_GET['c'] == 2){
		if(!isset($_POST['spaceleft'])){
			$content .= alert("danger", "Error.");
		} else {
			$Spaceleft = $mysqli->real_escape_string($_POST['spaceleft']);
			$mysqli->query("UPDATE faucet_settings Set value = '$Spaceleft' WHERE id = '3'");
			$content .= alert("success", "HTML Code 'Space left' changed successfully.");
			$Spaceleft = $_POST['spaceleft'];
		}
	}

	$content .= "<form method='post' action='?p=ads&c=2'>
	<textarea class='form-control' cols='65' rows='10' name='spaceleft'>".$Spaceleft."</textarea><br />
	<button type='submit' class='btn btn-success btn-lg'>Change</button>
	</form><br />";

	$content .= "<h3>Space right</h4>";

	$Spaceright = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '4' LIMIT 1")->fetch_assoc()['value'];

	if($_GET['c'] == 3){
		if(!isset($_POST['spaceright'])){
			$content .= alert("danger", "Error.");
		} else {
			$Spaceright = $mysqli->real_escape_string($_POST['spaceright']);
			$mysqli->query("UPDATE faucet_settings Set value = '$Spaceright' WHERE id = '4'");
			$content .= alert("success", "HTML Code 'Space right' changed successfully.");
			$Spaceright = $_POST['spaceright'];
		}
	}

	$content .= "<form method='post' action='?p=ads&c=3'>
	<textarea class='form-control' cols='65' rows='10' name='spaceright'>".$Spaceright."</textarea><br />
	<button type='submit' class='btn btn-success btn-lg'>Change</button>
	</form><br />";

	break;
	}

} else {
	$content .= "<h3>Log In</h3>";

	if(isset($_POST['username']) AND isset($_POST['password'])){
		if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
		unset($_SESSION['token']);
		$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
		exit;
		}
		unset($_SESSION['token']);
		$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));

		if($_POST['username'] AND $_POST['password']){
			$username = $mysqli->real_escape_string($_POST['username']);
			$password = $mysqli->real_escape_string(hash("sha256", $_POST['password']));

			$UserDB = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '12' LIMIT 1")->fetch_assoc()['value'];
			$PwDB = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '13' LIMIT 1")->fetch_assoc()['value'];
			$loginAttempt = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '17' LIMIT 1")->fetch_assoc()['value'];

			$lastLoginSecond = time() - $loginAttempt;

			$mysqli->query("UPDATE faucet_settings Set value = '".time()."' WHERE id = '17'");

			if($lastLoginSecond < 4){
				$content .= alert("danger", "You're trying to log in very fast.");
			} else {
				if($UserDB == $username){
					if($PwDB == $password){
						$_SESSION['admin'] = "Admin_".$username."_Password_".$password;
						header("Location: admin.php");
						exit;
					} else {
						$content .= alert("danger", "Password is wrong.");
					}
				} else {
					$content .= alert("danger", "Username is wrong.");
				}
			}
		} else if($_POST['username']){
			$content .= alert("Please fill all fields.");
		}
	}

	$content .= "
	<form method='post' action='?'>
	<div class='form-group'>
		<label>Username</label>
		<center><input class='form-control' type='text' name='username' style='width: 225px;' placeholder='Username ...'></center>
	</div>

	<div class='form-group'>
		<label>Password</label>
		<center><input class='form-control' type='password' name='password' style='width: 225px;' placeholder='Password ...'></center>
	</div>
	<input type='hidden' name='token' value='".$_SESSION['token']."'/>
	<button type='submit' class='btn btn-primary'>Log In</button>
	</form>";
}

$tpl->assign("content", $content);
$tpl->display();
?>
