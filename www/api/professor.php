

<?php
//Use requireonce to call the file once YOU NEED
class Controller extends RestApiController {

    function create() {
        
        require_once("../dbconfig.php");

        $name = $_POST['name'];
        
        $sql = "INSERT INTO professor(name) VALUES (:name);";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('name', $name);
        $stmt->execute();

        return true;

    }

    function read($id) {
        require_once("../dbconfig.php");
        
        $sql = "SELECT id, name FROM professor WHERE id=:id;";
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
        
        $sql = "DELETE FROM professor WHERE id=:id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return true;
    }

}