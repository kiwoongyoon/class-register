<?php
require("./dbconfig.php");
require("./classes.php");

session_start();
// 세션 시작을 위해서 student_id를 설정한다 
//아래의 로그인이 잘 된 경우에 $student_id를 만들게 된다 
$student_id = $_SESSION['student_id'] ?? -1;

if($student_id>0){
    // 제대로 설정이 되었는지 확인하는 부분
    $sql="SELECT * FROM student WHERE id=${student_id};"; 
    $stmt=$conn->prepare($sql) ;
    $stmt->execute() ;

    $student_info=$stmt->fetch() ;
    //
}
if(isset($_POST['name'])){
    $name=$_POST['name']; 
    if(empty($name)){
        session_unset(); 
        //여기는 만약 로그아웃 버튼을 누른 경우에 작동한다 
    }
    else{
        //만약 첫화면의 로그인이 성공하면 비밀번호까지 확인한다 
        //비밀번호 일치함이 확인되면 세션의 student_id를 해당 학생의 아이디(등록순서)로 정해준다 
        $password=$_POST['password'] ;
        $sql="SELECT * FROM student WHERE name LIKE :name;"; 
        $stmt= $conn->prepare($sql) ;
        $stmt->bindParam(':name',$name) ;
        $stmt->execute();
        $result=$stmt->fetch() ;
        if($result){
            if(password_verify($password,$result['password'])){
                $_SESSION['student_id']=$result['id'];
            }
        }
    }
}
// if(isset($_POST['student_id'])) {
//     $_SESSION['student_id'] = $_POST['student_id'];
// }
if(isset($_GET['unapply_subject_id'])) {
    $sql = "DELETE FROM student_subject WHERE student_id = :student_id AND subject_id=:unapply_subject_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('student_id', $student_id);
    $stmt->bindParam('unapply_subject_id', $_GET['unapply_subject_id']);
    $stmt->execute();
    // 만약 학생이 수강신청한 과목을 다시 수강취소 하고 싶을 경우에 수강취소 버튼으로 과목의 id를 받는다 
}

if(isset($_GET['apply_subject_id'])) {
    $sql = "INSERT INTO student_subject(subject_id, student_id) VALUES ( :apply_subject_id, :student_id );";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('apply_subject_id', $_GET['apply_subject_id']);
    $stmt->bindParam('student_id', $student_id);
    $stmt->execute();
    // 과목들의 목록에서 수강을 희망하는 과목을 선택하여 그 과목의 아이디를 받아온다. 
    
}

if(count($_GET) || count($_POST)) {
    
    header('Location: '.$_SERVER['PHP_SELF']);
    
}

// $sql = "SELECT id, name  FROM student;";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     // output data of each row
//     while($row = $result->fetch_assoc()) {
//         $student = new Student();
//         $student->id = $row['id'];
//         $student->name = $row['name'];
//         $students []= $student;
//     }
// }

$subjects = [];
// 과목 배열을 하나 만들고 학생_과목 데이터와 연동하여 정보를 갖고 올 수 있도록 한다 
// 이후에 result 와 row로 만들어주고 배열에 하나씩 집어넣는다 
// total_students가 해당 과목을 수강신청한 학생의 수이다 
$sql = "SELECT subject.id, subject.name AS subject_name, professor.name AS professor_name, subject.max_seats, s1.student_id AS student_id, COUNT(s2.id) AS total_students
FROM subject
-- count(s2.subject_id)로 해도 문제가 없는데 이유는 아마도 각 과목의 고유 아이디를 기준으로 카운트를 하기 때문 일 것이다. 
JOIN professor ON professor.id = subject.professor_id
LEFT OUTER JOIN student_subject AS s1 ON s1.subject_id = subject.id AND s1.student_id = :student_id
LEFT OUTER JOIN student_subject AS s2 ON s2.subject_id = subject.id

GROUP BY subject.id 
ORDER BY subject.id
-- 정렬순서를 과목의 아이디 순으로 해주어서 8번과목을 취소했다가 다시 신청해도 9번, 10번 과목보다는 상단에 위치하도록 한다 
--    만약 정렬 순서를 max_seats로 바꿔주면 수강가능인원의 오름 차순으로 모든 과목의 정렬이 바뀐다 

;";
$stmt=$conn->prepare($sql) ;
$stmt->bindParam('student_id', $student_id) ;
$stmt->execute();


