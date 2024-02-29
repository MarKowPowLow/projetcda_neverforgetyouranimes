<?php

// Fonction de connexion à la BDD.
function getPDO(string $dsn, string $user = 'root', string $pass = ''): PDO
{
    return new PDO($dsn, $user, $pass); // On retourne une instantiation de PDO avec les paramètres définis dans la fonction.
}

// Fonction Read - Lecture de notre liste d'animes du User qui est log (On va lire la liste du user dont l'id sera indiqué dans l'URL) avec une pagination.
function getListWithAnimesByUser(PDO $pdo, int $user_id, int $perPage, int|null $page): array
{
    // Préparation de la requête afin de compter le nombre d'animes correspondant au $user_id.
    $queryCount = $pdo->prepare('SELECT COUNT(*) AS totalcount 
    FROM animeslists al 
    LEFT JOIN users u ON al.user_id = u.id 
    WHERE u.id = :id');

    $queryCount->bindValue('id', $user_id, PDO::PARAM_INT);
    $queryCount->execute(); // Lancement de la requête pour pouvoir compter le nombre d'animes. On va ensuite stocker ce nombre d'animes pour pouvoir calculer le nombre de page total par rapport au nombre d'animes/par le nombre d'animes affichés par page.

    $count = $queryCount->fetchColumn(); // Vérification dans la BDD si il y a un ou plusieurs animes correspondant au user.

    if ($count > 0) { // Si il y a des animes correspondant à son user_id, alors.

        $totalpage = (int) ceil($count / $perPage); // On va calculer le nombre de page total nécessaire pour afficher tout les animes.

        if (isset($_GET['page'])) { // Si il y a un n° de page dans l'URL, notre page sera définie par ce n°.
            $page = intval($_GET['page']);
            if ($page > $totalpage) { // Si le n° de page dans l'URL est supérieur au nombre total de page, on est renvoyé sur la dernière page.
                $page = $totalpage;
            }
        } else { // Si il n'y a pas de n° de page dans l'URL, on commence à la première page.
            $page = 1;
        }
        $start_index = ($page - 1) * $perPage; // Calcul de la première valeur de l'index de la page actuelle.

        $last_result_page = min($start_index + $perPage, $count); // Calcul de la dernière valeur de l'index de la page actuelle.

        $first_result_page = $start_index + 1; // Calcul de la première valeur de la page actuelle.


        // Préparation de la requête pour avoir notre liste d'animes correspondant à l'ID dans l'URL, ranger dans l'ordre décroissant afin d'avoir nos derniers animes sortit en premier. La requête sera limitée par un nombre d'animes par page, determiné par la première entrée et le nombre d'animes par page.
        $query = $pdo->prepare('SELECT al.id, al.current_episode, al.last_released_episode, al.updated_at, a.name AS anime_name, a.updated_at AS anime_updated_at, s.name AS status_name, l.abbr AS language_abbr, u.username AS user_username, a.id AS anime_id
        FROM animeslists al
        LEFT JOIN users u ON al.user_id = u.id
        LEFT JOIN animes a ON al.anime_id = a.id
        LEFT JOIN statuses s ON al.status_id = s.id
        LEFT JOIN languages l ON al.language_id = l.id
        WHERE u.id = :id
        ORDER BY anime_updated_at DESC 
        LIMIT :perPage OFFSET :offset
        ');

        $query->bindValue('id', $user_id, PDO::PARAM_INT);
        $query->bindValue('perPage', $perPage, PDO::PARAM_INT);
        $query->bindValue('offset', $start_index, PDO::PARAM_INT); // Calcul du premier anime à afficher sur la page.
        $query->execute();

        $animesList = $query->fetchAll(PDO::FETCH_ASSOC);

        return [
            'count_total' => $count,
            'per_page' => $perPage,
            'last_result_page' => $last_result_page,
            'first_result_page' => $first_result_page,
            'page' => [
                'current_page' => $page,
                'total_pages' => $totalpage,
            ],
            'data' => $animesList,
        ];
    } else {
        return []; // Sinon, on retourne un tableau vide.
    }
}

// Fonction Read - Lecture de notre l'utilisateur (par son id) pour pouvoir récupérer son username ainsi que de sa PP.
function getUsernameAndPp(PDO $pdo, int $user_id): array
{
    // Préparation de la requête afin de récupérer le nom d'utilisateur correspondant au $user_id.
    $queryUserData = $pdo->prepare('SELECT username, profil_picture FROM users WHERE id = :id');
    $queryUserData->bindValue('id', $user_id, PDO::PARAM_INT);
    $queryUserData->execute();
    $userData = $queryUserData->fetch(PDO::FETCH_ASSOC);

    return $userData;
}

// ------------------------------------------ Formulaire Create ------------------------------------------

// Fonction Create - Création d'une nouvelle entrée dans notre table (L'ajout d'un anime dans notre liste).
function addAnimesToList(PDO $pdo, int $current_episode, int $last_released_episode, int $user_id, int $anime_id, int $status_id, int $language_id): bool
{
    $query = $pdo->prepare('INSERT INTO animeslists (current_episode, last_released_episode, user_id, anime_id, status_id, language_id)
    VALUES (:current_episode, :last_released_episode, :user_id, :anime_id, :status_id, :language_id)
    ');

    return $query->execute([
        ':current_episode' => $current_episode,
        ':last_released_episode' => $last_released_episode,
        ':user_id' => $user_id,
        ':anime_id' => $anime_id,
        ':status_id' => $status_id,
        ':language_id' => $language_id,
        ]);
}

// Fonction Read - Pour trouver l'anime que le user souhaite ajouter à sa liste.
function searchAnimeInDatabase(PDO $pdo): array
{
    $query = $pdo->prepare('SELECT id, name, episode_number_total FROM animes ORDER BY name');
    $query->execute();

    $animeData = $query->fetchAll();

    return $animeData;
}

// Fonction Read - Pour rechercher dans la table languages les id et name des langages disponible pour l'anime en question (Avec en jointure la table anime_has_languages).
function searchLanguageForAnime(PDO $pdo, string $anime_id): array
{
    $query = $pdo->prepare('SELECT l.id, l.abbr, ahl.language_id
    FROM languages l
    LEFT JOIN anime_has_languages ahl ON l.id = ahl.language_id
    WHERE ahl.anime_id = :id
    ');

    $query->bindValue('id', $anime_id, PDO::PARAM_INT);
    $query->execute();

    $languageData = $query->fetchAll();

    return $languageData;
}

// Fonction Read - Pour rechercher dans la table statuses les status disponibles.
function searchStatuses(PDO $pdo): array
{
    $query = $pdo->prepare('SELECT id, name FROM statuses ORDER BY name');

    $query->execute();

    $statusesData = $query->fetchAll();

    return $statusesData;
}

// Fonction Read - Pour rechercher le listing des épisodes
function searchEpisodesForAnime(PDO $pdo, string|int $anime_id, string|int $language_id): array
{
    $query = $pdo->prepare('SELECT id, episode_name, episode_number, anime_id, language_id
    FROM episodes
    WHERE anime_id = :anime_id AND language_id = :language_id
    ');

    $query->bindValue('anime_id', $anime_id, PDO::PARAM_INT);
    $query->bindValue('language_id', $language_id, PDO::PARAM_INT);
    $query->execute();

    $currentEpisodesData = $query->fetchAll();

    return $currentEpisodesData;
}

// Fonction Read - Pour rechercher le dernier épisode sorti par anime et par langage (Cela renseignera automatiquement le dernier épisode sorti dans un champ de formulaire caché).
function searchLastEpisode(PDO $pdo, string|int $anime_id, string|int $language_id): array|false
{
    $query = $pdo->prepare('SELECT id, episode_name, episode_number
    FROM episodes
    WHERE anime_id = :anime_id AND language_id = :language_id
    ORDER BY episode_number DESC
    LIMIT 1
    ');

    $query->bindValue('anime_id', $anime_id, PDO::PARAM_INT);
    $query->bindValue('language_id', $language_id, PDO::PARAM_INT);
    $query->execute();

    $lastEpisodeData = $query->fetch(PDO::FETCH_ASSOC);

    return $lastEpisodeData;
}

// Fonction pour valider et nettoyer nos entiers (Afin d'éviter des problèmes de sécurité).
function cleanAndValidateValue($value)
{
    // Traitement et stockage de notre variable value afin d'enlever les caractères indésirables.
    $cleaned_value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    // Vérification que notre variable cleaned_value est bien un entier valide.
    $validated_value = filter_var($cleaned_value, FILTER_VALIDATE_INT);
    // Retour de la variable si elle est valide, sinon on retourne null.
    return ($validated_value !== false) ? intval($validated_value) : null;
}

// ------------------------------------------ Update -----------------------------------------------

// Fonction Update - Modification de l'animelist d'un utilisateur (Modification du status, du dernier épisode vu, ect...).
function updateAnimesList(PDO $pdo, int $current_episode, int $last_released_episode, int $user_id, int $anime_id, int $status_id, int $language_id, int $animesList_id): bool
{
    $query = $pdo->prepare(
        'UPDATE animeslists SET current_episode = :current_episode, last_released_episode = :last_released_episode, user_id = :user_id, anime_id = :anime_id, status_id = :status_id, language_id = :language_id 
    WHERE id = :id'
    );

    $query->bindValue(':current_episode', $current_episode, PDO::PARAM_INT);
    $query->bindValue(':last_released_episode', $last_released_episode, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':anime_id', $anime_id, PDO::PARAM_INT);
    $query->bindValue(':status_id', $status_id, PDO::PARAM_INT);
    $query->bindValue(':language_id', $language_id, PDO::PARAM_INT);
    $query->bindValue('id', $animesList_id, PDO::PARAM_INT);
    return $query->execute();
}

// Fonction Read - Pour trouver l'anime que le user souhaite à modifier dans sa liste.
function searchAnimeInAnimesList(PDO $pdo, int $user_id, int $anime_id): array
{
    $query = $pdo->prepare('SELECT al.id, al.current_episode, al.last_released_episode, al.updated_at, a.name AS anime_name, a.updated_at AS anime_updated_at, s.name AS status_name, l.abbr AS language_abbr, a.id AS anime_id, al.language_id AS language_id, s.id AS status_id
    FROM animeslists al
    LEFT JOIN users u ON al.user_id = u.id
    LEFT JOIN animes a ON al.anime_id = a.id
    LEFT JOIN statuses s ON al.status_id = s.id
    LEFT JOIN languages l ON al.language_id = l.id
    WHERE u.id = :user_id AND a.id = :anime_id
    ');

    $query->bindValue('user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue('anime_id', $anime_id, PDO::PARAM_INT);
    $query->execute();

    $animeData = $query->fetch(PDO::FETCH_ASSOC);

    return $animeData;
}

// ------------------------------------------ Delete -----------------------------------------------

// Fonction Delete - Suppression d'un anime de l'animelist d'un utilisateur (Si il ne voudrait plus suivre cet anime).
function deleteAnimesInList(PDO $pdo, int $id): bool
{
    $query = $pdo->prepare('DELETE FROM animeslists WHERE id = :id');

    $query->bindValue(':id', $id, PDO::PARAM_INT);
    return $query->execute();
}