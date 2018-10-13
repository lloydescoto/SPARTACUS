<?php
	session_start();
	
	$server = "localhost";
	$dbUsername = "root";
	$dbPassword = "";
	$database = "gymdatabase";

	$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
	
	$link = $_SERVER['PHP_SELF'];
	$link_array = explode('/',$link);
	$pageName = end($link_array);
	
	if(isset($_SESSION['username']) && $pageName == "index.php")
	{
		header("Location: http://".$_SERVER['HTTP_HOST']."/home.php");
		exit();
	}

	if ($conn->connect_error) {
		die("Connect Failed:" . $conn->connect_error);
	}
	
	if(isset($_POST['loginInButton']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		$proPassword = hash('sha256', $password);
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ? AND Password = ?");
		$stmt->bind_param("ss",$username,$proPassword);
		$stmt->execute();
		$stmt->bind_result($id, $user, $pass, $type, $status);
		$stmt->fetch();
		if($user == $username && $pass == $proPassword && $type == "Admin") 
		{	
			if($status == NULL)
			{
				$_SESSION['username'] = $user;
				$_SESSION['id'] = $id;
				header("Location: home.php");
			}
		}
		else if($user == $username && $pass == $proPassword && $type == "Assistant Admin")
		{
			if($status == "Active")
			{
				$_SESSION['username'] = $user;
				$_SESSION['id'] = $id;
				header("Location: home.php");
			}
			else
			{
				$errMsg = "Account is Deactivate";
			}
		}
		else if($user == $username && $pass == $proPassword && $type == "Instructor")
		{
			if($status == "Active")
			{
				$_SESSION['username'] = $user;
				$_SESSION['id'] = $id;
				header("Location: home.php");
			}
			else
			{
				$errMsg = "Account is Deactivate";
			}
		}
		else if($user == $username && $pass == $proPassword && $type == "User")
		{
			if($status == "Active")
			{
				$_SESSION['username'] = $user;
				$_SESSION['id'] = $id;
				header("Location: home.php");
			}
			else
			{
				$errMsg = "Account is Deactivate";
			}
		}
		else
		{
			$errMsg = "Invalid Username and Password";
		}
		$stmt->close();
	}
	$conn->close();
?>
<!doctype html>
<html>
	<head>
	<title>Spartacus Gym</title>
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="main.js"></script>
	</head>
	<body>
	<div id = "loginForm" class = "overlay">
	<a href="javascript:void(0)" class="closebtn" onclick="closeLogin()"><i class = "fa fa-close"></i></a>
	<div class = "overlay-content">
	<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="username" placeholder="Username" required/>
			<input type="password" name="password" placeholder="Password" required/>
			<input name="loginInButton" type="submit" value="Login" />
			</form>
			</div>
	</div>
	</div>
	<center>
	<br>
	<p id="size" class="glow"><i id="isize" class="fa fa-themeisle glow"></i> S P A R T A C U S</p>
	<div>
	<a class = "uibutton" onclick = "openLogin()">Login</a>
	</div>
	</center>
	</body>
</html>