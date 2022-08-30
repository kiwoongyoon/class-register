<?php

class Controller extends RestApiController {

    function create() {

        require_once("../dbconfig.php");

        $name = $_POST['name'];
        $professor_id = $_POST['professor_id'];
        $max_seats = $_POST['max_seats'];
        

        $sql = "INSERT INTO subject(name, professor_id, max_seats) VALUES (:name, :professor_id, :max_seats);";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('professor_id', $professor_id);
        $stmt->bindParam('max_seats', $max_seats);
        $stmt->execute();

        return true;

    }

    function read($id) {
        require_once("../dbconfig.php");
        
        $sql = "SELECT id, name, max_seats FROM subject WHERE id=:id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    function update($id) {
        throw new Exception("Not Implemented");
    
    }

    function delete($id) {
        require_once("../dbconfig.php");
        
        $sql = "DELETE FROM subject WHERE id=:id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return true;
    }

}   