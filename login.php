<?php
    include 'header.php';
?>

<div class= "container">

<form method="POST" action="loginprocess.php">
  <fieldset>
    <legend>Login</legend>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">User ID </label>
      <input type="text" name= "fid"class="form-control" id="exampleInputPassword1" placeholder="Enter User ID " autocomplete="off" required>
    </div>

     <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Password</label>
      <input type="password"  name= "fpwd" class="form-control" id="exampleInputPassword1" placeholder="Enter passsword" autocomplete="off"required>
    </div><br><br>

    <button type="submit" class="btn btn-primary">Login</button><br><br>

  </fieldset>
</form>
</div>

<?php
    include 'footer.php';
?>