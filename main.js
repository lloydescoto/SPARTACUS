		var myCenter = new google.maps.LatLng(15.2233482, 120.57398,13);

		function initialize() {
		var mapProp = {
		center:myCenter,
		zoom:12,
		scrollwheel:false,
		draggable:true,
		mapTypeId:google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);

		var marker = new google.maps.Marker({
		position:myCenter,
		});

		marker.setMap(map);
		}

		google.maps.event.addDomListener(window, 'load', initialize);
		
		$(document).ready(function(){
			$('[data-toggle="popover"]').popover();
		});
		
		function openProfile() {
		document.getElementById("ProfileForm").style.width = "100%";
		}

		function closeProfile() {
		document.getElementById("ProfileForm").style.width = "0%";
		}
		
		function openLogin()
		{
		document.getElementById("loginForm").style.width = "100%";
			}
		function closeLogin()
		{
		document.getElementById("loginForm").style.width = "0%";
		}
		function openAddUser() {
		document.getElementById("addUser").style.width = "100%";
		}

		function closeAddUser() {
		document.getElementById("addUser").style.width = "0%";
		}
		function openAddInstructor() {
		document.getElementById("addInstructor").style.width = "100%";
		}

		function closeAddInstructor() {
		document.getElementById("addInstructor").style.width = "0%";
		}
		function openAddAssist() {
		document.getElementById("addAssist").style.width = "100%";
		}

		function closeAddAssist() {
		document.getElementById("addAssist").style.width = "0%";
		}
		function openAddAdmin() {
		document.getElementById("addAdmin").style.width = "100%";
		}

		function closeAddAdmin() {
		document.getElementById("addAdmin").style.width = "0%";
		}
		function openExpiredAccounts() {
		document.getElementById("expiredForm").style.width = "100%";
		}

		function closeExpiredAccounts() {
		document.getElementById("expiredForm").style.width = "0%";
		}
		function openDeactivateUsers() {
		document.getElementById("deactivateUsers").style.width = "100%";
		}

		function closeDeactivateUsers() {
		document.getElementById("deactivateUsers").style.width = "0%";
		}
		function openRemoveAssist() {
		document.getElementById("removeAssist").style.width = "100%";
		}

		function closeRemoveAssist() {
		document.getElementById("removeAssist").style.width = "0%";
		}
		function openExtendUsers() {
		document.getElementById("extendUsers").style.width = "100%";
		}

		function closeExtendUsers() {
		document.getElementById("extendUsers").style.width = "0%";
		}
		function openActivateUsers() {
		document.getElementById("activateUsers").style.width = "100%";
		}
		function closeActivateUsers() {
		document.getElementById("activateUsers").style.width = "0%";
		}
		function openAddInstructorUser() {
		document.getElementById("addInstructorForm").style.width = "100%";
		}

		function closeAddInstructorUser() {
		document.getElementById("addInstructorForm").style.width = "0%";
		}
		function openCheckBalanceUser() {
		document.getElementById("checkBalanceUser").style.width = "100%";
		}

		function closeCheckBalanceUser() {
		document.getElementById("checkBalanceUser").style.width = "0%";
		}
		function openUsersRecord() {
		document.getElementById("UsersRecord").style.width = "100%";
		}

		function closeUsersRecord() {
		document.getElementById("UsersRecord").style.width = "0%";
		}
		function openInstructorRecord() {
		document.getElementById("InstructorRecord").style.width = "100%";
		}

		function closeInstructorRecord() {
		document.getElementById("InstructorRecord").style.width = "0%";
		}
		function openAssistRecord() {
		document.getElementById("AssistRecord").style.width = "100%";
		}
		function closeAssistRecord() {
		document.getElementById("AssistRecord").style.width = "0%";
		}
		function openMembershipRecord() {
		document.getElementById("membershipRecord").style.width = "100%";
		}

		function closeMembershipRecord() {
		document.getElementById("membershipRecord").style.width = "0%";
		}
		function openStatusRecord() {
		document.getElementById("statusRecord").style.width = "100%";
		}

		function closeStatusRecord() {
		document.getElementById("statusRecord").style.width = "0%";
		}
		function openMyClientsRecord() {
		document.getElementById("myClientsRecord").style.width = "100%";
		}

		function closeMyClientsRecord() {
		document.getElementById("myClientsRecord").style.width = "0%";
		}
		function openChangesPassword() {
		document.getElementById("changePassword").style.width = "100%";
		}

		function closeChangesPassword() {
		document.getElementById("changePassword").style.width = "0%";
		}
		function openAccountRecord() {
		document.getElementById("AccountRecord").style.width = "100%";
		}

		function closeAccountRecord() {
		document.getElementById("AccountRecord").style.width = "0%";
		}