<?php
class connect
{

    private const host_name = "localhost";
    private const user_name = "root";
    private const password = "";
    private const db = "courses-platform";

    private $con;

    public function __construct()
    {

        $this->con = mysqli_connect(
            self::host_name,
            self::user_name,
            self::password,
            self::db
        );
    }

    public function insert(array $post, $table): bool
    {
        $clos = [];
        $values = [];
        foreach ($post as $key => $value) {
            $clos[] = $key;
            $values[] = "'" . $value . "'";
        }
        $closstring = implode(',', $clos);
        $valuesstring = implode(',', $values);
        // INSERT INTO students ($closstring) VALUES ($valuesstring)
        if ($this->con->query("INSERT INTO $table ($closstring) VALUES ($valuesstring)"))
            return true;
        return false;
    }


    public function select(string $table): array
    {
        $rows = $this->con->query("select * from $table");
        $data = [];
        if ($rows->num_rows > 0) {
            // convert from sql table to php array
            $data = $rows->fetch_all(MYSQLI_ASSOC);
        }
        return $data;
    }


    public function selectone(string $table, int $id): array
    {
        $row = $this->con->query("SELECT * from $table where id = $id limit 1");
        $data = [];
        if ($row->num_rows > 0) {
            $data = $row->fetch_assoc();
            return $data;
        }
        return $data;
    }


    public function update(array $post, string $table, int $id): bool
    {
        print_r($post);
        $colvalue = [];
        foreach ($post as $key => $value) {
            $colvalue[] = "$key = '$value'";
        }
        // print "<br>";
        // print_r($colvalue);
        $colvaluestring = implode(',', $colvalue);
        // print "<br>";
        //print_r($colvaluestring);


        if ($this->con->query("UPDATE $table set $colvaluestring where id = $id"))
            return true;
        return false;
    }








    public function alert(string $text, string $color)
    {
        print "<div class='alert alert-$color'>$text</div>";
    }



    //    public function login(string $user_name, string $password)
    //    {
    //      $row = $this->con->query("SELECT * FROM users where user_name = '$user_name' and password = '$password' limit 1");
    //      $data = [];
    //      if($row->num_rows > 0)
    //      {
    //         $data = $row->fetch_assoc();

    //      }
    //      return  $data;
    //     }





    public function selectwhere(string $table, string $column, int $value): array
    {
        $rows = $this->con->query(
            "SELECT * FROM $table WHERE $column = $value"
        );

        $data = [];

        if ($rows->num_rows > 0) {
            $data = $rows->fetch_all(MYSQLI_ASSOC);
        }

        return $data;
    }









    public function delete(string $table, int $id): bool
    {
        if ($this->con->query("DELETE FROM $table WHERE id = $id"))
            return true;
        return false;
    }



    // ======= Teacher (START) =========

    public function countRecords(string $table, string $where = ""): int
    {
        $sql = "SELECT COUNT(*) as total FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }

        $result = $this->con->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int) $row['total'];
        }
        return 0;
    }

    // 2. دالة لاستعلامات الـ JOIN المخصصة (مفيدة لـ courses.php و students.php)
    public function customQuery(string $sql): array
    {
        $rows = $this->con->query($sql);
        $data = [];
        if ($rows && $rows->num_rows > 0) {
            $data = $rows->fetch_all(MYSQLI_ASSOC);
        }
        return $data;
    }



    // ======= Teacher (END) =========
    // ======= Admin / JOIN Queries =========

    // 1. جلب الكورسات مع المدرس، القسم، وعدد الدروس والطلاب
    public function getCoursesWithDetails(): array
    {
        $sql = "SELECT courses.*, 
                       users.name AS teacher_name, 
                       categories.name AS category_name,
                       (SELECT COUNT(*) FROM lessons WHERE lessons.course_id = courses.id) AS total_lessons,
                       (SELECT COUNT(*) FROM enrollments WHERE enrollments.course_id = courses.id) AS total_students
                FROM courses 
                LEFT JOIN users ON courses.teacher_id = users.id 
                LEFT JOIN categories ON courses.category_id = categories.id 
                ORDER BY courses.id DESC";

        return $this->customQuery($sql);
    }

    // 2. جلب التصنيفات مع عدد الكورسات التابعة لكل قسم
    public function getCategoriesWithCount(): array
    {
        $sql = "SELECT categories.*, 
                       COUNT(courses.id) AS total_courses
                FROM categories
                LEFT JOIN courses ON courses.category_id = categories.id
                GROUP BY categories.id
                ORDER BY categories.id DESC";

        return $this->customQuery($sql);
    }

    // 3. جلب التقييمات مع اسم الطالب وعنوان الكورس
    public function getReviewsWithDetails(): array
    {
        $sql = "SELECT reviews.*, 
                       users.name AS student_name, 
                       courses.title AS course_title
                FROM reviews
                LEFT JOIN users ON reviews.student_id = users.id
                LEFT JOIN courses ON reviews.course_id = courses.id
                ORDER BY reviews.id DESC";

        return $this->customQuery($sql);
    }

    public function getError(): string
    {
        return $this->con ? $this->con->error : '';
    }
}
