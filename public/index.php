<?php
require_once __DIR__ . '/functions.php';

$pdo = getPDO('mysql:host=localhost;dbname=neverforgetyouranimes', 'root', '');

// Stockage du résultat de notre fonction afin de récupérer le Username ainsi que la PP
$userData = getUsernameAndPp($pdo, (int) htmlspecialchars($_GET['id']));

// Vérification du nombre d'anime que nous souhaitons affiché (Pas encore implémenté, donc on va utiliser une valeur définie par défaut).
$perPage = isset($_GET['perPage']) ? $_GET['perPage'] : 5;

// Vérification de notre variable page pour savoir si elle est définie ou non puis conversion de notre page pour éviter les injections. Si notre variable page n'est pas définie, elle sera considérée comme null pour que notre méthode nous amènes directement à la page 1.
$currentPage = isset($_GET['page']) ? (int) htmlspecialchars($_GET['page']) : 1;

// Stockage du résultat de notre méthode dans une variable afin de pouvoir afficher dans notre index ce que l'ont a reçu de notre BDD.
if (empty($ListWithAnimesByUser = getListWithAnimesByUser($pdo, (int) htmlspecialchars($_GET['id']), (int) $perPage, $currentPage))) {
    $ListWithAnimesByUser = [
        'count_total' => 0,
        'per_page' => $perPage,
        'last_result_page' => 0,
        'first_result_page' => 0,
        'page' => [
            'current_page' => $currentPage,
            'total_pages' => 1,
        ]
        ];
};
?>

<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <title>NeverForgetYourAnimes</title>
</head>

<header>
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-2xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/?id=<?=htmlspecialchars($_GET['id']);?>" class="flex items-center space-x-3 rtl:space-x-reverse">
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
                        <a href="/?id=<?=htmlspecialchars($_GET['id']);?>" class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500" aria-current="page">Tableau de bord</a>
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

