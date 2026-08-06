<?php
// contact.php — Simple contact form that reads the fields with GET
include "data.php";

$sentName    = $_GET["name"] ?? "";
$sentMessage = $_GET["message"] ?? "";
$submitted   = ($sentName !== "" || $sentMessage !== "");

include "header.php";
?>

<h1>Contact Me</h1>

<?php if ($submitted) { ?>
  <div class="card">
    <h2>Thanks, <?php echo htmlspecialchars($sentName); ?>!</h2>
    <p>Your message &mdash; &ldquo;<?php echo htmlspecialchars($sentMessage); ?>&rdquo;
       &mdash; was received. (This demo doesn't save it anywhere yet.)</p>
    <p><a href="contact.php">Send another</a></p>
  </div>
<?php } ?>

<div class="card">
  <form action="contact.php" method="get">
    <p>
      Name:<br>
      <input type="text" name="name">
    </p>
    <p>
      Message:<br>
      <textarea name="message" rows="4" cols="40"></textarea>
    </p>
    <input type="submit" value="Send">
  </form>
</div>

<?php include "footer.php"; ?>
