<?php
include_once("./connect_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = $_POST['student_no'];
    $eventid = $_POST['eventid'];
    $log_time = $_POST['log_time'];

    $stmt = $pdo->prepare("
        SELECT * FROM attendance 
        WHERE user = (SELECT iduser FROM user WHERE student_no LIKE :stud_no)
        AND event = :event
        ;
    ");
    $stmt->bindParam(':stud_no', $student_no, PDO::PARAM_STR);
    $stmt->bindParam(':event', $eventid, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if (!$row){
        echo 'error:unknown_user';
        exit();
    }


    $stmt = $pdo->prepare("
        UPDATE attendance 
        SET $log_time = ADDTIME(CURRENT_TIMESTAMP(), '08:00:00')
        WHERE user = (SELECT iduser FROM user WHERE student_no LIKE :stud_no)
        AND event = :event
        ;
    ");
    $stmt->bindParam(':stud_no', $student_no, PDO::PARAM_STR);
    $stmt->bindParam(':event', $eventid, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare("
    SELECT 
        profile_pic,
        CONCAT(f_name, ' ', l_name) AS full_name,
        student_no AS student_number,
        CONCAT('Year ', year, ' Block ', block) AS section
    FROM user
    WHERE iduser = (SELECT iduser FROM user WHERE student_no LIKE :stud_no);
    ");
    $stmt->bindParam(':stud_no', $student_no, PDO::PARAM_STR);
    $stmt->execute();

    $user_data = $stmt->fetch();
    $profile_pic_base64 = base64_encode($user_data['profile_pic']);
    echo '
     <div class="w-20 h-20 mt-3 overflow-hidden border border-gray-400 rounded-full">
        <img src="data:image/jpeg;base64,' . $profile_pic_base64 . '" alt="Profile Picture" class="object-cover w-full h-full">
    </div>
    <div class="ml-4 text-left">
        <em class="font-bold text-green-700">Logged:</em>
        <p id="full-name" class="text-lg font-bold " contenteditable="true">' . $user_data['full_name'] . '</p>
        <p id="student-number" class="text-sm " contenteditable="true">' . $user_data['student_number'] . '</p>
        <p id="section" class="" contenteditable="true">' . $user_data['section'] . '</p>
    </div>
    ';






}



