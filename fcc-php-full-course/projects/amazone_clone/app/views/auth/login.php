<?php
// views/auth/login.php — sign in to an existing account.
$pageTitle = "Sign in";
include __DIR__ . "/../layout/header.php";
?>
<div class="amz-auth">
  <div class="amz-auth-card">
    <h1>Sign in</h1>
    <form action="/login" method="post" class="amz-auth-form">
      <?php echo csrf_field(); ?>
      <label class="amz-field">Email
        <input type="email" name="email" required autofocus
               value="<?php echo e($_SESSION["old"]["email"] ?? ""); ?>">
      </label>
      <label class="amz-field">Password
        <input type="password" name="password" required>
      </label>
      <button class="amz-btn amz-btn-yellow" type="submit">Sign in</button>
    </form>
    <p class="amz-auth-alt">By continuing you agree to the amazone Conditions of Use
      &amp; Privacy Notice (this is a demo).</p>
  </div>
  <div class="amz-auth-divider">
    <span>New to amazone?</span>
    <a class="amz-btn amz-btn-white" href="/register">Create your amazone account</a>
  </div>
</div>
<?php unset($_SESSION["old"]);
include __DIR__ . "/../layout/footer.php"; ?>
