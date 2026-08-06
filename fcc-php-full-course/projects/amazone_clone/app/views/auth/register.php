<?php
// views/auth/register.php — create an account.
$pageTitle = "Create Account";
include __DIR__ . "/../layout/header.php";
?>
<div class="amz-auth">
  <div class="amz-auth-card">
    <h1>Create account</h1>
    <form action="/register" method="post" class="amz-auth-form">
      <?php echo csrf_field(); ?>
      <label class="amz-field">Your name
        <input type="text" name="name" required autofocus
               value="<?php echo e($_SESSION["old"]["name"] ?? ""); ?>">
      </label>
      <label class="amz-field">Email
        <input type="email" name="email" required
               value="<?php echo e($_SESSION["old"]["email"] ?? ""); ?>">
      </label>
      <label class="amz-field">Password
        <input type="password" name="password" required minlength="8"
               placeholder="At least 8 characters">
      </label>
      <label class="amz-field">Re-enter password
        <input type="password" name="password_confirm" required>
      </label>
      <button class="amz-btn amz-btn-yellow" type="submit">Create your amazone account</button>
    </form>
    <p class="amz-auth-alt">Already have an account? <a href="/login">Sign in</a></p>
  </div>
</div>
<?php unset($_SESSION["old"]);
include __DIR__ . "/../layout/footer.php"; ?>
