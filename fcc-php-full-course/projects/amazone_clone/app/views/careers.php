<?php
// views/careers.php — work at amazone.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Careers at amazone</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Build the future, one idea at a time</h2>
    <p>Join a team that loves learning. We're a small clone of a big idea &mdash;
       and we're always experimenting with how the store works.</p>
    <a class="amz-btn amz-btn-white" href="#openings">See open roles</a>
  </div>
  <img class="amz-banner-img" src="/img/products/keyboard.jpg" alt="Careers" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128218;</div>
    <h3>Learn by doing</h3>
    <p>Ship real features with plain PHP, SQL, and vanilla JavaScript.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128295;</div>
    <h3>Own your work</h3>
    <p>Small teams, big ownership &mdash; your code goes live, not into a drawer.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#127793;</div>
    <h3>Build for customers</h3>
    <p>Every decision starts with one question: is this better for the customer?</p>
  </div>
</div>

<div class="amz-card amz-help-faq" id="openings">
  <h2>Open roles</h2>
  <details>
    <summary>Junior PHP Developer</summary>
    <p>Help build product, cart, and checkout features. Great for someone learning the
       fundamentals of server-side rendering and SQL.</p>
  </details>
  <details>
    <summary>Full-Stack Learner / Intern</summary>
    <p>Work across the whole stack &mdash; from the SQLite schema to the CSS that makes
       it look like the real store.</p>
  </details>
  <details>
    <summary>UI/UX Tinkerer</summary>
    <p>Make pages feel pixel-perfect: hover states, empty states, and responsive layouts.</p>
  </details>
  <p class="amz-sum-note">This is a demo page for a learning project &mdash; there are no real
     job openings here!</p>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
