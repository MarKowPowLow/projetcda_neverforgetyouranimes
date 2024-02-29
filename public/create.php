<?php declare(strict_types=1);

// On inclut le fichier functions.php pour avoir accès aux fonctions.
require_once 'functions.php';

// Instantiation de PDO.
$pdo = getPDO('mysql:host=localhost;dbname=neverforgetyouranimes', 'root', '');

// Stockage de l'id du user dans une variable.
$get_user_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : (isset($_POST['hidden_user_id']) ? htmlspecialchars($_POST['hidden_user_id']) : '');

// Stockage du résultat de notre fonction afin de récupérer le Username ainsi que la PP
$userData = getUsernameAndPp($pdo, (int) $get_user_id);

// Initialisation de la variable messageSucceeded pour éviter les erreurs si elle n'est pas encore définie.
$messageSucceeded = '';

// Initialisation de la variable messageFailed pour éviter les erreurs si elle n'est pas encore définie.
$messageFailed = '';

// Initialisation de la variable lastEpisodeValue pour éviter les erreurs si elle n'est pas encore définie.
$lastEpisodeValue = '';

// Appel de la fonction searchAnimeInDatabase puis stockage du retour dans une variable afin d'avoir accès à la liste d'anime.
$animesData = searchAnimeInDatabase($pdo);

// Vérification de l'id de l'anime dans la superglobale $_POST puis stockage de cet ID de manière sécurisée avec htmlspecialchars. Si l'id n'est pas encore défini, on lui donne une valeur par défaut ''.
$anime_id = isset($_POST['select_anime_id']) ? htmlspecialchars($_POST['select_anime_id']) : '';

// Vérification de l'id du langage dans la superglobale $_POST puis stockage de cet ID de manière sécurisée avec htmlspecialchars. Si l'id n'est pas encore défini, on lui donne une valeur par défaut ''.
$language_id = isset($_POST['select_language_id']) ? htmlspecialchars($_POST['select_language_id']) : '';

// Appel de la fonction searchStatuses puis stockage du retour dans une variable afin d'avoir accès à la liste des status.
$statusesData = searchStatuses($pdo);

