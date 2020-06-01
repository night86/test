<div class="row">

    <div class="col-md-6 col-sm-12">
        <h5>{{ "Changed price alerts"|t }}</h5>
        <table class="simple-datatable table table-striped">
            <thead>
            <th class="sortbydate">{{ "Date"|t }}</th>
            <th>{{ "Product code"|t }}</th>
            <th>{{ "Product name"|t }}</th>
            <th>{{ "Old price"|t }}</th>
            <th>{{ "New price"|t }}</th>
            </thead>
            <tbody>
            {% for product in products %}
                <tr>
                    <td><div class="hidden">{{ product.getUpdatedAt() }}</div>{{ product.getUpdatedAt()|dttonl }}</td>
                    <td>{{ product.getCode() }}</td>
                    <td>{{ product.getName() }}</td>
                    <td>{{ product.getPrice() }}</td>
                    <td>{{ product.getPrice() }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>

    <div class="col-md-6 col-sm-12">
        <h5>{{ "Recent orders"|t }}</h5>
        <table class="simple-datatable table table-striped">
            <thead>
            <th>{{ "Order no."|t }}</th>
            <th>{{ "Supplier"|t }}</th>
            <th>{{ "Order date"|t }}</th>
            <th>{{ "Status"|t }}</th>
            </thead>
            <tbody>
            {% for order in orders %}
                <tr>
                    <td>{{ order.getId() }}</td>
                    <td>{{ order.supplierName() }}</td>
                    <td><div class="hidden">{{ order.getCreatedAt() }}</div>{{ order.getCreatedAt()|dttonl }}</td>
                    <td>{{ order.getStatusLabel()|t }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>

    <div class="col-md-6 col-sm-12">
        <h5>{{ "Access log"|t }}</h5>
        <table class="simple-datatable table table-striped">
            <thead>
            <th class="sortbydate">{{ "Date"|t }}</th>
            <th>{{ "Time"|t }}</th>
            <th>{{ "User"|t }}</th>
            </thead>
            <tbody>
            {% for user in users %}
                <tr>
                    <td><div class="hidden">{{ user.getDateTimeArr()['date'] }}</div>{{ user.getDateTimeArr()['date']|dttonl }}</td>
                    <td>{{ user.getDateTimeArr()['time'] }}</td>
                    <td>{{ user.getFullName() }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>

</div>