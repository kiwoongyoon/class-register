<?php

class Controller extends RestApiController {

    function create() {

        require_once("../dbconfig.php");

        $name = $_POST['name'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO student(name, password) VALUES (:name, :password);";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('password', $password);
        $stmt->execute();

        return true;

    }

    function read($id) {
        require_once("../dbconfig.php");
        
        $sql = "SELECT id, name FROM student WHERE id=:id;";
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
        
        $sql = "DELETE FROM student WHERE id=:id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return true;
    }

}