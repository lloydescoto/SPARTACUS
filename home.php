<?php
	session_start();
	
	$server = "localhost";
	$dbUsername = "root";
	$dbPassword = "";
	$database = "gymdatabase";

	$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
	
	if ($conn->connect_error) {
		die("Connect Failed:" . $conn->connect_error);
	}


	if(!isset($_SESSION['username'])) {
	 header("Location: index.php");
	}
	
	if(isset($_SESSION['username']))
	{
	$id_check = $_SESSION['id'];
	$stmt = $conn->prepare("SELECT * FROM accounts WHERE ID = ?");
	$stmt->bind_param("i",$id_check);
	$stmt->execute();
	$stmt->bind_result($id, $user, $pass, $type, $status);
	$stmt->fetch();
	$stmt->close();
		if($type == "Admin")
		{
			$stmt2 = $conn->prepare("SELECT * FROM admin WHERE ID = ?");
			$stmt2->bind_param("i",$id);
			$stmt2->execute();
			$stmt2->bind_result($Aid, $fname, $lname, $Fid);
			$stmt2->fetch();
			$stmt2->close();
		}
		else if($type == "Assistant Admin")
		{
			$stmt3 = $conn->prepare("SELECT * FROM assistadmin WHERE ID = ?");
			$stmt3->bind_param("i",$id);
			$stmt3->execute();
			$stmt3->bind_result($Assiid, $fname, $lname, $email, $age, $address, $gender, $contact, $Fid);
			$stmt3->fetch();
			$stmt3->close();
		}
		else if($type == "Instructor")
		{
			$stmt4 = $conn->prepare("SELECT * FROM gyminstructor WHERE ID = ?");
			$stmt4->bind_param("i",$id);
			$stmt4->execute();
			$stmt4->bind_result($Insid, $fname, $lname, $email, $age, $address, $gender, $contact, $Fid);
			$stmt4->fetch();
			$stmt4->close();
		}
		else if($type == "User")
		{
			$stmt4 = $conn->prepare("SELECT * FROM userdata WHERE ID = ?");
			$stmt4->bind_param("i",$id);
			$stmt4->execute();
			$stmt4->bind_result($Userid, $fname, $lname, $email, $age, $address, $gender, $contact, $Fid, $FInsid);
			$stmt4->fetch();
			$stmt4->close();
		}
	}
	
	if(isset($_POST['addUserButton']))
	{
		$status = "Active";
		$acctype = "User";
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		$firstname = $_POST['firstName'];
		$lastname = $_POST['lastName'];
		$age = $_POST['age'];
		$gender = $_POST['gender'];
		$address = $_POST['address'];
		$contact = $_POST['contact'];
		$membership = $_POST['membership'];
		$date = date("Y/m/d");
		if($membership == 7)
		{
			$expiration = date('Y/m/d',strtotime($date . "+7 days"));
			$fee = 100;
		}
		else if($membership == 30)
		{
			$expiration = date('Y/m/d',strtotime($date . "+30 days"));
			$fee = 400;
		}
		else
		{
			$expiration = date('Y/m/d',strtotime($date . "+365 days"));
			$fee = 1400;
		}
		$proPassword = hash('sha256', $password);
		$stmt = $conn->prepare("INSERT INTO accounts(Username, Password, AccType, AccStatus) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss",$username,$proPassword,$acctype,$status);
		if($stmt->execute())
		{
			$stmt->close();
			$stmt2 = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
			$stmt2->bind_param("s",$username);
			$stmt2->execute();
			$stmt2->bind_result($Uid, $Uuser, $Upass, $Utype, $Ustatus);
			$stmt2->fetch();
			$stmt2->close();
			$stmt3 = $conn->prepare("INSERT INTO userdata(UserFName, UserLName, UserEmail, UserAge, UserAddress, UserGender, UserContact, ID) VALUES (?,?,?,?,?,?,?,?)");
			$stmt3->bind_param("sssissii",$firstname,$lastname,$email,$age,$address,$gender,$contact,$Uid);
			if($stmt3->execute())
			{
				$stmt3->close();
				$stmt4 = $conn->prepare("SELECT * FROM userdata WHERE ID = ?");
				$stmt4->bind_param("i",$Uid);
				if($stmt4->execute())
				{
				$stmt4->bind_result($Userid, $Userfname, $Userlname, $Useremail, $Userage, $Useraddress, $Usergender, $Usercontact, $Uid, $Userinstructor);
				$stmt4->fetch();
				$stmt4->close();
				$stmt5 = $conn->prepare("INSERT INTO usermembership(UserID, RegDate, ExpDate, MembershipDays) VALUES (?,?,?,?)");
				$stmt5->bind_param("issi",$Userid,$date,$expiration,$membership);
					if($stmt5->execute())
					{
						$stmt5->close();
						$stmt6 = $conn->prepare("INSERT INTO userbalance(UserID, UserBalance) VALUES (?,?)");
						$stmt6->bind_param("ii",$Userid,$fee);
						$stmt6->execute();
						$stmt6->close();
						$chkMsg = "Successfully Work";
					}
					else 
					{
						$errMsg = "Error at Registering Balance";
					}
				}
				else 
				{
					$errMsg = "Error at Registering Membership";
				}
			}
			else 
			{
				$errMsg = "Error at Registering Data ";
			}
		}
		else 
		{
			$errMsg = "Error at Registering Accounts";
		}
	}
	
	if(isset($_POST['addInstructorButton']))
	{
		$status = "Active";
		$acctype = "Instructor";
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		$firstname = $_POST['firstName'];
		$lastname = $_POST['lastName'];
		$age = $_POST['age'];
		$gender = $_POST['gender'];
		$address = $_POST['address'];
		$contact = $_POST['contact'];
		$proPassword = hash('sha256', $password);
		$stmt = $conn->prepare("INSERT INTO accounts(Username, Password, AccType, AccStatus) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss",$username,$proPassword,$acctype,$status);
			if($stmt->execute())
			{
				$stmt->close();
				$stmt2 = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
				$stmt2->bind_param("s",$username);
				if($stmt2->execute())
				{
					$stmt2->bind_result($Iid, $Iuser, $Ipass, $Itype, $Istatus);
					$stmt2->fetch();
					$stmt2->close();
					$stmt3 = $conn->prepare("INSERT INTO gyminstructor(InsFName, InsLName, InsEmail, InsAge, InsAddress, InsGender, InsContact, ID) VALUES (?,?,?,?,?,?,?,?)");
					$stmt3->bind_param("sssissii",$firstname,$lastname,$email,$age,$address,$gender,$contact,$Iid);
					$stmt3->execute();
					$stmt3->close();
					$chkMsg = "Successfully Work";
				}
				else
				{
					$errMsg = "Error at Registering Instructor";
				}
			}
			else
			{
				$errMsg = "Error at Registering Accounts";
			}
	}
	
	if(isset($_POST['addAssistButton']))
	{
		$status = "Active";
		$acctype = "Assistant Admin";
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		$firstname = $_POST['firstName'];
		$lastname = $_POST['lastName'];
		$age = $_POST['age'];
		$gender = $_POST['gender'];
		$address = $_POST['address'];
		$contact = $_POST['contact'];
		$proPassword = hash('sha256', $password);
		$stmt = $conn->prepare("INSERT INTO accounts(Username, Password, AccType, AccStatus) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss",$username,$proPassword,$acctype,$status);
			if($stmt->execute())
			{
				$stmt->close();
				$stmt2 = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
				$stmt2->bind_param("s",$username);
				if($stmt2->execute())
				{
					$stmt2->bind_result($AAid, $AAuser, $AApass, $AAtype, $AAstatus);
					$stmt2->fetch();
					$stmt2->close();
					$stmt3 = $conn->prepare("INSERT INTO assistadmin(AssistFName, AssistLName, AssistEmail, AssistAge, AssistAddress, AssistGender, AssistContact, ID) VALUES (?,?,?,?,?,?,?,?)");
					$stmt3->bind_param("sssissii",$firstname,$lastname,$email,$age,$address,$gender,$contact,$AAid);
					$stmt3->execute();
					$stmt3->close();
					$chkMsg = "Successfully Work";
				}
				else
				{
					$errMsg = "Error at Registering Assistant";
				}
			}
			else
			{
				$errMsg = "Error at Registering Accounts";
			}
	}
	
	if(isset($_POST['addAdminButton']))
	{
		$acctype = "Admin";
		$username = $_POST['username'];
		$password = $_POST['password'];
		$firstname = $_POST['firstName'];
		$lastname = $_POST['lastName'];
		$proPassword = hash('sha256', $password);
		$stmt = $conn->prepare("INSERT INTO accounts(Username, Password, AccType) VALUES (?,?,?)");
		$stmt->bind_param("sss",$username,$proPassword,$acctype);
			if($stmt->execute())
			{
				$stmt->close();
				$stmt2 = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
				$stmt2->bind_param("s",$username);
				if($stmt2->execute())
				{
					$stmt2->bind_result($Aid, $Auser, $Apass, $Atype, $Astatus);
					$stmt2->fetch();
					$stmt2->close();
					$stmt3 = $conn->prepare("INSERT INTO admin(AdminFName,AdminLName,ID) VALUE (?,?,?)");
					$stmt3->bind_param("ssi",$firstname,$lastname,$Aid);
					$stmt3->execute();
					$stmt3->close();
					$chkMsg = "Successfully Work";
				}
				else
				{
					$errMsg = "Error at Registering Admin";
				}
			}
			else
			{
				$errMsg = "Error at Registering Accounts";
			}
	}
	
	if(isset($_POST['deactivateUsersButton']))
	{
		$deactivate = "Deactivate";
		$id = $_POST['id'];
		$username = $_POST['username'];
		$stmt = $conn->prepare("UPDATE accounts SET AccStatus = ? WHERE ID = ? && Username = ?");
		$stmt->bind_param("sis",$deactivate,$id,$username);
		if($stmt->execute())
		{
			$stmt->close();
			$chkMsg = "Successfully Work";
		}
		else
		{
			$errMsg = "Error at Deactivating Users";
		}
	}
	
	if(isset($_POST['removeAssistButton']))
	{
		$acctype = "Assistant Admin";
		$id = $_POST['id'];
		$username = $_POST['username'];
		$stmt = $conn->prepare("DELETE FROM assistadmin WHERE ID = ?");
		$stmt->bind_param("i",$id);
		if($stmt->execute())
		{
			$stmt->close();
			$stmt2 = $conn->prepare("DELETE FROM accounts WHERE ID = ? AND Username = ? AND AccType = ?");
			$stmt2->bind_param("iss",$id,$username,$acctype);
			if($stmt2->execute())
			{
				$stmt2->close();
				$chkMsg = "Successfully Work";
			}
			else 
			{
				$errMsg = "Error at Deleting Assistant Admin Account";
			}
		}
		else 
		{
			$errMsg = "Error at Deleting Assistant Admin";
		}
	}
	
	if(isset($_POST['activateUsersButton']))
	{
		$activate = "Active";
		$id = $_POST['id'];
		$username = $_POST['username'];
		$stmt = $conn->prepare("UPDATE accounts SET AccStatus = ? WHERE ID = ? && Username = ?");
		$stmt->bind_param("sis",$activate,$id,$username);
		if($stmt->execute())
		{
			$stmt->close();
			$chkMsg = "Successfully Work";
		}
		else
		{
			$errMsg = "Error at Deactivating Users";
		}
	}
	
	if(isset($_POST['extendUsersButton']))
	{
		$id = $_POST['id'];
		$membership = $_POST['membership'];
		$stmt = $conn->prepare("SELECT * FROM usermembership WHERE UserID = ?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->bind_result($Userid,$date,$expiration,$membership);
		$stmt->fetch();
		$stmt->close();
			if($membership == 7)
			{
				$newExpiration = date('Y/m/d',strtotime($expiration . "+7 days"));
				$newMembership = $membership + 7;
			}
			else if($membership == 30)
			{
				$newExpiration = date('Y/m/d',strtotime($expiration . "+30 days"));
				$newMembership = $membership + 30;
			}
			else
			{
				$newExpiration = date('Y/m/d',strtotime($expiration . "+365 days"));
				$newMembership = $membership + 365;
			}
			$stmt2 = $conn->prepare("UPDATE usermembership SET ExpDate = ?,MembershipDays = ? WHERE UserID = ?");
			$stmt2->bind_param("sii",$newExpiration,$newMembership,$id);
			if($stmt2->execute())
			{
				$stmt2->close();
				$chkMsg = "Successfully Work";
			}
			else
			{
				$errMsg = "Error at Extending";
			}
	}
	
	if(isset($_POST['instructorUsersButton']))
	{
		$user_check = $_SESSION['username'];
		$instructor = $_POST['instructor'];
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
		$stmt->bind_param("s",$user_check);
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			$accountdata = $result->fetch_array(MYSQLI_ASSOC);
			$stmt->close();
			$stmt2 = $conn->prepare("SELECT * FROM userdata WHERE ID = ?");
			$stmt2->bind_param("i",$accountdata['ID']);
			if($stmt2->execute())
			{	
				$result2 = $stmt2->get_result();
				$userdata = $result2->fetch_array(MYSQLI_ASSOC);
				$stmt2->close();
				$stmt3 = $conn->prepare("UPDATE userdata SET InstructorID = ? WHERE UserID = ? AND UserFName = ?");
				$stmt3->bind_param("iis",$instructor,$userdata['UserID'],$userdata['UserFName']);
				if($stmt3->execute())
				{
					$stmt3->close();
					$stmt4 = $conn->prepare("INSERT INTO instructorusers(UserID,InstructorID) VALUES (?,?)");
					$stmt4->bind_param("ii",$userdata['UserID'],$instructor);
					if($stmt4->execute())
					{
						$stmt4->close();
						$stmt5 = $conn->prepare("SELECT * FROM userbalance WHERE UserID = ?");
						$stmt5->bind_param("i",$userdata['UserID']);
						if($stmt5->execute())
						{
							$result3 = $stmt5->get_result();
							$balancedata = $result3->fetch_array(MYSQLI_ASSOC);
							$newbalance = $balancedata['UserBalance'] + 1000;
							$stmt5->close();
							$stmt6 = $conn->prepare("UPDATE userbalance SET UserBalance = ? WHERE UserID = ?");
							$stmt6->bind_param("ii",$newbalance,$userdata['UserID']);
							if($stmt6->execute())
							{
								$stmt6->close();
								$chkMsg = "Successfully Work";
							}
							else
							{
								$errMsg = "Failed to get Update";
							}
						}
						else 
						{
							$errMsg = "Failed to get Add";
						}
					}
					else
					{
						$errMsg = "Failed to get Add";
					}
				}
				else
				{
					$errMsg = "Failed to get Instructor";
				}
			}
			else
			{
				$errMsg = "Failed User Data";
			}
		}
	}
	
	if(isset($_POST['changePasswordButton']))
	{
		$user_check = $_SESSION['username'];
		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];
		$renewpassword = $_POST['renewpassword'];
		$proPassword = hash('sha256', $oldpassword);
		$proNewPassword = hash('sha256', $renewpassword);
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ? AND Password = ?");
		$stmt->bind_param("ss",$user_check,$proPassword);
		if($stmt->execute())
		{
			if($newpassword == $renewpassword)
			{
				$result = $stmt->get_result();
				$accountdata = $result->fetch_array(MYSQLI_ASSOC);
				$stmt->close();
				$stmt2 = $conn->prepare("UPDATE accounts SET Password = ? WHERE Username = ? AND Password = ?");
				$stmt2->bind_param("sss",$proNewPassword,$accountdata['Username'],$accountdata['Password']);
				if($stmt2->execute())
				{
					$stmt2->close();
					header("Location: logout.php");
				}
			}
			else
			{
				$errMsg = "";
			}
		}
		else
		{
			$errMsg = "";
		}
		
	}
	$conn->close();
