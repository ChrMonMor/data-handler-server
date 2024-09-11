<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
        require_once '../config.php';
        
        
        $sql = "INSERT INTO `tempData` (`readings`, ip) VALUES ('".$_POST['readings']."', '".$_POST['ip']."')";
        try {

            $pdo->exec($sql);

        } catch (Exception $ex) {

            echo json_encode($ex->getMessage());

        } finally {
                
            unset($pdo);

        }
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        require_once "../config.php";

        $sql = "SELECT * FROM `humidData`";
        $data = [];

        try {

            $stmt = $pdo->query($sql);
                
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $data[] = $row;

            }
                
            echo json_encode($data);

        } catch (Exception $ex) {

            echo json_encode($ex->getMessage());

        } finally {
                
            unset($stmt);
            unset($pdo);

        }    
    }
?>