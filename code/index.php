<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<link rel="stylesheet" href="https://rawgithub.com/arschmitz/jquery-mobile-datepicker-wrapper/master/jquery.mobile.datepicker.css" />


	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<script src="https://rawgithub.com/jquery/jquery-ui/1-10-stable/ui/jquery.ui.datepicker.js"></script>
	<!-- <script src="http://code.jquery.com/mobile/git/jquery.mobile-git.js"></script>  -->
	<script src="https://rawgithub.com/arschmitz/jquery-mobile-datepicker-wrapper/master/jquery.mobile.datepicker.js"></script>


</head>
<body>

	<div data-role="page" id="login">

	<?php require './pageheader.php'; ?>
	
		<div data-role="main" class="ui-content">
			<p>Kirjaudu sisään</p>
			<form>
				<!-- <label for="email">Sähköpostiosoite:</label> -->
				<input type="email" name="email" id="email" placeholder="Sähköpostiosoite">
				<!-- <label for="pswd" class="ui-hidden-accessible">Salasana:</label> -->
				<input type="password" name="passw" id="pswd" placeholder="Salasana">
			</form>
			<!-- <a href="#createaccount">Luo tili</a> -->
		</div>

		<div data-role="footer" data-id="main-footer">
			<h1>Footer Text</h1>
		</div>
	</div> 

	<div data-role="page" id="createaccount">
		<div data-role="header">
			<h1>Luo tili</h1>
		</div>

		<div data-role="main" class="ui-content">
			<form>
				<input type="text" name="firstname" id="firstname" placeholder="Etunimi">
				<input type="text" name="lastname" id="lastname" placeholder="Sukunimi">
				<input type="email" name="email" id="email" placeholder="Sähköpostiosoite">				
				<input type="text" name="address" id="address" placeholder="Katuosoite">
				<input type="text" name="zipcode" id="zipcode" placeholder="Postinumero">
				<input type="text" name="city" id="city" placeholder="Postitoimipaikka">
				<input type="password" name="password" id="password" placeholder="Salasana">
			</form>
			<a href="#login">Kirjaudu sisään</a>
		</div>

		<div data-role="footer">
			<h1>Footer Text</h1>
		</div>
	</div> 

	<div data-role="page" class="ui-content" id="createswimmer">
		<div data-role="header" data-id="main-header" data-position="fixed"></div>
		<div data-role="main">
			<form>
				<input type="text" name="firstname" id="firstname" placeholder="Etunimi">
				<input type="text" name="lastname" id="lastname" placeholder="Sukunimi">
				<input type="text" data-role="date" name="birthday" id="birthday" placeholder="Syntymäaika">
				<input type="text" name="observations" id="observations" placeholder="Huomioita">
			</form>
		</div>
		<div data-role="footer" data-id="main-footer" data-position="fixed"></div>
	</div>
</body>
</html>

