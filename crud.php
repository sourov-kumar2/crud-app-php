<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Application</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php 
        
        $conn = mysqli_connect("localhost", "root", "", "crud");

       
        if (!$conn) {
            die("<div class='alert alert-danger'>Connection failed: " . mysqli_connect_error() . "</div>");
        }

        
        $nameErr = $cityErr = "";
        $name = $city = "";
        $editId = null;

        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['addStudent'])) {
                if (empty($_POST["name"])) {
                    $nameErr = "Name is required";
                } else {
                    $name = $_POST["name"];
                    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                        $nameErr = "Only letters and white space allowed";
                    }
                }

                if (empty($_POST["city"])) {
                    $cityErr = "City is required";
                } else {
                    $city = $_POST["city"];
                    if (!preg_match("/^[a-zA-Z-' ]*$/", $city)) {
                        $cityErr = "Only letters and white space allowed";
                    }
                }

                if (empty($nameErr) && empty($cityErr)) {
                    $query = "INSERT INTO students (`name`, `city`) VALUES ('$name', '$city')";
                    $insert = $conn->query($query);
                    if ($insert) {
                        echo "<div class='alert alert-success'>Student added successfully</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Student not added</div>";
                    }
                }
            } elseif (isset($_POST['updateStudent'])) {
                $id = $_POST['id'];
                if (empty($_POST["name"])) {
                    $nameErr = "Name is required";
                } else {
                    $name = $_POST["name"];
                    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                        $nameErr = "Only letters and white space allowed";
                    }
                }

                if (empty($_POST["city"])) {
                    $cityErr = "City is required";
                } else {
                    $city = $_POST["city"];
                    if (!preg_match("/^[a-zA-Z-' ]*$/", $city)) {
                        $cityErr = "Only letters and white space allowed";
                    }
                }

                if (empty($nameErr) && empty($cityErr)) {
                    $query = "UPDATE students SET name='$name', city='$city' WHERE id=$id";
                    $update = $conn->query($query);
                    if ($update) {
                        echo "<div class='alert alert-success'>Student updated successfully</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Student not updated</div>";
                    }
                }
            } elseif (isset($_POST['deleteStudent'])) {
                $id = $_POST['id'];
                $query = "DELETE FROM students WHERE id=$id";
                $delete = $conn->query($query);
                if ($delete) {
                    echo "<div class='alert alert-success'>Student deleted successfully</div>";
                } else {
                    echo "<div class='alert alert-danger'>Student not deleted</div>";
                }
            }
        }

       
        $selectQuery = "SELECT * FROM students";
        $students = $conn->query($selectQuery);

        
        if (isset($_GET['edit'])) {
            $editId = $_GET['edit'];
            $editQuery = "SELECT * FROM students WHERE id=$editId";
            $editStudent = $conn->query($editQuery)->fetch_assoc();
            $name = $editStudent['name'];
            $city = $editStudent['city'];
        }
        ?>

        <!--  add or update korar form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><?= $editId ? 'Edit Student' : 'Add Student' ?></h2>
            </div>
            <div class="card-body">
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <?php if ($editId): ?>
                        <input type="hidden" name="id" value="<?= $editId ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control <?php echo $nameErr ? 'is-invalid' : ''; ?>" name="name" id="name" placeholder="Enter Your Name" value="<?= htmlspecialchars($name) ?>">
                        <div class="invalid-feedback"><?= $nameErr ?></div>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control <?php echo $cityErr ? 'is-invalid' : ''; ?>" name="city" id="city" placeholder="Enter Your City" value="<?= htmlspecialchars($city) ?>">
                        <div class="invalid-feedback"><?= $cityErr ?></div>
                    </div>
                    <button type="submit" class="btn btn-success" name="<?= $editId ? 'updateStudent' : 'addStudent' ?>"><?= $editId ? 'Update Student' : 'Add Student' ?></button>
                </form>
            </div>
        </div>

        <!--  student records display korar table -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Student List</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>SI</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        while ($student = $students->fetch_object()) { 
                        ?>
                        <tr>
                            <td><?= $sl++ ?></td>
                            <td><?= htmlspecialchars($student->name) ?></td>
                            <td><?= htmlspecialchars($student->city) ?></td>
                            <td>
                                <a href="?edit=<?= $student->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $student->id ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" name="deleteStudent">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap  -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
