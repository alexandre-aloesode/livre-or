<?php 

    include 'connecSQL.php';
    include 'connec.php';

    $request_admin = $mysqli->query('select * from utilisateurs');
    $result_admin = $request_admin->fetch_array(MYSQLI_ASSOC);

?>

<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="livre-or.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Page Admin</title>
</head>

<body>

    <?php if (!isset($_SESSION["user"]) || $_SESSION["user"] !== "admin"): ?>

        <div class="fondnotadmin">

            <?php include 'header.php' ?>

                <div class="midnotadmin">
                    <img src="https://media.giphy.com/media/3o7aTskHEUdgCQAXde/giphy.gif">
                    <br>
                    <div class="msgnotadmin">ARE YOU LOST MY FRIEND ?</div>
                </div> 

            <?php include 'footer.php' ?>
            
        </div>

    <?php elseif (isset($_SESSION['user']) || $_SESSION['user'] == "admin"): ?>

        <?php include 'header.php' ?>
        
        <table border="3">

            <thead>
                <?php foreach($result_admin = $request_admin->fetch_array(MYSQLI_ASSOC) as $key => $value){
                    echo '<th>' . $key . '</th>';
                }
                ?>
            </thead>

            <tbody>
                <?php
                    while($result_admin !=null){
                        echo '<tr>';
                        foreach($result_admin as $value){
                            echo '<td>' . $value . '</td>';
                        }
                        $result_admin = $request_admin->fetch_array(MYSQLI_ASSOC);
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    
    <?php include 'footer.php' ?>

    <?php endif ?>

</body>
</html>