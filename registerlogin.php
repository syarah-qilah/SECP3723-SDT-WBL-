<?php
    include 'header.php';
?>

<div class= "container">

<br><p class="text-success">Registration successful.Please login</p>

<form method="POST" action="loginprocess.php">
  <fieldset>
    <legend>Login</legend>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">User ID</label>
      <input type="text" name="fid" class="form-control" id="exampleInputPassword1" placeholder="Please write your full official name " autocomplete="off" required>
    </div>

     <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Password</label>
      <input type="password" name="fpwd" class="form-control" id="exampleInputPassword1" placeholder=" Enterpasssword" autocomplete="off"required>
    </div>

  
    <button type="submit" class="btn btn-primary">Login</button><br><br>


  </fieldset>x med
</form>
</div>

<?php
    include 'footer.php';
?>