// Vérification de l'utilsation de la méthode POST dans la variable $_SERVER, si elle a été utiliser alors le reste du code dans le if s'execute.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Vérification de la sélection d'un anime dans le formulaire.
    if (isset($_POST['select_anime_id']) || isset($animesData['anime_id']));
    {
        // Si oui, on appel la fonction searchLanguageForAnime qui va venir vérifier quel est ou quel sont les langages disponible pour l'anime en question.
        $languagesData = searchLanguageForAnime($pdo, $anime_id);
    }

    // Vérification de la sélection d'un anime et d'un langage dans le formulaire.
    if (isset($_POST['select_anime_id'], $_POST['select_language_id'])) {
        // Si oui, on appel la fonction searchEpisodesForAnime qui va venir récupérer les épisodes disponible pour l'anime et le langage en question.
        $episodesData = searchEpisodesForAnime($pdo, $anime_id, $language_id);
        // Puis, on appel la fonction searchLastEpisode qui va venir récupérer le dernier épisode sorti pour l'anime et le langage en question.
        $lastEpisodeData = searchLastEpisode($pdo, $anime_id, $language_id);
        // Enfin, on utilise une expression ternaire afin de vérifier si il y a bien un dernier épisode pour cet anime (Au cas où les épisodes aurait été supprimer ou autre...), si un épisode n'est pas défini (donc si notre variable est vide ou est null), on retourne alors '' afin d'éviter une erreur.
        $lastEpisodeValue = ($lastEpisodeData) ? $lastEpisodeData['episode_number'] : '';
    }

    // Vérification de l'existance de notre variable action ainsi que du nom de l'action.
    if (isset($_POST['action']) && $_POST['action'] === "Ajouter l'anime à votre liste") {
        // Vérification de l'existance des variables dans notre superglobale $_POST, mais également de leur non nullité.
        if (
            isset($_POST['select_anime_id'], $_POST['select_status_id'], $_POST['select_language_id'], $_POST['select_episode_id'], $_POST['hidden_last_released_episode'], $_POST['hidden_user_id']) &&
            !empty($_POST['select_anime_id']) && !empty($_POST['select_status_id']) && !empty($_POST['select_language_id']) && !empty($_POST['select_episode_id']) && !empty($_POST['hidden_last_released_episode']) && $_POST['hidden_user_id']
        ) {

            // Traitement de chacune de nos variables afin d'enlever les caractères indésirables et de vérifier si ce sont bien des entiers valide.
            $user_id = cleanAndValidateValue($_POST['hidden_user_id']);
            $anime_id = cleanAndValidateValue($_POST['select_anime_id']);
            $status_id = cleanAndValidateValue($_POST['select_status_id']);
            $language_id = cleanAndValidateValue($_POST['select_language_id']);
            $current_episode = cleanAndValidateValue($_POST['select_episode_id']);
            $last_released_episode = cleanAndValidateValue($_POST['hidden_last_released_episode']);

            // Enfin, si toutes les valeurs ont été correctement vérifiées, appel de la fonction addAnimesToList afin d'ajouter une entrée dans la BDD.
            $success = addAnimesToList($pdo, $current_episode, $last_released_episode, $user_id, $anime_id, $status_id, $language_id);

            // Vérification de la réussite de l'opération (C'est un boolean), si l'opération à réussi, on nettoie notre superglobale $_POST de toutes les données et on affiche un message de réussite.
            if ($success) {
                unset($_POST['select_anime_id']);
                unset($_POST['select_status_id']);
                unset($_POST['select_language_id']);
                unset($_POST['select_episode_id']);
                unset($_POST['hidden_last_released_episode']);
                unset($_POST['action']);
                $messageSucceeded = "L'anime a été ajouté à votre liste !";
            } else {
                // Sinon, on envoie un message signalant une erreur lors de l'opération.
                $messageFailed = "Une erreur s'est produite lors de l'ajout de l'anime à votre liste.";
            }
        } else {
            // Si les valeurs du formulaire n'existe pas toutes ou si elles sont nulles, on affiche un message d'erreur.
            $messageFailed = "Tous les champs du formulaire doivent être remplis !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <title>NeverForgetYourAnimes</title>
</head>

<header>
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/?id=<?=$get_user_id;?>" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="./img/neverforgetyouranimesLogobis.png" class="h-8" alt="NeverForgetYourAnimes Logo"/>
            </a>
            <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <button type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                    <span class="sr-only">Open user menu</span>
                    <!-- Affichage de l'image du user comme bouton de notre menu dropdown. -->
                    <img class="w-8 h-8 rounded-full" src="<?=$userData['profil_picture']?>" onerror="this.onerror=null; this.src='./img/testicon.jpg'" alt="user photo">
                </button>
                <!-- Menu Dropdown -->
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
                    <div class="px-4 py-3">
                        <!-- Affichage du Username dans notre menu dropdown. -->
                    <span class="block text-sm text-gray-900 dark:text-white">Bonjour <?=$userData['username']?> </span>
                    </div>
                    <ul class="py-2" aria-labelledby="user-menu-button">
                    <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Paramètres</a>
                    </li>
                    <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Déconnexion<!-- ProjectAriane -> LogOut-PHP / Laravel ? --></a>
                    </li>
                    </ul>
                </div>
                <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-user" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
                <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="/?id=<?=$get_user_id;?>" class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500" aria-current="page">Tableau de bord</a>
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Découvrir</a> <!-- ProjectAriane -> Proposition de manga par rapport au nombres de votant ? -->
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Rechercher</a> <!-- ProjectAriane -> Menu de recherche de manga par catégories ou autre ? -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!--Affichage du message de réussite du formulaire.-->
    <?php if(!empty($messageSucceeded)):?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Succès !</strong>
        <span class="block sm:inline"><?php echo $messageSucceeded; ?></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1 1 0 0 1-1.415 1.415l-3.533-3.533-3.535 3.533a1 1 0 1 1-1.415-1.415l3.535-3.533-3.535-3.535a1 1 0 1 1 1.415-1.415l3.535 3.535 3.533-3.535a1 1 0 0 1 1.415 1.415l-3.533 3.535 3.533 3.533z"/></svg>
        </span>
    </div>
    <?php endif;?>

<!-- Affichage du message d'échec du formulaire -->
    <?php if(!empty($messageFailed)):?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Erreur !</strong>
        <span class="block sm:inline"><?php echo $messageFailed; ?></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1 1 0 0 1-1.415 1.415l-3.533-3.533-3.535 3.533a1 1 0 1 1-1.415-1.415l3.535-3.533-3.535-3.535a1 1 0 1 1 1.415-1.415l3.535 3.535 3.533-3.535a1 1 0 0 1 1.415 1.415l-3.533 3.535 3.533 3.533z"/></svg>
        </span>
    </div>
    <?php endif;?>


    <div class="container mx-auto py-8">
        <form action="create.php" method="post" class="w-full max-w-sm mx-auto bg-white p-8 rounded-md shadow-md">
            <label for="hidden_user_id">
            <input type="text" id="hidden_user_id" name="hidden_user_id" value="<?=$get_user_id?>" hidden>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="select_anime_id">Nom de l'anime :</label>
                <select name="select_anime_id" id="select_anime_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <!-- Foreach de notre variable $results qui comprend des tableaux imbriqués dont le tableau queryAnimes -->
                    <option value="">Sélectionnez un anime</option>
                    <!-- Début d'un Foreach de notre variable animesData qui comprend la liste de tout les animes.-->
                    <?php foreach($animesData as $animeData):?>
                    <option value="<?=$animeData['id']?>" <?=(isset($anime_id) && $animeData['id'] == $anime_id) ? 'selected' : ''?>>
                        <?=$animeData['name']?>
                    <?php endforeach;?>
                </select><br><br>

                <div class="flex justify-end mb-4">
                    <label for="validAnime">
                    <button class="w-40 bg-indigo-500 text-white text-sm font-bold py-2 px-4 rounded-md hover:bg-indigo-600 transition duration-300" type="submit" name="action" id="validAnime" value="validAnime">Valider l'anime
                    </button><br><br>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="select_language_id">Langue :</label>
                <select name="select_language_id" id="select_language_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Sélectionnez un langage</option>
                    <!-- Début d'un Foreach de notre variable languagesData qui comprend la liste de tout les langages disponible pour cet anime.-->
                    <?php foreach($languagesData as $languageData):?>
                        <option value="<?=$languageData['id']?>" <?=(isset($language_id) && $languageData['id'] == $language_id) ? 'selected' : ''?>>
                        <?=$languageData['abbr']?>
                    <?php endforeach;?>
                </select><br><br>
                
                <div class="flex justify-end mb-4">
                    <label for="validLanguage">
                    <button class="w-40 bg-indigo-500 text-white text-sm font-bold py-2 px-4 rounded-md hover:bg-indigo-600 transition duration-300" type="submit" name="action" id="validLanguage" value="validLanguage">Valider la langue
                    </button><br><br>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="select_status_id">Status :</label>
                <select name="select_status_id" id="select_status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <!-- Début d'un Foreach de notre variable statusesData qui comprend la liste de tout les status disponible.-->
                    <?php foreach($statusesData as $statusData):?>
                    <option value=<?=$statusData['id']?>>
                        <?=$statusData['name']?>
                    </option>
                    <?php endforeach;?>
                </select><br><br>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="select_episode_id">Dernier épisode vu :</label>
                <select name="select_episode_id" id="select_episode_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">0 - Pas encore</option>
                    <!-- Début d'un Foreach de notre variable episodesData qui comprend la liste de tout les épisodes disponible selon l'anime choisi ainsi que le langage.-->
                    <?php foreach($episodesData as $episodeData):?>
                    <option value=<?=$episodeData['episode_number']?>>
                        <?=$episodeData['episode_number']?> - <?=$episodeData['episode_name']?>
                    </option>
                    <?php endforeach;?>
                </select><br><br>
            </div>
            <div class="mb-4">
                <label for="hidden_last_released_episode">
                <input type="text" id="hidden_last_released_episode" name="hidden_last_released_episode" value="<?=$lastEpisodeValue?>" hidden>
            </div>
            <div class="flex justify-end mb-4">
                <label for="addAnimeToList">
                <button
                class="w-40 bg-indigo-500 text-white text-sm font-bold py-2 px-4 rounded-md hover:bg-indigo-600 transition duration-300"
                type="submit" name="action" id="addAnimeToList" value="Ajouter l'anime à votre liste">Ajouter l'anime
                </button>
            </div>
        </form>
    </div>
</body>
</html>