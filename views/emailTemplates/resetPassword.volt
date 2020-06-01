{% extends "emailTemplates/layouts/main.volt" %}

{% block title %}{{ "Reset your password"|t }}{% endblock %}

{% block content %}

	{{ "To reset the password for the Signadens account associated with your email, click on the button below. If you don't want to reset your password, please disregard this email."|t }}
	<br/>

    {% if resetUrl is defined %}
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>
			  <table border="0" cellspacing="0" cellpadding="0">
				<tr>
				  <td bgcolor="#4471C4" style="padding: 12px 18px 12px 18px; border-radius:3px" align="center"><a href="{{ resetUrl }}" target="_blank" style=" font-family: Arial, sans-serif; font-weight: normal; font-size: 20px; color: #ffffff; text-decoration: none; display: inline-block;">{{ "Reset"|t }}</a></td>
				</tr>
			  </table>
			</td>
		  </tr>
		</table>

    {% endif %}
{% endblock %}
