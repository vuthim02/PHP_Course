<button id="back-to-top" class="amz-back-top" type="button">Back to top</button>
<footer class="amz-footer">
  <div class="amz-footer-cols">
    <div class="amz-footer-col">
      <h4>Get to Know Us</h4>
      <a href="/about"<?php echo is_active("/about") ? " class='active'" : ""; ?>>About amazone</a>
      <a href="/careers"<?php echo is_active("/careers") ? " class='active'" : ""; ?>>Careers</a>
      <a href="/sustainability"<?php echo is_active("/sustainability") ? " class='active'" : ""; ?>>Sustainability</a>
    </div>
    <div class="amz-footer-col">
      <h4>Make Money with Us</h4>
      <a href="/sell"<?php echo is_active("/sell") ? " class='active'" : ""; ?>>Sell on amazone</a>
      <a href="/affiliate"<?php echo is_active("/affiliate") ? " class='active'" : ""; ?>>Become an Affiliate</a>
      <a href="/advertise"<?php echo is_active("/advertise") ? " class='active'" : ""; ?>>Advertise Your Products</a>
    </div>
    <div class="amz-footer-col">
      <h4>Let Us Help You</h4>
      <a href="/account"<?php echo $onAccount ?? false ? " class='active'" : ""; ?>>Your Account</a>
      <a href="/shipping-policies"<?php echo is_active("/shipping-policies") ? " class='active'" : ""; ?>>Shipping Rates &amp; Policies</a>
      <a href="/returns"<?php echo is_active("/returns") ? " class='active'" : ""; ?>>Returns &amp; Replacements</a>
      <a href="/help"<?php echo is_active("/help") ? " class='active'" : ""; ?>>Help</a>
    </div>
  </div>
  <div class="amz-footer-bottom">
    <a class="amz-logo amz-logo-sm" href="/"><span>amazone</span></a>
    <p>&copy; <?php echo date("Y"); ?> amazone &mdash; a learning project built with plain PHP + SQLite.</p>
  </div>
</footer>
<script src="/js/main.js"></script>
</body>
</html>
