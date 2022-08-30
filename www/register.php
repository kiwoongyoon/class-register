<?php
require("./classes.php"); 
require("./dbconfig.php");

if(isset($_GET['delete_professor_id'])) {
    $sql = "DELETE FROM professor WHERE id=:delete_professor_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('delete_professor_id', $_GET['delete_professor_id']);
    // 교수 목록에서 지우고 싶은 교수를 선택해서 아이디(등록번호)로 찾아 지운다
}

if(isset($_GET['delete_student_id'])) {
    $sql = "DELETE FROM student WHERE id=:delete_student_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('delete_student_id', $_GET['delete_student_id']);
    // 학생 목록에서 지우고 싶은 학생을 선택해서 아이디(등록번호)로 찾아 지운다
}

if(isset($_GET['delete_subject_id'])) {
    $sql = "DELETE FROM subject WHERE id=:delete_subject_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('delete_subject_id', $_GET['delete_subject_id']);
    // 수업목록에서 지우고 싶은 수업을 선택해서 아이디(등록번호)로 찾아 지운다
}

if(isset($_POST['professor_name'])) {
    $sql = "INSERT INTO professor(name) VALUES (:professor_name);";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('professor_name', $_POST['professor_name']);
    // form method ->post로 하고 name을 professor_name으로 설정해야 한다 
}

