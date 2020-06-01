{% extends "emailTemplates/layouts/main.volt" %}
{% block content %}
	{{ "You've received the following order from lab"|t }} {{ cart.Organisation.getName() }}</p>
	 <p style="margin: 0;font-size: 14px;line-height: 17px">{{ "Order number"|t }}: {{ cartCode }}</p>
	<table class="table table-striped mail-order-table" width="800" style="width:800px;">
		<thead>
		<tr>
			<td style="padding-bottom:10px;">{{ "Amount"|t }}</td>
			<td style="padding-bottom:10px;">{{ "Product name"|t }}</td>
			<td style="padding-bottom:10px;">{{ "Product material"|t }}</td>
			<td style="padding-bottom:10px;">{{ "Product code"|t }}</td>
			<td style="padding-bottom:10px;">{{ "Product price"|t }}</td>
		</tr>
		</thead>
		<tbody>
		{% set totalPrice = 0 %}
		{% for orderCartProduct in orderCartProducts %}
			{% set product = orderCartProduct.Product %}
			{% set totalPrice += product.price %}
			<tr>
				<td>{{ orderCartProduct.amount }}</td>
				<td>{{ product.name }}</td>
				<td>{{ product.material }}</td>
				<td>{{ product.code }}</td>
				<td>&euro;{{ product.price }}</td>
			</tr>
		{% endfor %}
		<tr><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td>{{ "Total Ex. VAT"|t }}</td><td>&euro;{{ totalPrice }}</td></tr>
		</tbody>
	</table>
	<p style="margin: 0;font-size: 14px;line-height: 17px">{{ "Open this order in"|t }} <a href="{{ baseUrl }}/supplier/order/edit/{{ cart.getId() }}">{{ "Orderlist"|t }}</a></p>
{% endblock %}