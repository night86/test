{% extends "layouts/main.volt" %}
{% block title %} {{ "Order"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("/supplier/order/history") }}"><i class="pe-7s-back"></i></a> {{ "Order"|t }}: {{ order.name }}
    </h3>
    <br/>

    <div class="row">
        <div class="col-md-4">
            <legend>{{ 'Delivery adress'|t }}</legend>

            <div class="form-group">
                <label>{{ 'Name'|t }}</label>
                <div class="col-md-12">
                    {{ order.OrderBy.getFullName() }}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'Address'|t }}</label>
                <div class="col-md-12">
                    {{ order.Organisation.getAddress() }}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'Zip code'|t }}</label>
                <div class="col-md-12">
                    {{ order.Organisation.getZipcode() }}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'City'|t }}</label>
                <div class="col-md-12">
                    {{ order.Organisation.getCity() }}
                    <br/><br/>
                </div>
            </div>
        </div>
        {% if order.Client %}
            <div class="col-md-4">

                <legend>{{ 'Client data'|t }}</legend>

                <div class="form-group">
                    <label>{{ 'Name'|t }}</label>
                    <div class="col-md-12">
                        {{ order.Client.name }}
                        <br/><br/>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ 'Email'|t }}</label>
                    <div class="col-md-12">
                        {{ order.Client.email }}
                        <br/><br/>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ 'Phone'|t }}</label>
                    <div class="col-md-12">
                        {{ order.Client.telephone }}
                        <br/><br/>
                    </div>
                </div>
            </div>
        {% endif %}

        <div class="col-md-4">

            <legend>{{'Order info'|t}}</legend>

            <div class="form-group">
                <label>{{ 'Delivery date'|t }}</label>
                <div class="col-md-12">
                    {% if order.delivery_at %}
                        {{ order.delivery_at }}
                        {% if order.discuss_delivery is 1 %}
                            <small>
                                <br />{{ "This is a rush order. Please contact the dentist to discuss the possibility of an earlier delivery."|t }}
                            </small>
                        {% endif %}
                    {% else %}
                        --
                    {% endif %}
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <legend></legend>
            <table id="recipes" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Amount"|t }}</th>
                    <th>{{ "Price"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for product in order.OrderCartProduct %}
                    <tr>
                        <td>{{ product.Product.name }}</td>
                        <td>{{ product.amount }}</td>
                        <td>{{ product.price }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}