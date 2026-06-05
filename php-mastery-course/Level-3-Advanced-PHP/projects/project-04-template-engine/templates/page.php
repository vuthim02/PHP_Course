{% extends "base" %}

{% block title %}Welcome {{ name }} | Demo{% endblock %}

{% block heading %}Hello, {{ name | upper }}!{% endblock %}

{% block content %}
    <div class="content">
        <p>This template demonstrates the PHP Template Engine features.</p>

        <h2>Users</h2>
        <ul>
        {% for user in users %}
            <li>{{ user.name | e }} — {{ user.email }}</li>
        {% endfor %}
        </ul>

        <h2>Conditional Output</h2>
        {% if show_details %}
            <p>Details are visible.</p>
            <p>Filtered: {{ name | upper | reverse }}</p>
        {% else %}
            <p>Details hidden.</p>
        {% endif %}

        <h2>Loop with Keys</h2>
        <dl>
        {% for key, item in items %}
            <dt>{{ key }}</dt>
            <dd>{{ item }}</dd>
        {% endfor %}
        </dl>

        {% include "footer" %}
    </div>
{% endblock %}
