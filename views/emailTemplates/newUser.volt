{% extends "emailTemplates/layouts/main.volt" %}

{% block title %}{{ "Welcome in Signadens"|t }}{% endblock %}
{% block subtitle %}{{ "Here is your login and password to system"|t }}{% endblock %}

{% block content %}

	Er is voor u een Signadens account aangemaakt.<br/>
	U kunt nu inloggen met onderstaande gegevens;<br/>
	<br/>
	Gebruikersnaam / mail: <span style="color: #000;">{{ email }}</span><br/>
	Wachtwoord: <span style="color: #000;">{{ password }}</span><br/>


{% endblock %}