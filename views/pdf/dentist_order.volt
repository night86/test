<div class="container">
    <h3>{{ "Order"|t }}: {{ order.code }}</h3>
    <br />
    <table width="100%">
        <tr>
            <td width="50%" style="">
                {{ "Dentist organisation name"|t }}: {{ organisation.getName() }}
            </td>
            <td width="50%">
                {{ 'Patient'|t }}: {{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                {{ 'Client number'|t }}: {{ labDentist.getClientNumber() }}
            </td>
            <td width="50%">
                {{ 'Gender'|t }}: {% if order.DentistOrderData.patient_gender is 'm' %}{{ "Male"|t }}{% elseif order.DentistOrderData.patient_gender is 'f' %}{{ "Female"|t }}{% endif %}
            </td>
        </tr>
        <tr>
            <td width="50%">
                {{ 'Location'|t }}: {% if order.DentistLocation %}{{ order.DentistLocation.getName() }}{% endif %}
            </td>
            <td width="50%">
                {{ "Order date"|t }}: {{ order.order_at|dttoDMY }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                {{ 'Date of birth'|t }}: {{ order.DentistOrderData.getPatientBirthFormat() }}
            </td>
            <td width="50%">
                {{ 'Dentist'|t }}: {% if order.DentistUser %}{{ order.DentistUser.firstname }} {{ order.DentistUser.lastname }}{% endif %}
            </td>
        </tr>
        <tr>
            <td width="50%">
                {{ 'Patient number'|t }}: {{ order.DentistOrderData.patient_number }}
            </td>
            <td width="50%">&nbsp;</td>
        </tr>
    </table>

    <br />
    <h4><strong>{{ 'Recipes'|t }}</strong></h4>
    <br />

    {% for recipeOrder in order.DentistOrderRecipe %}

        <h4><i>{{ recipeOrder.Recipes.ParentRecipe.recipe_number }} - {{ recipeOrder.Recipes.custom_name }}</i></h4>

        <br />
        <table width="100%">
            <thead>
            <tr>
                <th width="34%">{{ "Phase"|t }}</th>
                <th width="33%">{{ 'Requested delivery date'|t }}</th>
                <th width="33%">{{ 'Prefered part of the day'|t }}</th>
            </tr>
            </thead>
            <tbody>
            {% for del in recipeOrder.Delivery %}
                <tr>
                    <td>{{ del.delivery_text }}</td>
                    <td>{{ del.delivery_date|dttoDMY }}</td>
                    <td>{{ del.part_of_day }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>

        {% if recipeOrder.schema_values %}
            <br />
            <strong>{{ "Element-selectie "|t }}</strong>
            <br />{{ implode(', ', json_decode(recipeOrder.schema_values)) }}
            <br />
        {% endif %}

        {% if recipeOrder.DentistOrderRecipeData|length > 0 %}
            {% for options in recipeOrder.DentistOrderRecipeData %}
                <br />
                <strong>{{ options.getFieldName() }}</strong>
                {% if json_decode(options.getFieldValue())|isArray %}
                    {# ???????? no idea why!!!!! ?????????????? #}
                    <?php
                        $arr = json_decode($options->getFieldValue());
                        foreach ($arr as $val) {
                            foreach ($options->Options as $option) {
                                if ($val == $option->getValue()) {
                                    echo "<br />".$option->getOption();
                                }
                            }
                        }
                    ?>
                    {#{% for k,v in json_decode(options.getFieldValue()) %}#}
                        {#{% for checkboxOption in options.Options %}#}
                            {#{% if option.getValue() is v %}#}
                                {#<br />{{ option.option }}#}
                            {#{% endif %}#}
                        {#{% endfor %}#}
                    {#{% endfor %}#}
                {% else %}
                    <br />
                    {% if options.getFieldType() is 'select' %}
                        <?php
                            foreach ($options->Options as $option) {
                                if ($options->getFieldValue() == $option->getValue()) {
                                    echo $option->getOption();
                                }
                            }
                        ?>
                        {#{% for option in options.Options %}#}
                            {#{% if option.getValue() is options.getFieldValue() %}#}
                                {#{{ option.option }}#}
                            {#{% endif %}#}
                        {#{% endfor %}#}
                    {% else %}
                        {{ options.getFieldValue() }}
                    {% endif %}

                {% endif %}
                <br />
            {% endfor %}
        {% endif %}



        <hr />
    {% endfor %}

    <h4><strong>{{ "Client preferences"|t }}</strong></h4>
    <p>{{ labDentist.getClientPreferences() }}</p>

    <hr />

    {% if messages|length > 0 %}
        <h4><strong>{{'Order messages'|t}}</strong></h4>

        <table width="100%">
            <tbody>
            {% for message in messages %}
                <tr style="background: transparent;">
                    <th colspan="3" style="border:none;">{{ message.getCreatedAt() }}</th>
                </tr>
                <tr>
                    <td width="20%">{{ message.Organisation.getName() }}</td>
                    <td width="20%">{{ message.CreatedBy.getFullname() }}</td>
                    <td>{{ message.getNote() }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endif %}


    {#<hr>#}
    {#<div class="row">#}
        {#<div class="col-md-12">#}



            {#<h3>{{ "Recipes"|t }}</h3>#}

            {#<table id="recipes" class="table table-striped" style="border-spacing: 18px; border-collapse: separate;" width="100%">#}
                {#<tbody>#}
                {#{% for recipeOrder in order.DentistOrderRecipe %}#}
                    {#<tr>#}
                        {#<td>{{ recipeOrder.Recipes.custom_name }}</td>#}
                        {#{% if recipeOrder.DentistOrderRecipeData|length > 0 %}#}
                            {#<td>#}
                                {#{% for options in recipeOrder.DentistOrderRecipeData %}#}
                                    {#<p>{{ options.getFieldName() }}: <strong>{{ options.getFieldValue() }}</strong></p>#}
                                {#{% endfor %}#}
                            {#</td>#}
                        {#{% endif %}#}
                        {#<td width="15%">#}
                            {#{% if recipeOrder.Recipes.DGD|length > 0 %}#}
                                {#{% for dgd in recipeOrder.Recipes.DGD %}#}
                                    {#{{ dgd.getDiscountPrice() }}#}
                                {#{% endfor %}#}
                            {#{% else %}#}
                                {#{{ recipeOrder.price }}#}
                            {#{% endif %}#}
                        {#</td>#}
                    {#</tr>#}
                    {#<tr>#}
                        {#<td colspan="3">#}
                            {#<p>{{ "Schema final selection:"|t }}</p>#}
                            {#<p>{% for index,sch in schema %}#}
                                {#{% if index == recipeOrder.id %}#}
                                    {#{% for tooth in sch %}#}
                                        {#{{ tooth }}&nbsp;&nbsp;#}
                                    {#{% endfor %}#}
                                {#{% endif %}#}
                                {#{% endfor %}</p>#}
                        {#</td>#}
                    {#</tr>#}
                {#{% endfor %}#}
                {#</tbody>#}
            {#</table>#}

            {#<h4>{{ "Client preferences"|t }}</h4>#}
            {#<p>{{ organisation.getClientPreferences() }}</p>#}
        {#</div>#}
    {#</div>#}

    {#{% if messages|length > 0 %}#}

    {#<div class="row">#}
        {#<div class="col-md-12">#}

            {#<h3>{{'Order messages'|t}}</h3>#}

            {#<table class="table" style="border-spacing: 18px; border-collapse: separate;" width="100%">#}
                {#<tbody>#}
                {#{% for message in messages %}#}
                    {#<tr>#}
                        {#<td width="20%">{{ message.Organisation.getName() }}</td>#}
                        {#<td width="20%">{{ message.CreatedBy.getFullname() }}</td>#}
                        {#<td>{{ message.getNote() }}</td>#}
                    {#</tr>#}
                {#{% endfor %}#}
                {#</tbody>#}
            {#</table>#}

        {#</div>#}

    {#</div>#}
    {#{% endif %}#}
</div>