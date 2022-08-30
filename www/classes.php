<?php

class Professor{
    public $id; 
    public $name ;
    
}
class Student {
    public $id;
    public $name;
}

class Subject {
    public $id;
    public $name;
    public $professor_name;
    public $max_seats;
    public $student_id;
    public $total_students;
}