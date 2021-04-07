<!-- Envoie Formulaire -->
<?php
//Si on reçois bien le shorcut
if (isset($_GET['q'])) {
    //Variable
    $shortcut = htmlspecialchars($_GET['q']);

    //Connexion à la base de donnée

    $bdd = new PDO('mysql:host=localhost;dbname=bilty;charset=utf8', 'root', 'root');

    //Si le shortcut n'est pas connue
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    //Boucle de control
    while ($result = $req->fetch()) {

        if ($result['x'] != 1) {
            header('location: ../raccourcisseurUrl/?error=true&message=Adresse url non connue');
            exit();
        }
    }
    // Redirection
    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while ($result = $req->fetch()) {

        header('location: ' . $result['url']);
        exit();
    }
}
//Si elle existe
if (isset($_POST['url'])) {
    //Variable
    $url = $_POST['url'];

    //Adresse valide?
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        //Url non valide
        header('location: ../raccourcisseurUrl/?error=true&message=Adresse url non valide');
        exit();
    }
    //ShortCut
    $shortcut = crypt($url, rand());

    //Connexion à la base de donnée

    $bdd = new PDO('mysql:host=localhost;dbname=bilty;charset=utf8', 'root', 'root');

    //Si url exite déjà
    $req  = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute(array($url));

    //Boucle de controle
    while ($result = $req->fetch()) {
        if ($result['x'] != 0) {
            header('location: ../raccourcisseurUrl/?error=true&message=Adresse url déjà raccourcie');
            exit();
        }
    }

    //Enregistre dans BDD

    $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?,?)') or die(print_r($bdd->errorInfo()));
    $req->execute(array($url, $shortcut));

    //Nouveau lien
    header('location: ../raccourcisseurUrl/?short=' . $shortcut);
    exit();
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racourcisseur d'URL</title>
    <link rel="stylesheet" type="text/css" href="design/default.css" media="all" />
    <link rel="icon" type="image/png" href="./design/pictures/favico.png">
</head>

<body>

    <!-- Présentation -->
    <section id="hello">

        <!-- Container -->
        <div class="container">
            <!-- Header -->
            <header>
                <img src="./design/pictures/logo.png" alt="logo" id="logo">
            </header>
            <h1>Une Url longue ? Raccourcissez-là.</h1>
            <h2>Largement meilleur et plus court que les autres.</h2>
            <!-- Formulaire -->
            <form method="post" action="index.php">
                <input type="url" name="url" placeholder="Saisissez votre Url...">
                <input type="submit" value="Raccourcir">
            </form>

            <!-- Afficher une erreur -->
            <?php if (isset($_GET['error']) && isset($_GET['message'])) { ?>
                <div class="center">
                    <div id="result">
                        <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                    </div>
                </div>
            <?php } else if (isset($_GET['short'])) { ?>
                <div class="center">
                    <div id="result">
                        <b>URL RACCOURCIE : </b>
                        http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

    </section>

    <!-- Brands -->
    <section id="brands">

        <div class="container">
            <h3>Ces marques nous font confiance</h3>
            <img src="design/pictures/1.png" alt="logo entreprise" class="picture">
            <img src="design/pictures/2.png" alt="logo entreprise" class="picture">
            <img src="design/pictures/3.png" alt="logo entreprise" class="picture">
            <img src="design/pictures/4.png" alt="logo entreprise" class="picture">
        </div>
        <!-- Footer-->
        <footer>
            <img id="logo" src="design/pictures/logo2.png" alt="logo">
            <p>2018 © Bilty</p>
            <a href="#">Contact</a>
            -
            <a href="#">À Propos</a>
        </footer>
    </section>
</body>

</html>