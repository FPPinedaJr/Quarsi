<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'submit') {
            $idevent = $_POST['idevent'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $log_time = $_POST['log_time'];

            $stmt = $pdo->prepare("
                UPDATE event
                SET name=:name, date=:date, log_time=:log_time
                WHERE idevent=:idevent
            ");

            $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':log_time', $log_time, PDO::PARAM_INT);
            if ($stmt->execute()) {
                if ($stmt->rowCount() !== 1) {
                    throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
                }
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

            if (!($stmt->execute())) {
                echo "Error updating event. Please try again.";
                exit();
            }

            $stmt = $pdo->prepare("
                DELETE FROM attendance
                WHERE event=:idevent
            ");

            $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);

            if (!($stmt->execute())) {
                echo "Error updating attendance. Please try again.";
                exit();
            }

            if ($stmt->rowCount() !== 1) {
                throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
            }

            header("Location: ../events.php");
            exit();

        } else if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $date = $_POST['date'];
            $stmt = $pdo->prepare("
                INSERT INTO event (name, date)
                VALUES (:name, :date)
            ");

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);

            if ($stmt->execute()) {
                if ($stmt->rowCount() !== 1) {
                    throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
                }
                header("Location: ../events.php");
                exit();
            } else {
                echo "Error adding event. Please try again.";
            }

        }
    }
}