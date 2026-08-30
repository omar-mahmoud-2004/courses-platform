<?php

include "../includes/header.php";
include "../connect.php";

$objCon = new connect();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Course Details - Developer Notes</title>

    <style>
        body {
            background-color: #f8fafc;
        }

        .details-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .details-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .details-header h1 {
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .details-header p {
            font-size: 17px;
            color: #64748b;
        }

        .info-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .info-box h2 {
            font-size: 22px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 15px;
        }

        .info-box p {
            color: #475569;
            font-size: 16px;
            line-height: 1.8;
        }

        .code-box {
            background-color: #0f172a;
            color: #e2e8f0;
            padding: 18px;
            border-radius: 12px;
            margin-top: 15px;
            font-family: Consolas, monospace;
            line-height: 1.8;
            overflow-x: auto;
        }

        .steps {
            counter-reset: step;
        }

        .step {
            position: relative;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px 22px 22px 65px;
            margin-bottom: 15px;
        }

        .step::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 20px;
            top: 20px;

            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            background-color: #2563eb;
            color: white;

            border-radius: 50%;
            font-weight: bold;
        }

        .step h3 {
            margin-bottom: 8px;
            color: #0f172a;
        }

        .step p {
            margin: 0;
            color: #64748b;
            line-height: 1.7;
        }

        .important {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 16px;
            padding: 22px;
            margin-top: 25px;
        }

        .important h2 {
            margin-bottom: 10px;
            font-size: 20px;
        }
    </style>

</head>


<body>


    <div class="details-container">


        <!-- Page Header -->

        <div class="details-header">

            <h1>
                Course Details
            </h1>

            <p>
                Instructions for the next developer
            </p>

        </div>


        <!-- Main Task -->

        <div class="info-box">

            <h2>
                المطلوب تنفيذُه في هذه الصفحة
            </h2>

            <p>
                صفحة <strong>details.php</strong> مسؤولة عن عرض تفاصيل الكورس
                الذي اختاره المستخدم من صفحة Courses.
                المطلوب ربط الصفحة بالـ ID الخاص بالكورس والـ ID الخاص بالـ Category.
            </p>

        </div>


        <!-- Course ID -->

        <div class="info-box">

            <h2>
                1. استقبال Course ID
            </h2>

            <p>
                من صفحة <strong>courses/index.php</strong> يوجد زر
                View Course.
                هذا الزر يرسل ID الكورس إلى صفحة التفاصيل.
            </p>

            <div class="code-box">
                details.php?id=<?= $course['id'] ?>
            </div>

            <p style="margin-top: 15px;">
                في هذه الصفحة يتم استقبال الـ ID من الرابط باستخدام:
            </p>

            <div class="code-box">
                $course_id = (int) ($_GET['id'] ?? 0);
            </div>

        </div>


        <!-- Get Course -->

        <div class="info-box">

            <h2>
                2. إحضار بيانات الكورس
            </h2>

            <p>
                بعد الحصول على Course ID، استخدم دالة
                <strong>selectone()</strong>
                لإحضار الكورس المطلوب من جدول
                <strong>courses</strong>.
            </p>

            <div class="code-box">
                $course = $objCon->selectone("courses", $course_id);
            </div>

            <p style="margin-top: 15px;">
                بعد ذلك يمكن عرض بيانات الكورس مثل:
            </p>

            <div class="code-box">
                title<br>
                description<br>
                image<br>
                price<br>
                category_id<br>
                teacher_id<br>
                created_at
            </div>

        </div>


        <!-- Category ID -->

        <div class="info-box">

            <h2>
                3. معرفة Category ID
            </h2>

            <p>
                الكورس يحتوي على
                <strong>category_id</strong>.
                استخدم هذه القيمة لمعرفة الـ Category التي ينتمي إليها الكورس.
            </p>

            <div class="code-box">
                $category_id = (int) $course['category_id'];
            </div>

            <p style="margin-top: 15px;">
                بعد ذلك يمكن استخدام الـ Category ID لإحضار اسم الـ Category
                من جدول <strong>categories</strong>.
            </p>

            <div class="code-box">
                $category = $objCon->selectone(
                "categories",
                $category_id
                );
            </div>

        </div>


        <!-- Display -->

        <div class="info-box">

            <h2>
                4. عرض تفاصيل الكورس
            </h2>

            <p>
                بعد إحضار بيانات الكورس والـ Category،
                يتم تصميم صفحة التفاصيل لعرض المعلومات بشكل مرتب.
            </p>

            <div class="code-box">
                Course Image<br>
                Course Title<br>
                Course Description<br>
                Category Name<br>
                Price<br>
                Created At
            </div>

        </div>


        <!-- Steps -->

        <div class="info-box">

            <h2>
                خطوات التنفيذ
            </h2>

            <div class="steps">


                <div class="step">

                    <h3>
                        استقبل ID الكورس
                    </h3>

                    <p>
                        خذ الـ ID الموجود في الرابط باستخدام
                        <strong>$_GET['id']</strong>.
                    </p>

                </div>


                <div class="step">

                    <h3>
                        هات الكورس من Database
                    </h3>

                    <p>
                        استخدم
                        <strong>selectone("courses", $course_id)</strong>
                        للحصول على بيانات الكورس.
                    </p>

                </div>


                <div class="step">

                    <h3>
                        استخرج category_id
                    </h3>

                    <p>
                        خذ قيمة <strong>category_id</strong>
                        من الكورس.
                    </p>

                </div>


                <div class="step">

                    <h3>
                        هات بيانات الـ Category
                    </h3>

                    <p>
                        استخدم category_id للحصول على اسم الـ Category
                        من جدول categories.
                    </p>

                </div>


                <div class="step">

                    <h3>
                        اعرض التفاصيل
                    </h3>

                    <p>
                        اعرض بيانات الكورس في تصميم مناسب للمستخدم.
                    </p>

                </div>


            </div>

        </div>


        <!-- Important -->

        <div class="important">

            <h2>
                ملاحظة مهمة
            </h2>

            <p>
                لا تنشئ Course جديد ولا Category جديد في هذه الصفحة.
                الصفحة وظيفتها فقط استقبال الـ ID،
                جلب البيانات الموجودة بالفعل من Database،
                ثم عرض تفاصيل الكورس.
            </p>

        </div>


        <!-- Final Flow -->

        <div class="info-box">

            <h2>
                شكل الربط النهائي
            </h2>

            <div class="code-box">
                Categories
                ↓
                category_id
                ↓
                Courses
                ↓
                course_id
                ↓
                details.php?id=course_id
                ↓
                selectone("courses", course_id)
                ↓
                category_id
                ↓
                selectone("categories", category_id)
                ↓
                عرض تفاصيل الكورس
            </div>

        </div>


    </div>


</body>

</html>


<?php

include "../includes/footer.php";

?>