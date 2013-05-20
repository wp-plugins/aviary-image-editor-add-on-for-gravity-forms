<?php
session_start();
if(isset($_POST['action'])){
  switch($_POST['action']){
    case 'set':
      $_SESSION['ig_user'] = $_POST['ig_user'];
      break;
    case 'del':
      unset($_SESSION['ig_user']);
      echo json_encode( array( 
        'code' => 'OK'
      ) );
      break;
  }
}
exit;
?>