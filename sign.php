<?php
$servername = "localhost";
$port_no = 3306;
$username = "ankur";
$password = "ankur";
$myDB = "lms";
session_start();

try{
  $conn = new PDO("mysql:host=$servername;port=$port_no,dbname=$myDB",$username,$password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   # echo "Connected Successfully";
   
   
   // signup empolyee
   function validatePassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{7,}$/', $password);
}
   
   if(isset($_POST['type']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])){
    $newPassword = $_POST['password'];
    if(!validatePassword($newPassword)){
        header("Location: signup.php?type=".$_POST['type']."&error=wp");
        exit();
    }

    
    $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

     $sql = "INSERT INTO lms.users(Role, Name, Email_id, Password_) VALUES(:type, :name, :email, :password)";
     $stmt = $conn->prepare($sql);
     $stmt->execute(array(
     ':type' => $_POST['type'],
     ':name' => $_POST['name'],
     ':email' => $_POST['email'],
     ':password' => $newHashedPassword));
     header("Location: signin.php");
     exit();
   }
   
   // signin employee 
   
   if(isset($_POST['email']) && isset($_POST['password'])){
       $email = $_POST['email'];
       $password = $_POST['password'];
       $sql = "SELECT `Password_`, `Role` FROM lms.users WHERE `Email_id` = :email";
       $stmt = $conn->prepare($sql);
       $stmt->bindParam(':email', $email);
       $stmt->execute();
       if($stmt->rowCount() === 0){
        header("Location: signin.php?error=User does Not exists");
        exit();
       }else{

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        

        if (password_verify($password, $user['Password_'])) {
            // Redirect based on user type
            if ($user['Role'] === 'Student') {
                $sql2 = "SELECT `Roll_Number` FROM lms.student WHERE `Email_id` = :email";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindParam(':email', $email);
                $stmt2->execute();
                $user_s = $stmt2->fetch(PDO::FETCH_ASSOC);
                $_SESSION["student_id"] = $user_s['Roll_Number'];
                header("Location: stud_course_dashboard.php");
            } else {
                $sql3 = "SELECT `Instructor_id` FROM lms.instructor WHERE `Email_id` = :email";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bindParam(':email', $email);
                $stmt3->execute();
                $user_i = $stmt3->fetch(PDO::FETCH_ASSOC);
                $_SESSION["instructor_id"] = $user_i['Instructor_id'];
                header("Location: instr_course_dashboard.php");
            }
            exit();
        } else {
            header("Location: signin.php?error=".urlencode($password)."and".urlencode($user['Password_']));
            exit();
        }
       }
       }
   }catch(PDOException $e){
    echo "Connection failed:".$e->getMessage();
}
   
   
 ?>
   
   

