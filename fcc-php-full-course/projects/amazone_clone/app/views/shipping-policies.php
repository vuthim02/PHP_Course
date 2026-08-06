<?php
// views/shipping-policies.php — shipping rates and delivery info.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Shipping Rates &amp; Policies</h1>

<div class="amz-card amz-help-faq">
  <h2>Delivery speed &amp; rates</h2>
  <table class="amz-table">
    <thead>
      <tr>
        <th>Order total</th>
        <th>Standard delivery</th>
        <th>Delivery time</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo money(25.0); ?> and over</td>
        <td><strong>FREE</strong></td>
        <td>2&ndash;5 business days</td>
      </tr>
      <tr>
        <td>Under <?php echo money(25.0); ?></td>
        <td><?php echo money(9.99); ?> flat</td>
        <td>2&ndash;5 business days</td>
      </tr>
      <tr>
        <td>Any order (express)</td>
        <td><?php echo money(19.99); ?> flat</td>
        <td>1&ndash;2 business days</td>
      </tr>
    </tbody>
  </table>
  <p class="amz-sum-note">Taxes are calculated at checkout based on the shipping
     address you provide.</p>
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128666;</div>
    <h3>Standard delivery</h3>
    <p>FREE on orders over <?php echo money(25.0); ?>, otherwise <?php echo money(9.99); ?>.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128337;</div>
    <h3>Express delivery</h3>
    <p>Get it in 1&ndash;2 business days for <?php echo money(19.99); ?>.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128257;</div>
    <h3>Free returns</h3>
    <p>Changed your mind? Start a return within 30 days of delivery.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128230;</div>
    <h3>Track every package</h3>
    <p>Follow your delivery in real time from Your Orders.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>Where's my order?</h2>
  <p>Sign in and check Your Orders for live tracking, or start a return from Returns &amp; Replacements.</p>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/account/orders">Track an order</a>
  <a class="amz-btn amz-btn-white amz-btn-inline" href="/returns">Start a return</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
