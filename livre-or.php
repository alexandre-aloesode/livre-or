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

// Ci-dessous ma requête pour afficher les réponses sur la page
    $request_answers = "SELECT * from reponses";
    $query_answers = $mysqli->query($request_answers);
    $result_answers = $query_answers->fetch_all();

// Ci-dessous ma requête pour rajouter un commentaire posté dans la bdd
    if(isset($_POST['send_comment']) && !empty($_POST['comment'])) {
        
        $user_ID = $_SESSION['userID'];
        $add_comment = mysqli_real_escape_string($mysqli, $_POST['comment']);
// J'utilise la fonction ci-dessus au cas où il y aurait des apostrophes dans la chaîne. Elle rajoute un antislash afin d'échapper les apostrophes.
        $date = date('Y-m-d-H-i-s');
        $request_add_comment = "INSERT INTO `commentaires`(`commentaire`, `id_utilisateur`, date) 
        VALUES ('$add_comment','$user_ID','$date')";
        $query_add_comment = $mysqli->query($request_add_comment);
        header('Location: livre-or.php');
    }

// Ci_dessous la requête pour supprimer le commentaire que l'utilisateur choisit d'enlever, et les réponses asssociées.
    if(isset($_POST['delete'])) {
        $request_delete_comment = "DELETE FROM `commentaires` WHERE `commentaires`.`id` = '$_POST[delete]'";
        $query_delete_comment = $mysqli->query($request_delete_comment);

        $request_delete_answers = "DELETE FROM `reponses` WHERE `reponses`.`id_commentaire` = '$_POST[delete]'";
        $query_delete_answers = $mysqli->query($request_delete_answers);
        header('Location: livre-or.php');
    }

// Ci-dessous la requête pour modifier son commentaire.
    if(isset($_POST['send_comment_modif']) && !empty($_POST['comment_modif'])) {
        $modified_comment = mysqli_real_escape_string($mysqli, $_POST['comment_modif']);
        $request_modify_comment = "UPDATE commentaires SET commentaire = '$modified_comment' WHERE id = '$_SESSION[comment_ID]'";
        $query_modify_comment = $mysqli->query($request_modify_comment);
        header('Location: livre-or.php');
    }

