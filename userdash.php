<?php
include 'confi.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$articles = mysqli_query($conn, "SELECT * FROM articles ORDER BY created_at DESC");

if (!$articles) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!doctype html>
<html>
<head>
    <title>USER DASHBOARD</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(to bottom right, #e8f5e9, #ffffff);
            margin: 0;
        }
        .header{
            background: #ffffff;
            padding:15px 20px;
            font-size:22px;
        }
        .container{
            padding: 20px;
        }
        .article{
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .article h3{
            margin-top:0;
            color: #1f2931;
        }
        .date{
            font-size: 12px;
            color:#555;
        }
    </style>
</head>
<body>

<div class="header">USER DASHBOARD</div>

<div class="container">
<?php while ($row = mysqli_fetch_assoc($articles)) { ?>
    <div class="article">
        <h3><?= $row['title']; ?></h3>
        <p class="date">Published on <?= $row['created_at']; ?></p>
        <p><?= $row['content']; ?></p>
    </div>
<?php } ?>
</div>

</body>
</html>
