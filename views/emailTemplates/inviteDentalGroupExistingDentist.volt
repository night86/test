{% extends "emailTemplates/layouts/main.volt" %}


{% block title %}{{ "Dental group invites you to be part of the group"|t }}{% endblock %}


{% block content %}

	{{ "Dental group invites you to be part of the group"|t }}

	{% if declineUrl is defined %}
		<a href="{{ declineUrl }}">{{ "Decline"|t }}</a>
	{% endif %}

{% endblock %}