// Ci-dessous la requête pour ajouter les réponses.
    if(isset($_POST['send_answer']) && !empty($_POST['answer_area'])) {
            $date = date('Y-m-d-H-i-s');
            $answer = mysqli_real_escape_string($mysqli, $_POST['answer_area']);
            $request_answer = "INSERT INTO reponses(reponse, login, id_commentaire, id_utilisateur, date) VALUES ('$answer', '$_SESSION[user]', $_SESSION[comment_ID], $_SESSION[userID], '$date')";
            $query_answer = $mysqli->query($request_answer);
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

<!-- Ci_dessous et jusqu'au prochain endif j'affiche:
        -si l'utilisateur n'est pas connecté, un message lui disant de se connecter pour commenter
        -s'il est connecté, une textarea pour taper son commentaire
        -s'il a cliqué sur modifier, une textarea pour modifier le commentaire sélectionné
        -s'il a cliqué sur répondre, une textarea pour poster sa réponse
Pour les modifications de commentaire et réponses aux commentaires, je crée une variable de session qui récupère l'id du commentaire grâce à la value du bouton. -->
        <?php if(isset($_POST['modify_comment'])):

            $_SESSION['comment_ID'] = $_POST['modify_comment'];
            $request_comment = "SELECT commentaire FROM commentaires WHERE id = '$_POST[modify_comment]'";
            $query_comment = $mysqli->query($request_comment);
            $result_query_comment = $query_comment->fetch_all();

        ?> 

            <form method="post" class="comment_form">

                <h2 id="modif_title">Modifier votre commentaire</h2>

                <textarea id="modif_area" name="comment_modif"><?php echo $result_query_comment[0][0] ?></textarea>

                <button type="submit" name="send_comment_modif" id="send_modif">Envoyer</button>

            </form>

            <?php elseif(isset($_POST['answer'])):

            $_SESSION['comment_ID'] = $_POST['answer'];
            
            ?>  

            <form method="post" class="comment_form">

                <h2 id="answer_title">Poster une réponse</h2>

                <textarea id="answer_area" name="answer_area" placeholder="Tapez votre réponse ici..."></textarea>

                <button type="submit" name="send_answer" id="send_answer">Envoyer</button>

            </form>

        <?php elseif(isset($_SESSION['user'])): ?>
            
            <form method="post" class="comment_form">
            
                <h2>Poster un commentaire</h2>

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
                    
                        <tr class="comment_lines">
                            
                            <td> <?php echo 'Le ' . $date ?> </td>

                            <td> <i class="fa-solid fa-user"> </i> <?php echo $result_comments[$x][1] ?> </td>

                            <td> <?php echo $result_comments[$x][2] ?>
                                    
                            </td>

<!-- Les lignes ci_dessous me permettent d'afficher un bouton supprimer à côte des commentaires de l'utilisateur si ce dernier est connecté.
En gros le premier if identifie les commentaires de l'utilisateur, et rajoute un bouton qui a en value la colonne id  du commentaire dans ma table commentaires.
Grâce à cette id du commentaire, si l'utilisateur clique sur supprimer, une requête sql se lance pour aller supprimer l'id en question. -->
                            <?php if(isset($_SESSION['user']) && $_SESSION['user'] == $result_comments[$x][1]): ?>

                                <td>
<!-- Si l'utilisateur est connecté j'affiche ci_dessous des bouton pour modifier/supprimer son commentaire, ou répondre à un commentaire. 
Ce bouton récupère en value l'id du commentaire-->

                                    <form method ="post">
                                        <button type="submit" name="delete" value="<?php echo $result_comments[$x][3] ?>">Supprimer</button>
                                    </form>

                                </td>

                                <td>
                                    <form method ="post">
                                        <button type="submit" class="modify_comment" name="modify_comment" value="<?php echo $result_comments[$x][3] ?>">Modifier</button>
                                    </form>
                                </td>
                            <?php endif ?>


                            <?php if(isset($_SESSION['user'])): ?>

                                <td>

                                    <form method ="post">
                                        <button type="submit" class="answer_button" name="answer" value="<?php echo $result_comments[$x][3] ?>">Répondre</button>
                                    </form>

                                </td>
                            <?php endif ?> 

<!-- Ci_dessous une boucle for qui vient parcourir la table reponses dans la colonne id_commentaire. S'il y a un match j'affiche un bouton voir les réponses,
et si l'utilisateur appuie ça affiche les réponses. -->
                            <?php for($j = 0; isset($result_answers[$j]); $j++) : ?>

                                <?php if($result_comments[$x][3] == $result_answers[$j][3]) : ?>
                
                                    <?php if(isset($_POST['show_answers']) && $_POST['show_answers'] == $result_comments[$x][3]) : ?>
<!-- Je rajoute le && $_POST['show_answers'] == $result_comments[$x][3]) sinon en appuyant sur le bouton des réponses, toutes les lignes avec des réponses s'affichent. 
Avec cette condition il n'y a que la ligne avec l'id de commentaire correspondante qui s'affiche.                                         -->
                                        <tr class="answer_lines">

                                            <td> 
                                                <?php
                                                    $date_answer = strtotime($result_answers[$j][5]);
                                                    $date_answer = date('d/m/Y à H:i', $date_answer);
                                                    echo 'Le ' . $date_answer;
                                                ?> 
                                            </td>

                                            <td> <i class="fa-solid fa-user"> </i> <?php echo $result_answers[$j][2] ?> </td>

                                            <td> <?php echo $result_answers[$j][1] ?> </td>

                                        </tr>   

                                    <?php else : ?>

                                        <tr class="button_line">
                                            <td>
                                                <form method="post">
                                                    <button type="submit" id="show_answers_button" name="show_answers" 
                                                    value="<?php echo $result_comments[$x][3] ?>">Voir les réponses</button>
                                                </form>
                                            </td>
                                        </tr>                                 
                                        <?php break ?>

                                    <?php endif ?>
                                <?php endif ?>
                            <?php endfor ?>

<!-- Ci_dessous le bouton pour masquer les réponses si on les a affichées. -->
                            <?php if(isset($_POST['show_answers']) && $_POST['show_answers'] == $result_comments[$x][3]) : ?>
                                <tr class="button_line">
                                    <td>
                                        <form method="post">
                                            <button type="submit" id="hide_answers_button" name="hide_answers">Masquer les réponses</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif ?>

                        </tr>                 
                    <?php endfor ?>
            </tbody>
        </table>            
    </main>

    <?php include 'footer.php' ?>
    
</body>
</html>