$result = $stmt->fetchAll(); 
foreach($result as $row) {
    // output data of each row
        $subject = new Subject();
        $subject->id = $row['id'];
        $subject->name = $row['subject_name'];
        $subject->professor_name = $row['professor_name'];
        $subject->max_seats = $row['max_seats'];
        $subject->student_id = $row['student_id'];
        $subject->total_students = $row['total_students'];
        $subjects []= $subject;
}

////////////////////////////////아래는 html 입니다 /////////////////////////////////////////////////////


?>
<!DOCTYPE html>
<html>
    <?php include("head.php")?>
<body>
    <?php include("nav.php")?>


    <!-- 학생을 선택해서 그 학생의 정보를 보여주자 -->
    
    <div class="applycontainer">
    <!-- <div class="apply_studentselect">
        <label>학생선택</label>
        <form id="studentSelectForm" method="POST" action="">
        <select name="student_id" class="std_select" onchange="onStudentChange()" >
            <option selected>학생을 선택하세요</option>
            <?php foreach($students as $student):?>
                <option value="<?=$student->id?>" <?=$student->id==$student_id? "selected": ""?>><?=$student->name?></option>
            <?php endforeach ?> 
        </select>
        </form>
    </div> -->
        
    <?php if($student_id<0):?>
        <div>
            <label>학생 로그인</label>
             <form id="studnetSelectForm" method="POST" action="">
                <input type="text" name="name" placeholder="이름">
                <input type="password" name="password" placeholder="비밀번호">
                <button type="submit" >로그인</button>
             </form>
        </div>
    <?php else :?>
    <div>
        <p><?=$student_info['name']?>님 환영해요</p>
        <form method="POST" action="">
            <input type="hidden" name="name" value="">
            <button type="submit" >로그아웃</button>
        </form>
        <!-- 여기서는 로그아웃 하면 name에 post로 아무것도 안 들어가서 세션이 종료가 된다.  -->
    </div>
    <?php endif ?>




    <?php if($student_id>=0):?>
<!--     
    여기부터는 학생이 선택된 경우 각각의 학생의 신청과목이 나와야해 -->
  
    
        <h2>개설 교과목</h2>
        <table class="container courses">
            <thead>
                <tr>
                    <th>#</th>
                    <th>과목이름</th>
                    <th>담당교수</th>
                    <th>수강가능인원</th>
                    <th>수강신청</th>
                </tr>
            </thead>
            <tbody>
            <!-- 개설된 과목들은 학생이 누구인지에 따라 상관없이 다 나와야해  -->
                <?php foreach($subjects as $subject): ?>
                    <tr>
                        <th><?=$subject->id?></th>
                        <td><?=$subject->name?></td>
                        <td><?=$subject->professor_name?></td>
                        <td><?=$subject->total_students?>/<?=$subject->max_seats?></td>
                        <td>
                            <?php if(!is_null($subject->student_id)):?>
                                <button disabled>신청완료</button>
                            <?php elseif($subject->total_students>=$subject->max_seats): ?>
                                <button>신청불가</button>
                            <?php else :?>
                                <a href="?apply_subject_id=<?=$subject->id?>" class="btn btn-primary">신청하기</a>
                            <?php endif ?>    
                        </td>
                    </tr>
                <?php endforeach ?>
                
            </tbody>
        </table>
    
    
    
        <h2>수강 신청 교과목</h2>
        <table class="container courses done">
            <thead>
                <tr>
                    <th>#</th>
                    <th>과목 이름</th>
                    <th>담당 교수</th>
                    <th>수강 취소</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $subject):?>
                    <?php if(is_null($subject->student_id))continue ?>
                    <tr>
                        <th><?=$subject->id?></th>
                        <td><?=$subject->name?></td>
                        <td><?=$subject->professor_name?></td>

                        <td><a href="?unapply_subject_id=<?=$subject->id?>" class="btn btn-danger">수강취소</a></td>
                    </tr>
                    <?php endforeach ?>
            </tbody>
        </table>   
        <?php endif ?>            
    </div>
   

<!-- 

    <script type="text/javascript">
        //학생 선택에 따라서 아래의 보이는 내용을 달리 하고싶으니 
        //select의 값의 변화를 갖고와야 한다. 
        function onStudentChange(){
            let form =document.getElementById("studentSelectForm"); 
            form.submit();
        }
    </script> -->

</body>
</html>