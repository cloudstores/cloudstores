<!DOCTYPE html>
<html>
<head>
 	<title>Cloud infrastructure for e-commerce in domestic and export market</title>
 	<meta charset="utf-8" />
 	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
	<meta name="keyword" content="Manufacturers, suppliers, sellers, wholesale, retailers" />
	<meta name="description" content="Find wholesale and retial SME products manufactured in India and are available for exports as well as in domestic markets"/>
	<link rel="stylesheet" type="text/css" href="http://localhost/knitpeer-UX/styles/normalize.css"></link>
	<link rel="stylesheet" type="text/css" href="http://localhost/knitpeer-UX/styles/frontpage.css"></link>
</head>

<body class="body">
	
	<div class="auth">
		<div class="signin">
		<article>
			<script type="text/javascript" src="http://localhost/knitpeer-UX/jscripts/jquery-1.11.3.js"></script> 
			<script type="text/javascript" src="http://localhost/knitpeer-UX/jscripts/authenticate.js"></script>
			<form action="signin.php" method="post" class="form" id="signinForm">
				<input type="text" id="username" name="username" placeholder="email-id or phone no.">
				<input type="password" id="passwd" name="passwd" placeholder="password">
				<button class="button" id="signin" value="sign in" >sing in </button>
			</form>
			<div id="signin_response" class="auth_response"></div>
		</article>
		</div>
	
		<div class="register">
		<article>
			<p>Join as new member</p>
			<script type="text/javascript" src="http://localhost/knitpeer-UX/jscripts/jquery-1.11.3.js"></script> 
			<script type="text/javascript" src="http://localhost/knitpeer-UX/jscripts/authenticate.js"></script>
			<form action="register.php" method="post" class="form" id="registerForm">
				<input type="text" id="FirstName" name="FirstName" placeholder="Mickey">
				<input type="text" id="LastName" name="LastName" placeholder="Mouse"><br/>
				<input type="email" id="email" name="email" placeholder="e-mail" size=43><br/>
				<input type="text" id="phone" name="phone" placeholder="phone no. for authentication"><br/>
				<input type="password" id="passwd1" name="passwd" placeholder="password"><br/>
				<input type="password" id="passwd2" name="passwd" placeholder="confirm password"><br/>
				<button class="button" id="signup" type="submit" value="sign up" >sign up</button>
			</form>
			<div id="signup_response" class="auth_response"><p></p></div>
		</article>
		</div>
	</div>

	<footer class="footer">
		<p> Copyrigts &copy; <a href="#" title="commerce">company name</a></p>
	</footer>
	
</body>
</html>