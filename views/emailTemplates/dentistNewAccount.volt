{% extends "emailTemplates/layouts/mainInvite.volt" %}

{% block content %}

	Er is voor u een Signadens account aangemaakt.<br/>
	U kunt nu inloggen met onderstaande gegevens;<br/>
	<br/>
	Gebruikersnaam / mail: <span style="color: #000;">{{ email }}</span><br/>
	Wachtwoord: <span style="color: #000;">{{ password }}</span><br/>
	<br/>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td>
		  <table border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td bgcolor="#4471C4" style="padding: 12px 18px 12px 18px; border-radius:3px" align="center"><a href="<?php echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME']; ?>{{ button['url'] }}" target="_blank" style=" font-family: Arial, sans-serif; font-weight: normal; font-size: 20px; color: #ffffff; text-decoration: none; display: inline-block;">Inloggen</a></td>
			</tr>
		  </table>
		</td>
	  </tr>
	</table>

{% endblock %}