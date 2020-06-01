<div class="container">

    {% if labLogo is defined %}
        <div class="row">
            <div class="col-xs-12">
                <img class="img-responsive pull-right" src="{{ image('organisation', labLogo) }}" alt="Logo" style="max-width: 300px; max-height: 150px;"/>
            </div>
        </div>
    {% endif %}
    <div class="row">
        <div class="col-xs-5 address">
            <span>{{ lab.name }}</span><br>
            <span>{{ lab.address }}</span><br>
            <span>{{ lab.zipcode }}</span><br>
            <span>{{ lab.city }}</span><br>
        </div>
        <div class="col-xs-5 address text-right">
            <span>{{ dentist.name }}</span><br>
            <span>{{ dentist.address }}</span><br>
            <span>{{ dentist.zipcode }}</span><br>
            <span>{{ dentist.city }}</span><br>
        </div>
    </div>
    <div class="row personal-data">
        <div class="col-xs-12">
            <span>{{ "Date"|t }}: <strong>{{ date("Y-m-d", strtotime(order.order_at)) }}</strong></span>
        </div>
        <div class="col-xs-12">
            <span>{{ "Order no."|t }}: <strong>{{ order.code }}</strong></span>
        </div>
        <div class="col-xs-12">
            <span>{{ "Delivery note number"|t }}: <strong>{{ deliveryNumber }}</strong></span>
        </div>
        {% if order.DentistOrderData %}
            <div class="col-xs-12">
                <span>{{ "Patient"|t }}: <strong>{{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}</strong></span>
            </div>
        {% endif %}
        {% if order.DentistOrderBsn and order.DentistOrderBsn.getBsn() %}
            <div class="col-xs-12">
                <span>{{ "BSN"|t }}: <strong>{{ order.DentistOrderBsn.getBsn() }}</strong></span>
            </div>
        {% endif %}
    </div>
    <div class="row personal-data">
        {% for dor in order.DentistOrderRecipe %}
            <div class="col-xs-12">
                <h3>{{"Recipe name"|t}}: {{ dor.Recipes.getName() }}</h3>
            </div>
            <div class="col-xs-12">
                <table width="100%">
                    <tr>
                        <th></th>
                        <th>{{ "Tariff code"|t }}</th>
                        <th>{{ "Description tariff code"|t }}</th>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Price tariff code"|t }}</th>
                        <th>{{ "Price"|t }}</th>
                    </tr>

                {% for dord in dor.DentistOrderRecipeData %}

                    {% if dord.field_type == 'checkbox' %}
                        {% for value in json_decode(dord.field_value) %}
                            {% for opt in dord.Options %}
                                {% if opt.value == value %}
                                    <tr>
                                        <td></td>
                                        <td>{{ opt.Tariff.code }}</td>
                                        <td>{{ opt.Tariff.description }}</td>
                                        <td>1</td>
                                        <td>{{ opt.Tariff.price }}</td>
                                        <td>{{ opt.Tariff.price }}</td>
                                    </tr>
                                {% endif %}
                            {% endfor %}
                        {% endfor %}
                    {% endif %}

                    {% if dord.field_type == 'select' %}
                        {% for opt in dord.Options %}
                            {% if opt.value == dord.field_value %}
                                <tr>
                                    <td></td>
                                    <td>{{ opt.Tariff.code }}</td>
                                    <td>{{ opt.Tariff.description }}</td>
                                    <td>1</td>
                                    <td>{{ opt.Tariff.price }}</td>
                                    <td>{{ opt.Tariff.price }}</td>
                                </tr>
                            {% endif %}
                        {% endfor %}
                    {% endif %}

                    {% if dord.field_type == 'number' %}
                        <tr>
                            <td></td>
                            <td>{{ dord.Tariff.code }}</td>
                            <td>{{ dord.Tariff.description }}</td>
                            <td>{{ dord.field_value }}</td>
                            <td>{{ dord.Tariff.price }}</td>
                            <td>{{ dord.Tariff.price * dord.field_value }}</td>
                        </tr>
                    {% endif %}

                    {% if dord.field_type == 'statement' %}
                        <tr>
                            <td></td>
                            <td>{{ dord.Tariff.code }}</td>
                            <td>{{ dord.Tariff.description }}</td>
                            <td>1</td>
                            <td>{{ dord.Tariff.price }}</td>
                            <td>{{ dord.Tariff.price }}</td>
                        </tr>
                    {% endif %}

                    {% if dord.field_type == 'text' and dord.Tariff is not null %}
                        <tr>
                            <td></td>
                            <td>{% if dord.Tariff %}{{ dord.Tariff.code }}{% endif %}</td>
                            <td>{% if dord.Tariff %}{{ dord.Tariff.description }}{% endif %}</td>
                            <td>1</td>
                            <td>{% if dord.Tariff %}{{ dord.Tariff.price }}{% endif %}</td>
                            <td>{% if dord.Tariff %}{{ dord.Tariff.price }}{% endif %}</td>
                        </tr>
                    {% endif %}

                {% endfor %}
                </table>
            </div>
            <div class="col-xs-12">
                <h4 class="pull-right">{{"Total activities"|t}}: {{ dor.getPrice() }}</h4>
                <h4 class="pull-right">{{"Total material"|t}}: {{ dor.getPrice() }}</h4>
                <h4 class="pull-right">{{"Total"|t}}: {{ dor.getPrice() }}</h4>
            </div>
            <div class="col-xs-12">
                <h3>{{"Certificate data recipe"|t}} [{{ dor.Recipes.getName() }}]</h3>
                <table width="100%">
                    <tr>
                        <th>{{ "Tariff code"|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Input"|t }}</th>
                    </tr>

                    {% for dord in dor.DentistOrderRecipeData %}

                        {% if dord.field_type == 'checkbox' %}
                            {% for value in json_decode(dord.field_value) %}
                                {% for opt in dord.Options %}
                                    {% if opt.value == value and (opt.tariff_options['lot'] != "none" or opt.tariff_options['batch'] != "none" or opt.tariff_options['alloy'] != "no" or opt.tariff_options['design'] != "no") %}
                                        <tr>
                                            <td>{{ opt.Tariff.code }}</td>
                                            <td>{{ opt.Tariff.description }}</td>
                                            <td>{{ "Lot"|t }}: {{ opt.tariff_options['lot'] }}<br />
                                                {{ "Batch"|t }}: {{ opt.tariff_options['batch'] }}<br />
                                                {{ "Alloy"|t }}: {{ opt.tariff_options['alloy'] }}<br />
                                                {{ "Design"|t }}: {{ opt.tariff_options['design'] }}</td>
                                        </tr>
                                    {% endif %}
                                {% endfor %}
                            {% endfor %}
                        {% endif %}

                        {% if dord.field_type == 'select' %}
                            {% for opt in dord.Options %}
                                {% if opt.value == value and (opt.tariff_options['lot'] != "none" or opt.tariff_options['batch'] != "none" or opt.tariff_options['alloy'] != "no" or opt.tariff_options['design'] != "no") %}
                                    <tr>
                                        <td>{{ opt.Tariff.code }}</td>
                                        <td>{{ opt.Tariff.description }}</td>
                                        <td>{{ "Lot"|t }}: {{ opt.tariff_options['lot'] }}<br />
                                            {{ "Batch"|t }}: {{ opt.tariff_options['batch'] }}<br />
                                            {{ "Alloy"|t }}: {{ opt.tariff_options['alloy'] }}<br />
                                            {{ "Design"|t }}: {{ opt.tariff_options['design'] }}</td>
                                    </tr>
                                {% endif %}
                            {% endfor %}
                        {% endif %}

                        {% if (dord.field_type == 'text' and dord.Tariff) or in_array(dord.field_type, ['number', 'statement']) %}
                            {% if dord.tariff_options['lot'] != "none" or dord.tariff_options['batch'] != "none" or dord.tariff_options['alloy'] != "no" or dord.tariff_options['design'] != "no" %}
                            <tr>
                                <td>{{ dord.Tariff.code }}</td>
                                <td>{{ dord.Tariff.description }}</td>
                                <td>{{ "Lot"|t }}: {{ dord.tariff_options['lot'] }}<br />
                                    {{ "Batch"|t }}: {{ dord.tariff_options['batch'] }}<br />
                                    {{ "Alloy"|t }}: {{ dord.tariff_options['alloy'] }}<br />
                                    {{ "Design"|t }}: {{ dord.tariff_options['design'] }}</td>
                            </tr>
                            {% endif %}
                        {% else %}
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        {% endif %}
                    {% endfor %}
                </table>
            </div>
        {% endfor %}
            <div class="col-xs-12">{{ lab.delivery_notes }}</div>
    </div>
</div>


