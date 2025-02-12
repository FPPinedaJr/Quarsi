<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update') {
            $iduser = $_POST['iduser'];
            $f_name = mb_convert_case(trim($_POST['f_name']), MB_CASE_TITLE, "UTF-8");
            $l_name = mb_convert_case(trim($_POST['l_name']), MB_CASE_TITLE, "UTF-8");            
            $organization = $_POST['program'];
            $student_no = trim($_POST['student_no']);
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = trim($_POST['email']);
            $user_type = $_POST['user_type'];
            $profile_pic = NULL;
            if (isset($_FILES['profile_pic'])){$profile_pic = $_FILES['profile_pic'];}
            $user = 1;
            $img_content = "";
            $hidden_profile = $_POST['hidden_profile'];

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
                $img_content = base64_decode($hidden_profile);
            }

            if ($user_type == 0) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=?, f_name=?, l_name=?, organization=?,
                        year=?, block=?, email=?, profile_pic=?, is_officer=0, is_superuser=0, is_admin=0
                    WHERE iduser=?
                ");
                $stmt->execute([$student_no, $f_name, $l_name, $organization, $year, $block, $email, $img_content, $iduser]);
                echo $iduser;
                exit();

            } else if ($user_type == 1) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=?, f_name=?, l_name=?, organization=?,
                        year=?, block=?, email=?, profile_pic=?, is_officer=1, is_superuser=0, is_admin=0
                    WHERE iduser=?
                    ");
                $stmt->execute([$student_no, $f_name, $l_name, $organization, $year, $block, $email, $img_content, $iduser]);
                echo $iduser;
                exit();
    
                } else if ($user_type == 2) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=?, f_name=?, l_name=?, organization=?,
                        year=?, block=?, email=?, profile_pic=?, is_superuser=1, is_officer=1, is_admin=0
                    WHERE iduser=?
                    ");
                $stmt->execute([$student_no, $f_name, $l_name, $organization, $year, $block, $email, $img_content, $iduser]);
                echo $iduser;
                exit();

            } else if ($user_type == 3) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=?, f_name=?, l_name=?, organization=?,
                        year=?, block=?, email=?, profile_pic=?, is_superuser=0, is_officer=0, is_admin=1
                    WHERE iduser=?
                    ");
                $stmt->execute([$student_no, $f_name, $l_name, $organization, $year, $block, $email, $img_content, $iduser]);
                echo $iduser;
                exit();
            }

        } else if ($_POST['action'] == 'delete') {
            $iduser = $_POST['iduser'];

            $stmt = $pdo->prepare("
                DELETE FROM user
                WHERE iduser=:iduser
            ");
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ../student.php");
                exit();
            } else {
                echo "Error deleting user. Please try again.";
            }
        } else if ($_POST['action'] == 'add') {
            $f_name = $_POST['f_name'];
            $l_name = $_POST['l_name'];
            $organization = $_POST['program'];
            $student_no = $_POST['student_no'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = $_POST['email'];
            $password = $_POST['student_no'];
            $profile_pic = $_FILES['profile_pic'];
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
                    INSERT INTO user (student_no, f_name, l_name, organization, year, block, email, password, profile_pic)
                    VALUES (?, ?, ?, ?, ?, ?, ?, SHA2(?, 256), ?)
                ");
            $stmt->execute([$student_no, $f_name, $l_name, $organization, $year, $block, $email, $student_no, $img_content]);
            echo "success";
            header("Location: ../student.php");
        }
    }
}
