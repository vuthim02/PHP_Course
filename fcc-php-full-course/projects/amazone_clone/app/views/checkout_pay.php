<?php
// checkout_pay.php — step 2: Stripe card element, confirm on submit.
include __DIR__ . "/layout/header.php";
?>

<h1 class="amz-page-title">Complete your purchase</h1>

<?php if (empty($pubKey) || str_starts_with($pubKey, "pk_test_xxx")) { ?>
  <div class="amz-card">
    <h2>Stripe keys not configured yet</h2>
    <p>Add your free TEST keys to <code>app/config.php</code>:</p>
    <ol>
      <li>Create a Stripe account at <a href="https://dashboard.stripe.com/register" target="_blank" rel="noopener">stripe.com</a>.</li>
      <li>Open <a href="https://dashboard.stripe.com/test/apikeys" target="_blank" rel="noopener">Dashboard → Developers → API keys</a> (Test mode).</li>
      <li>Copy <code>pk_test_...</code> and <code>sk_test_...</code> into the <code>stripe</code> section.</li>
    </ol>
    <p><a class="amz-btn amz-btn-yellow amz-btn-inline" href="/cart">Back to cart</a></p>
  </div>
<?php } else { ?>

<div class="amz-checkout-layout">
  <div class="amz-card">
    <h2 class="amz-step">2. Payment</h2>

    <div class="amz-order-placed">
      <strong>Order #<?php echo (int) $orderId; ?></strong> created &mdash;
      total <strong><?php echo money($summary["total"]); ?></strong>. Nothing is charged until you pay below.
    </div>

    <form id="payment-form">
      <div id="card-element"></div>
      <div id="card-errors" class="amz-card-errors"></div>
      <button class="amz-btn amz-btn-yellow" type="submit" id="pay-button">Pay <?php echo money($summary["total"]); ?></button>
      <p class="amz-buybox-secure">&#128274; Secure checkout powered by <strong>Stripe</strong> (test mode)</p>
      <p class="amz-sum-note">Test card: <code>4242 4242 4242 4242</code> &mdash; any future expiry, any CVC.</p>
    </form>
  </div>

  <aside class="amz-order-summary">
    <h2>Order summary</h2>
    <div class="amz-sum-row"><span>Items</span><span><?php echo money($summary["subtotal"]); ?></span></div>
    <div class="amz-sum-row"><span>Shipping</span>
      <span><?php echo $summary["shipping"] > 0 ? money($summary["shipping"]) : "FREE"; ?></span></div>
    <div class="amz-sum-row"><span>Tax</span><span><?php echo money($summary["tax"]); ?></span></div>
    <div class="amz-sum-row amz-sum-total"><span>Order total</span><span><?php echo money($summary["total"]); ?></span></div>
  </aside>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
  const stripe = Stripe("<?php echo e($pubKey); ?>");
  const clientSecret = "<?php echo e($clientSecret); ?>";
  const elements = stripe.elements();
  const card = elements.create("card");
  card.mount("#card-element");

  const form = document.getElementById("payment-form");
  const payBtn = document.getElementById("pay-button");
  const errors = document.getElementById("card-errors");

  card.on("change", (e) => { errors.textContent = e.error ? e.error.message : ""; });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    payBtn.disabled = true;
    payBtn.textContent = "Processing…";

    const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
      payment_method: { card }
    });

    if (error) {
      errors.textContent = error.message;
      payBtn.disabled = false;
      payBtn.textContent = "Pay <?php echo money($summary["total"]); ?>";
      return;
    }

    if (paymentIntent.status === "succeeded") {
      const csrf = document.querySelector("meta[name=csrf]").content;
      const res = await fetch("/checkout/complete", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ csrf_token: csrf, payment_intent: paymentIntent.id })
      });
      window.location.href = res.redirected ? res.url : "/cart";
    }
  });
</script>

<?php } ?>

<?php include __DIR__ . "/layout/footer.php"; ?>