<body class="h-full">
    <div class="flex-grow sm:mx-5 mx-0 mt-5">
        <div class="mt-4 rounded-md border shadow bg-white border-gray-300">
            <header class="px-6 py-5 border-b border-gray-300">
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="flex items-center ml-4 mt-2 sm:ml-2">
                        <input class="relative flex items-start h-5 w-4 invisible" type="checkbox" id="">
                        <!-- Affichage du nombre d'anime sur la page et du nombre total d'anime. -->
                        <div class="pl-4 sm:pl-10">
                            <div class="animate-pulse flex space-x-4 w-48" style="display:none;"></div>
                            <p class="text-sm leading-5 text-gray-700">
                                Affichage de 
                                <span class="font-medium"><?=$ListWithAnimesByUser['first_result_page']?></span>
                                à
                                <span class="font-medium"><?=$ListWithAnimesByUser['last_result_page']?></span>
                                sur
                                <span class="font-medium"><?=$ListWithAnimesByUser['count_total']?></span>
                                animes.
                            </p>
                        </div>
                    </div>
                    <div class="ml-4 mt-2 shrink-0">
                        <div>
                            <span class="inline-flex rounded-md shadow-sm lg:hidden">
                                <button type="button" onclick="location.reload()" class="py-2 px-4 items-center flex w-full rounded-md font-medium justify-center shadow-sm border text-sm border-gray-300 bg-white text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="-ml-1 mr-1 h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </span>
                            <span class="hidden lg:inline-flex rounded-md shadow-sm">
                                <button type="button" onclick="location.reload()" class="flex items-center justify-center rounded-lg px-2 py-2 border-2 text-sm font-medium border-gray-300 bg-white text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="-ml-1 mr-2 h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Rafraîchir
                                </button>
                            </span>
                            <span class="ml-3 inline-flex lg:hidden rounded-md shadow-sm">
                                <a href="/create.php?id=<?=htmlspecialchars($_GET['id']);?>">
                                    <button type="button" id ="addManga" class="flex items-center justify-center rounded-lg px-2 py-2 border-2 text-sm font-medium bg-blue-700 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="-ml-1 mr-1 h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </a>
                            </span>
                            <span class="ml-3 lg:inline-flex hidden rounded-md shadow-sm">
                                <a href="/create.php?id=<?=htmlspecialchars($_GET['id']);?>">
                                    <button type="button" id ="addManga" class="flex items-center justify-center rounded-lg px-2 py-2 border-2 text-sm font-medium bg-blue-700 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="-ml-1 mr-2 h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Ajout d'Anime
                                    </button>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <?php if(!empty($ListWithAnimesByUser['data'])):
                foreach ($ListWithAnimesByUser['data'] as $i): // On fait une boucle foreach pour afficher la liste d'animes que l'utilisateur suit.?>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li class="list-row group list-none border border-opacity-50 border-gray-400 bg-white py-1 sm:px-6">
                            <div class="flex items-center px-4 py-4">
                                <span class="inline-block relative ml-1.5 w-2.5 h-2.5 sm:ml-4">
                                    <span class="animate-ping bg-violet-500 opacity-75 rounded-full inset-0 absolute h-full w-full"></span>
                                    <span class="inset-0 absolute h-2.5 w-2.5 bg-violet-700 border-opacity-100 rounded-full"></span>
                                </span>
                                <div role="spacing" class="w-4 invisible"></div>
                                <div role="row_body" class="flex min-w-0 flex-1 items-center">
                                    <div role="row" class="px-4 lg:grid lg:grid-cols-12 lg:gap-4">
                                        <div role="cell" class="relative col-span-5 lg:flex lg:min-w-0 lg:items-center">
                                            <div class="lg:overflow-hidden lg:text-ellipsis lg:whitespace-nowrap text-gray-900 font-medium text-sm">
                                                <?=$i['anime_name']?> <!-- Affichage du nom de l'anime. -->
                                            </div>
                                        </div>
                                        <div class="flex col-span-3 mt-1 lg:mt-0 justify-around">
                                            <div role="cell" class="text-sm font-normal leading-5 text-gray-900 items-center justify-center flex">
                                                <span class="inline-flex rounded-full font-medium text-xs bg-green-200 text-green-900 align-middle w-20 px-2.5 py-0.5 leading-4">
                                                    <div class="w-full overflow-hidden text-center text-ellipsis whitespace-nowrap"><?=$i['status_name']?></div> <!-- Affichage du status de suivi de l'utilisateur. -->
                                                </span>
                                            </div>
                                            <div role="cell" class="m-2 text-sm font-normal leading-5 text-gray-900 items-center justify-center flex">
                                                <span class="inline-flex items-center rounded-full font-medium text-xs bg-fuchsia-400 text-fuchsia-900 w-20 px-2.5 py-0.5 leading-4">
                                                    <div class="w-full text-center overflow-hidden text-ellipsis whitespace-nowrap"><?=$i['language_abbr']?></div> <!-- Affichage de la langue choisi par l'utilisateur pour l'anime. -->
                                                </span>
                                            </div>
                                        </div>
                                        <div role="cell" class="col-span-2 inline-block mt-2 mr-1 lg:m-0 lg:block">
                                            <div class="max-w-40">
                                                <span class="inline-flex items-center rounded-full font-medium text-xs bg-blue-200 text-blue-700 px-2.5 py-0.5 leading-4">
                                                    <div class="overflow-hidden text-ellipsis whitespace-nowrap">Ep. <?=$i['current_episode']?></div> <!-- Affichage du dernier épisode vu par l'utilisateur pour l'anime. -->
                                                </span>
                                            </div>
                                            <div class="m-2 lg:flex items-center text-sm text-gray-500 hidden">
                                                <svg data-v-6b1133e1="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="shrink-0 h-5 w-5 text-gray-400 mr-1.5">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <time datetime="<?=$i['updated_at']?>">
                                                    <?=$i['updated_at']?> <!-- Affichage de la date à laquelle l'utilisateur à regarder le dernier épisode. -->
                                                </time>
                                            </div>
                                        </div>
                                        <div role="cell" class="col-span-2 inline-block lg:block">
                                            <div class="flex items-end space-x-1">
                                                <div class="max-w-40">
                                                    <span class="inline-flex items-center rounded-full font-medium text-xs bg-blue-200 text-blue-700 px-2.5 py-0.5 leading-4 max-w-40">
                                                        <div class="overflow-hidden text-ellipsis whitespace-nowrap">Ep. <?=$i['last_released_episode']?></div> <!-- Affichage du dernier épisode sorti pour l'anime. -->
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-2 lg:flex items-center text-sm text-gray-500 hidden">
                                                <svg data-v-6b1133e1="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="shrink-0 h-5 w-5 text-gray-400 mr-1.5">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <time datetime="<?=$i['anime_updated_at']?>">
                                                    <?=$i['anime_updated_at']?> <!-- Affichage de la date à laquelle est sorti le dernier épisode pour l'anime. -->
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end ml-auto">
                                        <a href="/update_delete.php?id=<?=htmlspecialchars($_GET['id']);?>&anime_id=<?=$i['anime_id']?>">
                                            <button type="button" id ="editManga" class="flex items-center justify-center rounded-lg p-2.5 border-2 text-sm font-medium bg-blue-700 text-white">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20,16v4a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V6A2,2,0,0,1,4,4H8" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                    <polygon fill="none" points="12.5 15.8 22 6.2 17.8 2 8.3 11.5 8 16 12.5 15.8" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                </svg>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                <?php endforeach;?>
            <?php endif;?>

            <footer class="sm:px-6 py-5 border-b border-gray-300">
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
                    <div class="flex items-center w-full ml-4 mt-2 sm:ml-2">
                        <input class="h-5 w-4 invisible" type="checkbox" id="">
                        <div class="w-full sm:pl-10">
                            <div class="flex-1 flex justify-end -ml-4 sm:ml-0 sm:items-center sm:justify-between w-full">
                                <div class="hidden sm:flex">
                                    <div class="animate-pulse flex space-x-4 w-48" style="display:none;"></div>
                                    <p class="text-sm leading-5 text-gray-700">
                                        Affichage de 
                                        <span class="font-medium"><?=$ListWithAnimesByUser['first_result_page']?></span>
                                        à
                                        <span class="font-medium"><?=$ListWithAnimesByUser['last_result_page']?></span>
                                        sur
                                        <span class="font-medium"><?=$ListWithAnimesByUser['count_total']?></span>
                                        animes.
                                    </p>
                                </div>
                                <nav class="relative hidden sm:inline-flex shadow-sm rounded-l-md" aria-label="Pagination"> <!-- Mise en place de la pagination avec vérification de la page dans l'URL, affichage de Previous ou Next suivant si il y a une page avant ou après de disponible puis, création du lien pour aller à la page précédente ou suivante (avec le bouton). -->
                                    <?php if ($currentPage > 1):?>
                                        <a href="/?id=<?= htmlspecialchars($_GET['id']);?>&page=<?= $currentPage - 1;?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Précédent</a>
                                    <?php endif;?>
                                    <?php if ($currentPage < $ListWithAnimesByUser['page']['total_pages']):?>
                                        <a href="/?id=<?= htmlspecialchars($_GET['id']); ?>&page=<?= $currentPage + 1;?>" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Suivant</a>
                                    <?php endif; ?>
                                </nav>
                                <nav class="relative sm:hidden inline-flex shadow-sm rounded-l-md" aria-label="Pagination">
                                    <?php if ($currentPage > 1): ?>
                                        <a href="/?id=<?= htmlspecialchars($_GET['id']); ?>&page=<?= $currentPage - 1;?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Précédent</span>
                                            <svg class="h-5 w-5 opacity-50" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($currentPage < $ListWithAnimesByUser['page']['total_pages']):?>
                                        <a href="/?id=<?= htmlspecialchars($_GET['id']);?>&page=<?= $currentPage + 1;?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Suivant</span>
                                            <svg class="h-5 w-5 opacity-50" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif;?>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>