?>
<!doctype html>
<html>
	<head>
	<title>Spartacus Gym</title>
	<link rel="icon" type="image/png" href="dumbbell.png">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="exercise/font/flaticon.css"> 
	<script src="http://maps.googleapis.com/maps/api/js"></script>
	<script src="main.js"></script>
	</head>
	<body>
	<div id = "ProfileForm" class = "overlay">
	<a href="javascript:void(0)" class="closebtn" onclick="closeProfile()"><i class = "fa fa-close"></i></a>
	<div class = "overlay-content">
	<div class="form-style-6">
			<center>
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<table>
			<tr>
			<td id="tdborder"><p>Username:</p></td><td id="tdborder"><p><?php echo $user ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Email:</p></td><td id="tdborder"><p> <?php echo $email ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>First Name:</p></td><td id="tdborder"><p> <?php echo $fname ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Last Name:</p></td><td id="tdborder"><p> <?php echo $lname ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Age:</p></td><td id="tdborder"><p> <?php echo $age ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Address:</p></td><td id="tdborder"><p> <?php echo $address ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Gender:</p></td><td id="tdborder"><p> <?php echo $gender ?></p></td>
			</tr>
			<tr>
			<td id="tdborder"><p>Contact:</p></td><td id="tdborder"><p> <?php echo $contact ?></p></td>
			</tr>
			</table>
			</center>
			</div>
	</div>
	</div>
	<div id="addUser" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAddUser()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="username" placeholder="Username" required/>
			<input type="password" name="password" placeholder="Password" required/>
			<input type="email" name="email" placeholder="Email Address" required/>
			<input type="text" name="firstName" placeholder="First Name " required/>
			<input type="text" name="lastName" placeholder="Last Name" required/>
			<input type="text" maxlength="2" name="age" placeholder="Age" required/>
			<input type="text" name="address" placeholder="Address" required/><br>
			<input type="text" maxlength="11" name="contact" placeholder="Contact No." required/>
			<br>
			<br>
			<h1>Gender</h1>
			<input type="radio" name="gender" value="Male" checked>Male
			<input type="radio" name="gender" value="Female">Female
			<br>
			<br>
			<br>
			<h1>Membership</h1>
			<input type="radio" name="membership" value="7" checked> 1 Week
			<input type="radio" name="membership" value="30"> 1 Month
			<input type="radio" name="membership" value="365"> 1 Year<br>
			<br>
			<input name="addUserButton" type="submit" value="Register" />
			</form>
			</div>
		</div>
	</div>
	<div id="addInstructor" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAddInstructor()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="username" placeholder="Username" required/>
			<input type="password" name="password" placeholder="Password" required/>
			<input type="email" name="email" placeholder="Email Address" required/>
			<input type="text" name="firstName" placeholder="First Name " required/>
			<input type="text" name="lastName" placeholder="Last Name" required/>
			<input type="text" maxlength="2" name="age" placeholder="Age" required/>
			<input type="text" name="address" placeholder="Address" required/><br>
			<input type="text" maxlength="11" name="contact" placeholder="Contact No." required/>
			<br>
			<br>
			<h1>Gender</h1>
			<input type="radio" name="gender" value="Male" checked>Male
			<input type="radio" name="gender" value="Female">Female
			<br>
			<br>
			<input name="addInstructorButton" type="submit" value="Register" />
			</form>
			</div>
		</div>
	</div>
	<div id="addAssist" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAddAssist()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="username" placeholder="Username" required/>
			<input type="password" name="password" placeholder="Password" required/>
			<input type="email" name="email" placeholder="Email Address" required/>
			<input type="text" name="firstName" placeholder="First Name " required/>
			<input type="text" name="lastName" placeholder="Last Name" required/>
			<input type="text" maxlength="2" name="age" placeholder="Age" required/>
			<input type="text" name="address" placeholder="Address" required/><br>
			<input type="text" maxlength="11" name="contact" placeholder="Contact No." required/>
			<br>
			<br>
			<h1>Gender</h1>
			<input type="radio" name="gender" value="Male" checked>Male
			<input type="radio" name="gender" value="Female">Female
			<br>
			<br>
			<input name="addAssistButton" type="submit" value="Register" />
			</form>
			</div>
		</div>
	</div>
	<div id="addAdmin" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAddAdmin()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="username" placeholder="Username" required/>
			<input type="password" name="password" placeholder="Password" required/>
			<input type="text" name="firstName" placeholder="First Name " required/>
			<input type="text" name="lastName" placeholder="Last Name" required/>
			<input name="addAdminButton" type="submit" value="Register" />
			</form>
			</div>
		</div>
	</div>
	<div id = "expiredForm" class = "overlay">
	<a href="javascript:void(0)" class="closebtn" onclick="closeExpiredAccounts()"><i class = "fa fa-close"></i></a>
	<div class = "overlay-content">
	<div class="form-style-6">
		<h1><i class="fa fa-themeisle size2 glow"></i></h1>
		<center>
		<table>
		<?php
	
			$server = "localhost";
			$dbUsername = "root";
			$dbPassword = "";
			$database = "gymdatabase";

			$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
			
			if ($conn->connect_error) {
				die("Connect Failed:" . $conn->connect_error);
			}
			
			$stmt = $conn->prepare("SELECT * FROM usermembership");
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$curdate = date("Y-m-d");
				$UserExpDate = $row['ExpDate'];
				if($UserExpDate <= $curdate)
				{	
					$Userid = $row['UserID'];
					$stmt2 = $conn->prepare("SELECT * FROM userdata WHERE UserID = ?");
					$stmt2->bind_param("i",$Userid);
					$stmt2->execute();
					$result2 = $stmt2->get_result();
					$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					$stmt2->close();
					?>
					<tr>
					<td id="tdborder"><p>Name:</p></td><td id="tdborder"><p><?php echo $row2['UserFName']; ?></p></td>
					</tr>
					<?php
				}
			}
			?>
			</table>
			</center>
			</div>
			<?php
		?>
	</div>
	</div>
	<div id="deactivateUsers" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeDeactivateUsers()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="id" placeholder="Account ID" required/>
			<input type="text" name="username" placeholder="Username" required/>
			<input name="deactivateUsersButton" type="submit" value="Deactivate" />
			</form>
			</div>
		</div>
	</div>
	<div id="removeAssist" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeRemoveAssist()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="id" placeholder="Account ID" required/>
			<input type="text" name="username" placeholder="Username" required/>
			<input name="removeAssistButton" type="submit" value="Remove" />
			</form>
			</div>
		</div>
	</div>
	<div id="activateUsers" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeActivateUsers()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="id" placeholder="Account ID" required/>
			<input type="text" name="username" placeholder="Username" required/>
			<input name="activateUsersButton" type="submit" value="Activate" />
			</form>
			</div>
		</div>
	</div>
	<div id="extendUsers" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeExtendUsers()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<input type="text" name="id" placeholder="Account ID" required/>
			<br>
			<br>
			<h1>Extend Membership</h1>
			<input type="radio" name="membership" value="7" checked> 1 Week
			<input type="radio" name="membership" value="30"> 1 Month
			<input type="radio" name="membership" value="365"> 1 Year<br>
			<br>
			<input name="extendUsersButton" type="submit" value="Extend" />
			</form>
			</div>
		</div>
	</div>
	<div id="addInstructorForm" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAddInstructorUser()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				
				$stmt = $conn->prepare("SELECT * FROM gyminstructor");
				$stmt->execute();
				$result = $stmt->get_result();
				?>
				Choose Instructor:</br>
				<select name="instructor">
				<?php
					while($row = $result->fetch_array(MYSQLI_ASSOC))
					{
						?>
						<option value="<?php echo $row['InstructorID'] ?>"><?php echo $row['InsFName'] ?></option>
						<?php
					}
				?>
			<input name="instructorUsersButton" type="submit" value="Choose" />
			</form>
			</div>
		</div>
	</div>
	<div id="checkBalanceUser" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeCheckBalanceUser()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				$user_check = $_SESSION['username'];
				$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
				$stmt->bind_param("s",$user_check);
				if($stmt->execute())
				{
					$result = $stmt->get_result();
					$accountdata = $result->fetch_array(MYSQLI_ASSOC);
					$stmt->close();
					$stmt2 = $conn->prepare("SELECT * FROM userdata WHERE ID = ?");
					$stmt2->bind_param("i",$accountdata['ID']);
					if($stmt2->execute())
					{
						$result2 = $stmt2->get_result();
						$userdata = $result2->fetch_array(MYSQLI_ASSOC);
						$stmt2->close();
						$stmt3 = $conn->prepare("SELECT * FROM userbalance WHERE UserID = ?");
						$stmt3->bind_param("i",$userdata['UserID']);
						$stmt3->execute();
						$result3 = $stmt3->get_result();
						$balancedata = $result3->fetch_array(MYSQLI_ASSOC);
						?>
						<center>
						<table>
						<tr>
						<td id="tdborder"><p>Balance:</p></td><td id="tdborder"><p><?php echo $balancedata['UserBalance'] ?></p></td>
						</tr>
						</table>
						</center>
						<?php
						$stmt3->close();
					}
				}
			?>
			</form>
			</div>
		</div>
	</div>
	<div id="UsersRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeUsersRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6v2">
			<center>
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<table>
			<tr>
			<td id="tdborder">UserID</td>
			<td id="tdborder">First Name</td>
			<td id="tdborder">Last Name</td>
			<td id="tdborder">Email</td>
			<td id="tdborder">Age</td>
			<td id="tdborder">Address</td>
			<td id="tdborder">Gender</td>
			<td id="tdborder">Contact</td>
			<td id="tdborder">AccountID</td>
			<td id="tdborder">InstructorID</td>
			</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				$stmt = $conn->prepare("SELECT * FROM userdata");
				$stmt->execute();
				$result = $stmt->get_result();
				while($userdata = $result->fetch_array(MYSQLI_ASSOC))
				{
					?>
					<tr>
					<td id="tdborder"><p><?php echo $userdata['UserID'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserFName'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserLName'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserEmail'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserAge'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserAddress'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserGender'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['UserContact'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['ID'] ?></p></td>
					<td id="tdborder"><p><?php echo $userdata['InstructorID'] ?></p></td>
					</tr>
					<?php
				}
			?>
			</table>
			</form>
			</center>
			</div>
		</div>
	</div>
	<div id="InstructorRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeInstructorRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6v2">
			<center>
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<table>
			<tr>
			<center>
			<td id="tdborder">InstructorID</td>
			<td id="tdborder">First Name</td>
			<td id="tdborder">Last Name</td>
			<td id="tdborder">Email</td>
			<td id="tdborder">Age</td>
			<td id="tdborder">Address</td>
			<td id="tdborder">Gender</td>
			<td id="tdborder">Contact</td>
			<td id="tdborder">AccountID</td>
			</center>
			</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				$stmt = $conn->prepare("SELECT * FROM gyminstructor");
				$stmt->execute();
				$result = $stmt->get_result();
				while($instructordata = $result->fetch_array(MYSQLI_ASSOC))
				{
					?>
					<tr>
					<center>
					<td id="tdborder"><p><?php echo $instructordata['InstructorID'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsFName'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsLName'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsEmail'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsAge'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsAddress'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsGender'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['InsContact'] ?></p></td>
					<td id="tdborder"><p><?php echo $instructordata['ID'] ?></p></td>
					</center>
					</tr>
					<?php
				}
			?>
			</table>
			</form>
			</center>
			</div>
		</div>
	</div>
	<div id="AssistRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAssistRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6v2">
			<center>
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<table>
			<tr>
			<center>
			<td id="tdborder">AssistID</td>
			<td id="tdborder">First Name</td>
			<td id="tdborder">Last Name</td>
			<td id="tdborder">Email</td>
			<td id="tdborder">Age</td>
			<td id="tdborder">Address</td>
			<td id="tdborder">Gender</td>
			<td id="tdborder">Contact</td>
			<td id="tdborder">AccountID</td>
			</center>
			</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				$stmt = $conn->prepare("SELECT * FROM assistadmin");
				$stmt->execute();
				$result = $stmt->get_result();
				while($assistdata = $result->fetch_array(MYSQLI_ASSOC))
				{
					?>
					<tr>
					<center>
					<td id="tdborder"><p><?php echo $assistdata['AssistID'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistFName'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistLName'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistEmail'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistAge'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistAddress'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistGender'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['AssistContact'] ?></p></td>
					<td id="tdborder"><p><?php echo $assistdata['ID'] ?></p></td>
					</center>
					</tr>
					<?php
				}
			?>
			</table>
			</form>
			</center>
			</div>
		</div>
	</div>
	<div id="AccountRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeAccountRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6v2">
			<center>
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<table>
			<tr>
			<center>
			<td id="tdborder">ID</td>
			<td id="tdborder">Username</td>
			<td id="tdborder">Password</td>
			<td id="tdborder">Account Type</td>
			<td id="tdborder">Account Status</td>
			</center>
			</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				$stmt = $conn->prepare("SELECT * FROM accounts");
				$stmt->execute();
				$result = $stmt->get_result();
				while($accountdata = $result->fetch_array(MYSQLI_ASSOC))
				{
					?>
					<tr>
					<center>
					<td id="tdborder"><p><?php echo $accountdata['ID'] ?></p></td>
					<td id="tdborder"><p><?php echo $accountdata['Username'] ?></p></td>
					<td id="tdborder"><p><?php echo $accountdata['Password'] ?></p></td>
					<td id="tdborder"><p><?php echo $accountdata['AccType'] ?></p></td>
					<td id="tdborder"><p><?php echo $accountdata['AccStatus'] ?></p></td>
					</center>
					</tr>
					<?php
				}
			?>
			</table>
			</form>
			</center>
			</div>
		</div>
	</div>
	<div id="membershipRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeMembershipRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
			<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<center>
			<table>
				<tr>
					<td id="tdborder"> Registration Date </td>
					<td id="tdborder"> Expiration Date </td>
					<td id="tdborder"> Total Days </td>
				</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				
				$stmt = $conn->prepare("SELECT * FROM usermembership WHERE UserID = ?");
				$stmt->bind_param("i",$Userid);
				if($stmt->execute())
				{
					$result = $stmt->get_result();
					$membershipdata = $result->fetch_array(MYSQLI_ASSOC);
					$stmt->close();
					?>
						<tr>
							<td id="tdborder"> <?php echo $membershipdata['RegDate'] ?> </td>
							<td id="tdborder"> <?php echo $membershipdata['ExpDate'] ?> </td>
							<td id="tdborder"> <?php echo $membershipdata['MembershipDays'] ?> </td>
						</tr>
					<?php
				}
				
			?>
			</table>
			</center>
			</form>
			</div>
		</div>
	</div>
	<div id="statusRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeStatusRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
			<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<center>
			<table>
				<tr>
					<td id="tdborder"> Status </td>
				</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}
				
				$user_check = $_SESSION['username'];
				$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
				$stmt->bind_param("s",$user_check);
				if($stmt->execute())
				{
					$result = $stmt->get_result();
					$accountdata = $result->fetch_array(MYSQLI_ASSOC);
					$stmt->close();
					?>
						<tr>
							<td id="tdborder"> <?php echo $accountdata['AccStatus'] ?> </td>
						</tr>
					<?php
				}
				
			?>
			</table>
			</center>
			</form>
			</div>
		</div>
	</div>
	<div id="myClientsRecord" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeMyClientsRecord()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
			<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			<center>
			<table>
				<tr>
					<td id="tdborder">Full Name</td>
				</tr>
			<?php
				$server = "localhost";
				$dbUsername = "root";
				$dbPassword = "";
				$database = "gymdatabase";

				$conn = new mysqli($server, $dbUsername, $dbPassword, $database);
				
				if ($conn->connect_error) {
					die("Connect Failed:" . $conn->connect_error);
				}

				$stmt = $conn->prepare("SELECT * FROM instructorusers WHERE InstructorID = ?");
				$stmt->bind_param("i",$Insid);
				if($stmt->execute())
				{
					$result = $stmt->get_result();
					while($insuserdata = $result->fetch_array(MYSQLI_ASSOC))
					{
						$stmt->close();
						$stmt2 = $conn->prepare("SELECT * FROM userdata WHERE UserID = ?");
						$stmt2->bind_param("i",$insuserdata['UserID']);
						$stmt2->execute();
						$result2 = $stmt2->get_result();
						while($userdata = $result2->fetch_array(MYSQLI_ASSOC))
						{
							?>
								<tr>
									<td id="tdborder"><?php echo $userdata['UserFName'].' '.$userdata['UserLName'] ?></td>
								</tr>
							<?php
						}
					}
				}
			?>
			</table>
			</center>
			</form>
			</div>
		</div>
	</div>
	<div id="changePassword" class="overlay">
		<a href="javascript:void(0)" class="closebtn" onclick="closeChangesPassword()"><i class = "fa fa-close"></i></a>
		<div class="overlay-content">
		<div class="form-style-6">
			<h1><i class="fa fa-themeisle size2 glow"></i></h1>
			<form method="post" autocomplete="off">
			Username: <?php echo $user ?>
			<br>
			<br>
			<input type="password" name="oldpassword" placeholder="Old Password" required/>
			<br>
			<br>
			<input type="password" name="newpassword" placeholder="New Password" required/>
			<input type="password" name="renewpassword" placeholder="Confirm New Password" required/>
			<input name="changePasswordButton" type="submit" value="Change" />
			</form>
			</div>
		</div>
	</div>
	<nav class="navbar navbar-default navbar-fixed-top" style="background-color:black;border-color:black;">
	  <div class="container-fluid">
		<div class="navbar-header">
		  <a class="navbar-brand" href="home.php" id="navColor">
			<p class="glow">
			<i class="fa fa-themeisle glow"></i> S P A R T A C U S
			</p>
		  </a>
		</div>
		<ul class="nav navbar-nav navbar-right">
			<li onclick="openProfile()"><a href="#" id="navColor"><span class="glyphicon glyphicon-user"></span> <?php echo $fname; ?></a></li>
			<li><a href="logout.php" id="navColor"><span class="glyphicon glyphicon-off"></span> Logout</a></li>
		</ul>
	  </div>
	</nav>
	<div class="jumbotron">
	</div>
	<div class="container socialIcons">
	<center>
	<h1 style="color:white;">TRAIN WITH US</h1>
		<div class="row">
			<ul>
				<li><i class="fa fa-facebook-f iCircle iconRotate"></i></li>
				<li><i class="fa fa-twitter iCircle iconRotate"></i></li>
				<li><i class="fa fa-google-plus iCircle iconRotate"></i></li>
				<li><i class="fa fa-youtube iCircle iconRotate"></i></li>
				<li><i class="fa fa-steam iCircle iconRotate"></i></li>
			</ul>
		</div>
	</center>
	</div>
	<br>
	<div class="container-fluid manageButton">
		<div class="row services">
		<center>
		<h1>Profile Management</h1>
		</center>
		<?php
			if(isset($chkMsg))
			{
				?>
					<div class="alert alert-success">
						<center>
							<strong><?php echo $chkMsg ?></strong>
						</center>
					</div>
				<?php
			}
		?>
		<?php
			if(isset($errMsg))
			{
				?>
					<div class="alert alert-danger">
						<center>
							<strong><?php echo $errMsg ?></strong>
						</center>
					</div>
				<?php
			}
		?>
		<?php
			if($type == "User" || $type == "Instructor" || $type == "Assistant Admin" ) {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user services-icon glow2" onclick="openProfile()"></i>
					</a>
					<h4>Profile</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Instructor") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-users services-icon glow2" onclick="openMyClientsRecord()"></i>
					</a>
					<h4>My Clients</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "User" && $FInsid == NULL) {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-plus services-icon glow2" onclick="openAddInstructorUser()"></i>
					</a>
					<h4>Add Instructor</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "User") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-money services-icon glow2" onclick="openCheckBalanceUser()"></i>
					</a>
					<h4>Total Spent</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "User") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-clock-o services-icon glow2" onclick="openMembershipRecord()"></i>
					</a>
					<h4>Membership Record</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "User") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-heartbeat services-icon glow2" onclick="openStatusRecord()"></i>
					</a>
					<h4>Status</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-plus services-icon glow2" onclick="openAddUser()"></i>
					<h4>User</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-plus services-icon glow2" onclick="openAddInstructor()"></i>
					<h4>Gym Instructor</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-plus services-icon glow2" onclick="openAddAssist()"></i>
					<h4>Assistant Admin</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-plus services-icon glow2" onclick="openAddAdmin()"></i>
					<h4>Admin</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-calendar-times-o services-icon glow2" onclick="openExpiredAccounts()"></i>
					<h4>Expired Users</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin" || $type == "Assistant Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-user-times services-icon glow2" onclick="openDeactivateUsers()"></i>
					<h4>Deactivate Users</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-times-circle services-icon glow2" onclick="openRemoveAssist()"></i>
					<h4>Remove Assistant Admin</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin" || $type == "Assistant Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-check-square-o services-icon glow2" onclick="openActivateUsers()"></i>
					<h4>Activate Users</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-clock-o services-icon glow2" onclick="openExtendUsers()"></i>
					<h4>Extend Users</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin" || $type == "Assistant Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-book services-icon glow2" onclick="openUsersRecord()"></i>
					<h4>Users Record List</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin" || $type == "Assistant Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-book services-icon glow2" onclick="openInstructorRecord()"></i>
					<h4>Instructor Record List</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin" || $type == "Assistant Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-book services-icon glow2" onclick="openAssistRecord()"></i>
					<h4>Assistant Admin Record List</h4>
				</div>
				<?php
			}
		?>
		<?php
			if($type == "Admin") {
				?>
				<div class="col-sm-4">
					<i class="fa fa-book services-icon glow2" onclick="openAccountRecord()"></i>
					<h4>Accounts Record List</h4>
				</div>
				<?php
			}
		?>
				<div class="col-sm-4">
					<i class="fa fa-key services-icon glow2" onclick="openChangesPassword()"></i>
					</a>
					<h4>Change Password</h4>
				</div>
		</div>
	</div>
	<div class="container-fluid">
		<center>
		<h1 id="fontCustomize">SEARCH</h1>
			<div class="form-style-6">
				<form method="post" autocomplete="off">
					<input type="text" name="id" placeholder="ID" required/>
					Type of Account
					<br>
					<br>
					<select name="searchType">
					<option value="User">User</option>
					<option value="Instructor">Instructor</option>
					</select>
					<input name="searchButton" type="submit" value="Search" />
				</form>
			</div>
				<br>
				<br>
				<?php
					if(isset($_POST['searchButton']))
					{
						$id = $_POST['id'];
						$searchType = $_POST['searchType'];
						if($searchType == "User")
						{
							$stmt = $conn->prepare("SELECT * FROM userdata WHERE UserID = ?");
							$stmt->bind_param("i",$id);
							if($stmt->execute())
							{
								$result = $stmt->get_result();
								?>
								<div class="form-style-6v2">
								<h1><i class="fa fa-themeisle size2 glow"></i></h1>
								<form method="post" autocomplete="off">
								<table>
								<center>
									<tr>
									<td id="tdborder">UserID</td>
									<td id="tdborder">First Name</td>
									<td id="tdborder">Last Name</td>
									<td id="tdborder">Email</td>
									<td id="tdborder">Age</td>
									<td id="tdborder">Address</td>
									<td id="tdborder">Gender</td>
									<td id="tdborder">Contact</td>
									<td id="tdborder">AccountID</td>
									<td id="tdborder">InstructorID</td>
									</tr>
								<?php
								while($userdata = $result->fetch_array(MYSQLI_ASSOC))
								{
									?>
										<center>
										<tr>
										<td id="tdborder"><p><?php echo $userdata['UserID'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserFName'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserLName'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserEmail'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserAge'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserAddress'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserGender'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['UserContact'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['ID'] ?></p></td>
										<td id="tdborder"><p><?php echo $userdata['InstructorID'] ?></p></td>
										</tr>
										</center>
									<?php
								}
								?>
								</form>
								</table>
								</center>
								</div>
								<?php
							}
							else
							{
								$errMsg = "No Result Found";
							}
						}
						else
						{
							$stmt = $conn->prepare("SELECT * FROM gyminstructor WHERE InstructorID = ?");
							$stmt->bind_param("i",$id);
							if($stmt->execute())
							{
								$result = $stmt->get_result();
								?>
								<div class="form-style-6v2">
								<h1><i class="fa fa-themeisle size2 glow"></i></h1>
								<form method="post" autocomplete="off">
								<table>
								<center>
									<tr>
									<td id="tdborder">InstructorID</td>
									<td id="tdborder">First Name</td>
									<td id="tdborder">Last Name</td>
									<td id="tdborder">Email</td>
									<td id="tdborder">Age</td>
									<td id="tdborder">Address</td>
									<td id="tdborder">Gender</td>
									<td id="tdborder">Contact</td>
									<td id="tdborder">AccountID</td>
									</tr>
								<?php
								while($instructordata = $result->fetch_array(MYSQLI_ASSOC))
								{
									?>
									<center>
									<td id="tdborder"><p><?php echo $instructordata['InstructorID'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsFName'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsLName'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsEmail'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsAge'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsAddress'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsGender'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['InsContact'] ?></p></td>
									<td id="tdborder"><p><?php echo $instructordata['ID'] ?></p></td>
									</center>
									<?php
								}
								?>
								</form>
								</table>
								</center>
								</div>
								<?php
							}
							else
							{
								$errMsg = "No Result Found";
							}
						}
					}
				?>
		</center>
	</div>
	<br>
	<div class="container-fluid">
		<center>
		<div class="row">
			<div class="col-md-12">
			<h1 id="fontCustomize">EXERCISES</h1>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise.png" height="150px" width="150px"/>
			<h1>Dumbbell Rollout</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/pushups.png" height="150px" width="150px"/>
			<h1>Pushups</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise-1.png" height="150px" width="150px"/>
			<h1>Dumbbell Lunge</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise-2.png" height="150px" width="150px"/>
			<h1>Pullups</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/weightlifting.png" height="150px" width="150px"/>
			<h1>Shoulder Press</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise-16.png" height="150px" width="150px"/>
			<h1>Kick Boxing</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise-9.png" height="150px" width="150px"/>
			<h1>Standing Toe Raise</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/exercise-10.png" height="150px" width="150px"/>
			<h1>Bike Workout</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/weightlifting-8.png" height="150px" width="150px"/>
			<h1>Biceps Curl</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/running.png" height="150px" width="150px"/>
			<h1>Threadmill</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/stretching-1.png" height="150px" width="150px"/>
			<h1>Lunges</h1>
			</div>
			<div class="col-md-3" id="picCustomize">
			<img src="exercise/png/yoga.png" height="150px" width="150px"/>
			<h1>Squats</h1>
			</div>
		</div>
		</center>
	</div>
	<div class="container-fluid">
		<div id="googleMap"></div>
	</div>
	<br>
	<br>
	<center>
	<div id="footer">
		<i class="fa fa-themeisle size2 glow"></i>
		<br>
		<br>
		<br>
		<p class="glow color" style="color:white"> <i class="fa fa-copyright"></i> Copyright 2016 Spartacus Gym | All Rights Reserved.</p>

	</div>
	</center>
	</body>
</html>