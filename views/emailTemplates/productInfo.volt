{% extends "emailTemplates/layouts/main.volt" %}

{% block content %}

	Deze informatie is via het formulier verstuurd:<br/>
	<br/>
	Gebruikersnaam: <span style="color: #000;">{{ username }}</span><br/>
	Email: <span style="color: #000;">{{ email }}</span><br/>
	Laboratorium: <span style="color: #000;">{{ userlab }}</span><br/>
	Bericht: <span style="color: #000;">{{ content }}</span><br/>


{% endblock %}