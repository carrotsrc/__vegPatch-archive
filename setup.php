<?php
	include_once("system/file/filemanager.php");
	include_once("system/db/db.php");
	global $config;

	$config = array(
				'posted' => false,
				'complete' => false,

				'dbcred' => false,
				'dbname' => false,
				'dbcheck' => false,
				
				'rootuser' => false,
				'rootpass' => false,
				'rootcheck' => false,

				'clean' => false,

				'sub' => ""
			);
	function config_testdb()
	{
		global $config;
		$db = core_create_db('mysql');
		if(!@$db->connect($_POST['dbuser'], $_POST['dbpass'])) {
			$_POST['dbpass'] = "";
			return false;
		}

		$config['dbcred'] = true;

		if(!$db->selectDatabase($_POST['dbname'])) {
			$_POST['dbname'] = "";
			return false;
		}

		$config['dbname'] = true;
		$config['dbcheck'] = true;
		return true;
	}

	function config_root()
	{
		global $config;
		if($_POST['rtuser'] == "") {
			$_POST['rtpass'] = "";
			$_POST['rtcheck'] = "";
			return false;

		}


		$config['rootuser'] = true;
		if($_POST['rtpass'] == "")
			return false;

		$config['rootpass'] = true;

		if($_POST['rtpass'] != $_POST['rtcheck']) {
			$_POST['rtpass'] = "";
			$_POST['rtcheck'] = "";
			return false;
		}

		$config['rootcheck'] = true;
		return true;
	}

	function config_docroot()
	{
		global $config;
		$droot = $_SERVER['DOCUMENT_ROOT'];
		$sdir = dirname(__FILE__);
		if($sdir == $droot)
			return;

		$drlen = strlen($droot);
		$slen = strlen($sdir);
		$config['sub'] = substr($sdir, $drlen, $slen-$drlen);
	}

	function config_dispatch()
	{
		global $config;
		$fm = new FileManager();
		$file = $fm->openFile("system/_ksysconfig.php", "r");
		$content = $file->read();
		$file->close();
		$org = array("\$dbcUsername = ''","\$dbcPassword = ''","\$dbcDatabase = ''","\$rootUser = ''","\$rootPass = ''", "\$appdir = ''");
		$rep = array(
			"\$dbcUsername = '{$_POST['dbuser']}'",
			"\$dbcPassword = '{$_POST['dbpass']}'", 
			"\$dbcDatabase = '{$_POST['dbname']}'",
			"\$rootUser = '{$_POST['rtuser']}'",
			"\$rootPass = '{$_POST['rtpass']}'",
			"\$appdir = '{$config['sub']}'"
			);
		$content = str_replace($org, $rep, $content);
		$file = $fm->newFile($_SERVER['DOCUMENT_ROOT']."/ksysconfig.php", "w");
		$file->write($content);
		$file->close();
	}

	function config_clean()
	{
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/ksysconfig.php"))
			return false;

		return true;
	}

	if(sizeof($_POST) > 0) {
		if(config_clean()) {
			$config['posted'] = true;
			$config['complete'] = true;

			if(!config_testdb())
				$config['complete'] = false;

			if(!config_root())
				$config['complete'] = false;

			
			if($config['complete']) {
				config_docroot();
				config_dispatch();
			}
			$config['clean'] = true;
		} else {
			$_POST['dbuser'] = "";
			$_POST['dbpass'] = "";
			$_POST['dbname'] = "";
			$_POST['rtuser'] = "";
			$_POST['rtpass'] = "";
			$_POST['rtcheck'] = "";
		}
	}
	else
	{
		$config['clean'] = config_clean();
		$_POST['dbuser'] = "";
		$_POST['dbpass'] = "";
		$_POST['dbname'] = "";
		$_POST['rtuser'] = "";
		$_POST['rtpass'] = "";
		$_POST['rtcheck'] = "";
	}
?>

<html>
<head>
	<title>vegPatch Setup</title>
	<link type="text/css" rel="stylesheet" href="dev/tools/template.css" />
	<style>
		.setup-block {
			padding: 15px;
			height: auto !important;
			color: #808080;
			overflow: auto !important;
		}

		.group-block {
			margin-bottom: 30px;
			overflow: auto !important;
		}

		.group-state {
			font-weight: bold;
			margin-top: 15px;
		}

		.state-success {
			color: #83A210;
			font-size: 25px;
			text-align: center;
		}

		.state-fail {
			color: #EF654A;
		}

		.group-title {
			font-weight: bold;
			color: #83A210;
		}

		.info-block {
			font-size: 15px;
			border: 2px dashed #d8d8d8;
			padding: 5px;
			margin-left: 15px;
			margin-right: 15px;
		}

		.subgroup-block {
			overflow: auto;
		}

		.form-block {
			margin-left: 15px;
			margin-right: 15px;
			margin-top: 25px;
			line-height: 8px;
		}

		.floater-block {
			float: left;
		}

		.spacer {
			margin-top: 10px;
		}
		a {
			color: #599EEF;
			text-decoration: none;
		}
	</style>
