<?php
session_start();
if($_SESSION['user_namee']){

    include_once("root/config.php");
    include_once("header.php");
    
    include_once("slider/slider.php");
    
    
    include_once("store.php");
    ?>
    
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <img src="img/security-seals.png" class="img-fluid" alt="Image">
            </div>
        </div>
    </div>
    
    <?php 
    include_once("footer.php");
}
 else {
   
    header("Location: http://localhost/notesjila/login.php"); // Redirect to login page
    exit();
 }
 
?>