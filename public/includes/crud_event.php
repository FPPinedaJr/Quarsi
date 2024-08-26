<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'submit') {
            $idevent = $_POST['idevent'];
            $name = $_POST['name'];
            $organization = $_POST['organization'];            
            $date = $_POST['date'];
            $set_points = $_POST['set_points'];
            $status = $_POST['status'];
            $log_time = $_POST['log_time'];
            
            $stmt = $pdo->prepare("
                UPDATE event
                SET name=:name, organization=:organization, date=:date, set_points=:set_points, 
                is_active=:status, log_time=:log_time
                WHERE idevent=:idevent
            ");

            $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':log_time', $log_time, PDO::PARAM_INT);
            $stmt->bindParam(':set_points', $set_points, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: ../events.php");
                exit(); 
            } else {
                echo "Error updating event. Please try again.";
            }

        } else if ($_POST['action'] == 'delete') {
            $idevent = $_POST['idevent'];

            $stmt = $pdo->prepare("
                DELETE FROM event
                WHERE idevent=:idevent
            ");

            $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ../events.php");
                exit(); 
            } else {
                echo "Error updating event. Please try again.";
            }

        } else if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $organization = $_POST['organization'];            
            $date = $_POST['date'];
            $set_points = $_POST['set_points'];
            $status = $_POST['status'];
            $log_time = $_POST['log_time'];
            
            $stmt = $pdo->prepare("
                INSERT INTO event (name, organization, date, set_points, is_active, log_time)
                VALUES (:name, :organization, :date, :set_points, :status, :log_time)
            ");

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':organization', $organization, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':log_time', $log_time, PDO::PARAM_INT);
            $stmt->bindParam(':set_points', $set_points, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: ../events.php");
                exit(); 
            } else {
                echo "Error adding event. Please try again.";
            }

        }
    }
}