</head>
</html>
<body>
<div id="header">
	<a href="init.php"><span class="ok-text-grey tx-xlarge">veg<span class="ok-text-green">Patch</span></span></a>
</div>

<div id="kr-layout-column" class="setup-block">
<form action="setup.php" method="post">
<?php
	if(!$config['clean']) {
		echo "<div class=\"state-fail tx-bold tx-large\">\n";
			echo "Platform already configured!";
		echo "</div>";

	}
?>
	<div class="group-block">
		<span class="group-title">Database & User</span>
		<div class="info-block spacer">
			Here you setup database access.<br >
			The credentials should be for a database user you have already created for handling the platform database operations.<br />
			The database should be a <i>blank</i> database created for use by the platform.
		</div>
		
		<div class="subgroup-block">
			<div class="floater-block">
				<div class="form-block">
					<b>Account Username</b><br />
					<input type="text" class="form-text tx-large" autocomplete="OFF" name="dbuser" value="<?php echo $_POST['dbuser']; ?>" />
				</div>
				<div class="form-block">
					<b>Account Password</b><br />
					<input type="password" class="form-text tx-large" name="dbpass" autocomplete="OFF" value="<?php echo $_POST['dbpass']; ?>" />
				</div>
			</div>

			<div class="form-block">
				<b>Platform Database</b><br />
				<input type="text" class="form-text tx-large" name="dbname" autocomplete="OFF" value="<?php echo $_POST['dbname']; ?>" />
			</div>
		</div>
<?php
	if($config['posted']) {
		if($config['dbcheck']) {
			echo "<div class=\"group-state state-success\">Success!</div>\n";

		}
		else {
			echo "<div class=\"group-state state-fail\">\n<ul>";
			if(!$config['dbcred'])
				echo "<li>Database account username or password is incorrect</li>";
			else
			if(!$config['dbname'])
				echo "<li>Could not access database</li>";
			echo "</ul>\n</div>";

		}
	}
?>
	</div>

	<div class="group-block">
		<span class="group-title">Platform Root User</span>
		<div class="info-block spacer">
			This configures the login credentials for the root user. This is the user who will have access to the root tools and who will be able to continue the setup.
		</div>
		
		<div class="subgroup-block">
			<div class="form-block">
				<b>Root User</b><br />
				<input type="text" class="form-text tx-large" autocomplete="OFF" name="rtuser" value="<?php echo $_POST['rtuser']; ?>"/>
			</div>

			<div class="subgroup-block">
				<div class="form-block floater-block">
					<b>Root Password</b><br />
					<input type="password" class="form-text tx-large" name="rtpass" autocomplete="OFF" value="<?php echo $_POST['rtpass']; ?>" />
				</div>

				<div class="form-block">
					<b>Retype Password</b><br />
					<input type="password" class="form-text tx-large" name="rtcheck" autocomplete="OFF" value="<?php echo $_POST['rtcheck']; ?>" />
				</div>
			</div>
		</div>
<?php
	if($config['posted']) {
		if($config['rootcheck']) {
			echo "<div class=\"group-state state-success\">Success!</div>\n";

		}
		else {
			echo "<div class=\"group-state state-fail\">\n<ul>";
			if(!$config['rootuser'])
				echo "<li>You must specify a root username</li>";

			if(!$config['rootpass'])
				echo "<li>You must specify a password</li>";
			else
				echo "<li>Password fields do not match</li>";
			echo "</ul>\n</div>";

		}
	}
?>
	</div>
	<div class="group-block">
		<span class="group-title">Finished</span>
		<div class="info-block spacer">
			The setup script should generate the file <i>ksysconfig.php</i> in the document root.<br />
			Once this has been performed successfully, you will need to go to the <a href="dev/tools">root tools</a> and login as the <b>root user</b> to continue setting up the platform.<br />
			<span class="state-fail tx-bold">Be sure to delete this script (setup.php) once you are finished with it</span>
		</div>
		<?php
		if(!$config['complete'])
			echo "<center><input type=\"submit\" class=\"form-button tx-large spacer\" value=\"Configure\" /></center>";
		else
			echo "<div class=\"group-state state-success\">Configured!</div>\n";

		?>
	</div>
</form>
</div>

</body>
</html>
