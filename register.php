<?php
    include 'header.php';
    include'dbconnect.php';
?>

<div class= "container">

<form method="POST" action="registerprocess.php">
  <fieldset>
    <legend>Registration Form</legend>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Full name</label>
      <input type="text" name="fname" class="form-control" id="exampleInputPassword1" placeholder="Please write your full official name " autocomplete="off" required>
    </div>

     <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Password</label>
      <input type="password" name="fpwd" class="form-control" id="exampleInputPassword1" placeholder="Please create strong passsword" autocomplete="off"required>
    </div>

    <div>
      <label for="exampleInputEmail1" class="form-label mt-4">Email address</label>
      <input type="email" name="femail" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"required>
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>

    <div>
      <label for="exampleSelect1" class="form-label mt-4">Phone number operator</label>
      <select class="form-select" name="foperator" id="exampleSelect1">
        <option>011</option>
        <option>012</option>
        <option>013</option>
        <option>014</option>
        <option>015</option>
      </select>
    </div>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Phone number</label>
      <input type="text" name="fphone" class="form-control" id="exampleInputPassword1" placeholder="Please write without dash " autocomplete="off"required>
    </div>

    <div>
      <label for="exampleSelect1" class="form-label mt-4">Gender</label>
      <select class="form-select" name="fgender" id="exampleSelect1">
        <option>FEMALE</option>
        <option>MALE</option>
        </select>
    </div>

     <div>
      <label for="exampleSelect1" class="form-label mt-4">Programme</label>
      <select class="form-select"  name="fprog" id="exampleSelect1">
        <option>SECPH</option>
        <option>SECJH</option>
        <option>FAIAI</option>
      </select>
    </div>

    <div>
      <label for="exampleSelect1" class="form-label mt-4">Residential College</label>
      <?php
      $sql="SELECT*FROM tb_residential";
      $result =mysqli_query($con,$sql);

      echo"<select class='form-select' name='fcol' id='exampleSelect1'>";
        while($row=mysqli_fetch_array($result))
        {
          echo"<option value='".$row['r_id']."'>".$row['r_name']."</option>";
        }
      echo"</select>";

      ?>

    </div><br><br><br>

    <button type="submit" class="btn btn-primary">Register</button>
    <button type="reset" class="btn btn-warning">Clear input</button><br><br>

  </fieldset>
</form>
</div>

<?php
    include 'footer.php';
?>