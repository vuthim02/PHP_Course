<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Default Title{% endblock %}</title>
</head>
<body>
    <header>
        <h1>{% block heading %}My Site{% endblock %}</h1>
    </header>
    <main>
        {% block content %}
            <p>Default content goes here.</p>
        {% endblock %}
    </main>
    <footer>
        <p>&copy; {{ now('Y') }} | PHP Template Engine</p>
    </footer>
</body>
</html>
