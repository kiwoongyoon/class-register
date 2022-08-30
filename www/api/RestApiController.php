<?php
//$_SERVER['HTTP_REFERER'] stores previous url so it can make user to come back to the page 
//Make Restapicontroller controls all the api requests from user
class RestApiController {

    private $id = null;

    function __construct($id=null)
    {
        $this->id = $id;
    }

    function respond() {
        $method = $_SERVER["REQUEST_METHOD"];

        if($method == "GET") {

            var_dump($this->read($this->id));

        }
        else if($method == "POST") {
            if($this->create()) {
                if(isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: '.$_SERVER['HTTP_REFERER']);
                }
                else {
                    echo("요청에 성공했습니다.");
                }
            } else {
                echo("요청에 실패했습니다.");
            }
        }
        else if($method == "PATCH") {

            if($this->update($this->id)) {
                if(isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: '.$_SERVER['HTTP_REFERER']);
                }
                else {
                    echo("요청에 성공했습니다.");
                }
            } else {
                echo("요청에 실패했습니다.");
            }
            

        } else if($method == "DELETE") {

            if($this->delete($this->id)) {
                if(isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: '.$_SERVER['HTTP_REFERER']);
                }
                else {
                    echo("요청에 성공했습니다.");
                }
            } else {
                echo("요청에 실패했습니다.");
            }
            
        }
    }

    function create() {
        throw new Exception("Not Implemented");
    }

    function read($id) {
        throw new Exception("Not Implemented");
    }

    function update($id) {
        throw new Exception("Not Implemented");
    
    }

    function delete($id) {
        throw new Exception("Not Implemented");
    }

}

//url을 부분적으로 나누어서 각각에 대해서 정보를 갖고오게 만든다 
//PHP_URL_PATH를 이용하면 파일의 경로를 갖고 오게 된다 
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);

if(count($uri_segments) < 3) {
    throw new Exception("Too few arguments");
}

$resource = $uri_segments[2];

if(!file_exists("{$resource}.php")) {
    die("Invalid Request");
}

require_once("{$resource}.php");

$controller = new Controller(count($uri_segments) > 3 ? $uri_segments[3] : null);
$controller->respond();