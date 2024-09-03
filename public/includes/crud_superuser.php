<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'submit') {
            $iduser = $_POST['iduser'];
            $f_name = $_POST['f_name'];
            $l_name = $_POST['l_name'];
            $organization = $_POST['program'];
            $student_no = $_POST['student_no'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = $_POST['email'];
            $user_type = $_POST['user_type'];
            $total_points = $_POST['total_points'];
            $profile_pic = $_FILES['profile_pic'];
            $user = 1;

            $img_content = ""; 

            if (!empty($profile_pic["tmp_name"])) {
                $source = $profile_pic["tmp_name"];
                list($width, $height) = getimagesize($source);

                $max_dimension = 200; // max resolution 
                $resize_ratio = min($max_dimension / $width, $max_dimension / $height);

                $new_width = $width * $resize_ratio;
                $new_height = $height * $resize_ratio;

                $info = getimagesize($source);
                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($source);
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($source);
                }

                $tn = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($tn, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                ob_start();
                imagejpeg($tn, NULL, 60); 
                $img_content = ob_get_clean();
                } else {
                    $img_content = file_get_contents("../assets/images/default_pic.jpg");
                }
            
            if ($user_type == 0) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, organization=:organization,
                        year=:year, block=:block, email=:email, total_points=:total_points, profile_pic=:profile_pic
                    WHERE iduser=:iduser
                ");
                $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                $stmt->bindParam(':profile_pic', $img_content, PDO::PARAM_LOB);

            } else if ($user_type == 1){
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, organization=:organization,
                        year=:year, block=:block, email=:email, is_officer=:user, total_points=:total_points, profile_pic=:profile_pic
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
                    $stmt->bindParam(':profile_pic', $img_content, PDO::PARAM_LOB);

            } else if ($user_type == 2) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, organization=:organization,
                        year=:year, block=:block, email=:email, is_superuser=:user, total_points=:total_points, profile_pic=:profile_pic
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
                    $stmt->bindParam(':profile_pic', $img_content, PDO::PARAM_LOB);

            } else if ($user_type == 3) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, organization=:organization,
                        year=:year, block=:block, email=:email, is_admin=:user, total_points=:total_points, profile_pic=:profile_pic
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
                    $stmt->bindParam(':profile_pic', $img_content, PDO::PARAM_LOB);
            }
            if ($stmt->execute()) {
                header("Location: ../superuser.php");
                exit(); 
            } else {
                echo "Error updating user. Please try again.";
            }

        } else if ($_POST['action'] == 'delete') {
            $iduser = $_POST['iduser'];

            $stmt = $pdo->prepare("
                DELETE FROM user
                WHERE iduser=:iduser
            ");
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ../superuser.php");
                exit(); 
            } else {
                echo "Error deleting user. Please try again.";
            }
        } else if ($_POST['action'] == 'add') {
            $iduser = $_POST['iduser'];
            $f_name = $_POST['f_name'];
            $l_name = $_POST['l_name'];
            $organization = $_POST['program'];
            $student_no = $_POST['student_no'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = $_POST['email'];
            $password = $_POST['student_no'];
            $profile_pic = $_FILES['profile_pic'];
            $user = 1;

            $img_content = ""; 

            if (!empty($profile_pic["tmp_name"])) {
                $source = $profile_pic["tmp_name"];
                list($width, $height) = getimagesize($source);

                $max_dimension = 200; // max resolution 
                $resize_ratio = min($max_dimension / $width, $max_dimension / $height);

                $new_width = $width * $resize_ratio;
                $new_height = $height * $resize_ratio;

                $info = getimagesize($source);
                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($source);
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($source);
                }

                $tn = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($tn, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                ob_start();
                imagejpeg($tn, NULL, 60); 
                $img_content = ob_get_clean();
                } else {
                    $img_content = file_get_contents("../assets/images/default_pic.jpg");
                }
            
            $stmt = $pdo->prepare("
                SELECT * FROM user 
                WHERE student_no=:student_no
            ");
            $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
    
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row) {
                header("location: ../error_user.php");
                exit();
            }
    
            $stmt = $pdo->prepare("
                INSERT INTO user (student_no, f_name, l_name, organization, year, block, email, password, is_superuser, profile_pic)
                VALUES (:student_no, :f_name, :l_name, :organization, :year, :block, :email, SHA2(:password, 256), :user, :profile_pic)
            ");

            $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
            $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
            $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
            $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':block', $block, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':user', $user, PDO::PARAM_INT);
            $stmt->bindParam(':profile_pic', $img_content, PDO::PARAM_LOB);
            }

            if ($stmt->execute()) {
                header("Location: ../superuser.php");
                exit(); 
            } else {
                echo "Error adding user. Please try again.";
            }
    }
}