if(isset($_POST['student_name'])) {
    $hashed_password = password_hash($_POST['student_password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO student(name, password) VALUES (:student_name, :hashed_password);";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('student_name', $_POST['student_name']);
    $stmt->bindParam('hashed_password', $hashed_password);
}

if(isset($_POST['subject_name'])) {
    $sql = "INSERT INTO subject(name, professor_id, max_seats) VALUES (:subject_name, :professor_id, :max_seats);";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('subject_name', $_POST['subject_name']);
    $stmt->bindParam('professor_id', $_POST['professor_id']);
    $stmt->bindParam('max_seats', $_POST['max_seats']);
}
if(count($_GET) || count($_POST)) {
    $stmt->execute();
    header('Location: '.$_SERVER['PHP_SELF']);
}
// ///////////////////////////////////////////////////////////////////////////////////////////
// 교수와 학생 그리고 개설과목들에 대한 배열을 각각 하나씩 만든다 
// 데이터 베이스에서 정보들을 다 갖고와서 class로 하나씩 만든다
// 그 이후에 교수목록 과목목록 학생목록에 다 나올 수 있게 foreach 를 이용한다 
$professors=[] ;
$sql ="SELECT id,name FROM professor;";
$stmt=$conn->prepare($sql);
$stmt->execute();
$result=$stmt->fetchAll();
foreach($result as $row){
    $professor=new Professor(); 
    $professor->id=$row['id'] ; 
    $professor->name=$row['name'] ;
    $professors[]=$professor; 

}
$students=[] ;
$sql ="SELECT id,name FROM student;";
$stmt=$conn->prepare($sql);
$stmt->execute() ;

$result =$stmt->fetchAll(); 

foreach($result as $row) {

    $student = new Student();
    $student->id = $row['id'];
    $student->name = $row['name'];

    $students []= $student;
}
$subjects = [];
$sql = "SELECT subject.id, subject.name AS subject_name, professor.name AS professor_name, subject.max_seats
        FROM subject
        JOIN professor ON professor.id=subject.professor_id
        
        ;";
// $result = $conn->query($sql);
$stmt=$conn->prepare($sql) ;
$stmt->execute() ; 
$result=$stmt->fetchAll() ; 

foreach($result as $row) {

    $subject = new Subject();
    $subject->id = $row['id'];
    $subject->name = $row['subject_name'];
    $subject->professor_name = $row['professor_name'];
    $subject->max_seats = $row['max_seats'];

    $subjects []= $subject;
}
///////////////////////////////////////////////////////////////////////
?>



<!DOCTYPE html>
<html>
    <?php include("head.php")?>
<body>
    <?php include("nav.php")?>
    <div class="container py-5">
    <h2>과목 목록</h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">과목 이름</th>
                <th scope="col">담당 교수</th>
                <th scope="col">수강 가능 인원</th>
                <th scope="col">과목 삭제</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($subjects as $subject) : ?>

        <tr>
             <th scope="row"><?=$subject->id?></th>
             <td><?=$subject->name?></td>
             <td><?=$subject->professor_name?></td>
             <td><?=$subject->max_seats?></td>
             <td><a href="?delete_subject_id=<?=$subject->id?>" class="btn btn-danger">삭제</a></td>
        </tr>

          <?php endforeach ?>
        </tbody>
    </table>
    
        <h2 class="mt-5">과목 추가</h2>
        <form action="" method="POST">
            <div class="input-group mb-2">
                <span class="input-group-text" id="basic-addon1">과목 이름</span>
                <input 
                name="subject_name" 
                 type="text"
                 id="sujnameadd"
                 class="form-control"
                 aria-describedby="basic-addon1"
                 aria-label="과목 이름을 입력해주세요"
                 placeholder="과목 이름을 입력해주세요.">
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" for="sujprofadd">담당 교수</span>
                <select name="professor_id" class="form-select" id="sujprofadd">
                    <option selected>담당 교수를 선택해주세요.</option>
                    <?php foreach($professors as $professor):?>
                        <option value="<?=$professor->id?>"><?=$professor->name?></option>
                    <?php endforeach ?>    
                </select>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="basic-addon2">수강 가능 인원</span>
                <input 
                 name="max_seats"
                 type="number"
                 class="form-control"
                 min="1"
                 id="sujnumadd"
                 aria-label="수강 가능 인원을 입력해주세요." aria-describedby="basic-addon2"
                 placeholder="수강 가능 인원을 입력하세요"/>
            </div>
            <button type="submit" class="btn btn-primary">과목 추가</button>
        </form>
    
        <h2 class="mt-5">교수 목록</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">교수 이름</th>
                    <th scope="col">교수 삭제</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($professors as $professor): ?>
                <tr>
                    <th scope="row"><?=$professor->id?></th>
                    <td><?=$professor->name?></td>
                    <td><a href="?delete_professor_id=<?=$professor->id?>" class="btn btn-danger">삭제</a></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    
        <h2 class="mt-5">교수 추가</h2>
        <form action="" method="POST">
            <div class="input-group mb-2">
                <span class="input-group-text">교수 이름</span>
                <input
                class="form-control"
                name="professor_name"
                type="text"
                id="addprofname"
                placeholder="교수 이름을 입력해주세요"
                />
            </div>
            <button type="submit" class="btn btn-primary">교수 추가</button>
              
            </form>
    
            
            <h2 class="mt-5">학생 목록</h2>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">학생 이름</th>
                    <th scope="col">학생 삭제</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student): ?>
                    <tr>
                        <th scope="row"><?=$student->id?></th>
                        <td><?=$student->name?></td>
                    <td><a href="?delete_student_id=<?=$student->id?>" class="btn btn-danger">삭제</a></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    
    
        <h2 class="mt-5">학생 추가</h2>
        <form action="" method="POST">
            <div class="input-group mb-2">
                <span class="input-group-text" for="addstudname">학생 이름</span>
                <input
                name="student_name"
                type="text"
                class="form-control"
                id="addstudname"
                placeholder="학생 이름을 입력해주세요."
                aria-label="학생 이름을 입력해주세요."
                />
            </div>
            
            <div class="input-group mb-2">
                <span for="addstudpw" class="input-group-text">비밀번호</span>
                <input name="student_password" type="password" class="form-control" placeholder="비밀번호를 입력하세요">
            </div>
            
            <button type="submit" class="btn btn-primary">학생 추가</button>
            
            
        </form>
    </div>
        
    </body>
</html>
