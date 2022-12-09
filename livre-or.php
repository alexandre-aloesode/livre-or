<?php

    include 'connec.php';
    include 'connecSQL.php';

// Ci-dessous ma requête pour afficher les commentaires sur la page
    $request_comments = "SELECT commentaires.date, utilisateurs.login, commentaires.commentaire, commentaires.id 
    FROM `commentaires`
    INNER JOIN `utilisateurs` ON utilisateurs.id = commentaires.id_utilisateur
    ORDER BY date DESC";
    $query_comments = $mysqli->query($request_comments);
    $result_comments = $query_comments->fetch_all();

// Ci-dessous ma requête pour rajouter un commentaire posté dans la bdd
    if(isset($_POST['send_comment']) && !empty($_POST['comment'])){
        
        $user_ID = $_SESSION['userID'];
        $add_comment = mysqli_real_escape_string($mysqli, $_POST['comment']);
// J'utilise la fonction ci-dessus au cas où il y aurait des apostrophes dans la chaîne. Elle rajoute un antislash afin d'échapper les apostrophes.
        $date = date('Y-m-d-H-i-s');
        $request_add_comment = "INSERT INTO `commentaires`(`commentaire`, `id_utilisateur`, date) 
        VALUES ('$add_comment','$user_ID','$date')";
        $query_add_comment = $mysqli->query($request_add_comment);
        header('Location: livre-or.php');
    }

// Ci_dessous la requête pour supprimer le commentaire que l'utilisateur choisit d'enlever.
    if(isset($_POST['delete'])){
        $request_delete_comment = "DELETE FROM `commentaires` WHERE `commentaires`.`id` = '$_POST[delete]'";
        $query_delete_comment = $mysqli->query($request_delete_comment);
        header('Location: livre-or.php');
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="livre-or.css" rel="stylesheet">
    <link href="index.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Livre d'or</title>
</head>
<body>

    <?php include 'header.php' ?>

    <main>

        <?php if(isset($_SESSION['user'])): ?>

            <form method="post" id="comment_form">
            
                <p>Poster un commentaire</p>

                <textarea name="comment" placeholder="Tapez votre commentaire ici..."></textarea>

                <button type="submit" name="send_comment">Envoyer</button>

            </form>

        <?php else: ?>

            <a href= "connexion.php">
                <p>Connectez-vous ici pour poster un commentaire</p>
            </a>             

        <?php endif ?>

        <table>
            <thead >
                <tr>
                    <th>Posté le :</th>
                    <th>Utilisateur</th>
                    <th>Commentaire</th>
                </tr>
            </thead>

            <tbody>
                <?php

                    for($x = 0; isset($result_comments[$x]); $x++):

                    $date = strtotime($result_comments[$x][0]);
                    $date = date('d/m/Y à H:i', $date);

                ?> 
                    
                        <tr>
                            <td> <?php echo 'Le ' . $date ?> </td>

                            <td> <i class="fa-solid fa-user"> </i> <?php echo $result_comments[$x][1] ?> </td>

                            <td> <?php echo $result_comments[$x][2] ?> </td>

<!-- Les lignes ci_dessous me permettent de d'afficher un bouton supprimer à côte des commentaires de l'utilisateur si ce dernier est connecté.
En gros le premier if identifie les commentaires de l'utilisateur, et rajoute un bouton qui a en value la colonne id  du commentaire dans ma table commentaires.
Grâce à cette id du commentaire, si l'utilisateur clique sur supprimer, une requête sql se lance pour aller supprimer l'id en question. -->
                            <?php if(isset($_SESSION['user']) && $_SESSION['user'] == $result_comments[$x][1]): ?>

                                <td>
                                    <form method ="post">
                                        <button type="submit" name="delete" value="<?php echo $result_comments[$x][3] ?>">Supprimer</button>
                                    </form>
                                </td>

                            <?php endif ?>                                                  
                        </tr>                 
                    <?php endfor ?>
            </tbody>
        </table>            
    </main>

    <?php include 'footer.php' ?>
    
</body>
</html>