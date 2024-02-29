# Projet CDA - Neverforgetyouranimes


Pour le projet CDA, j'ai choisi de créer une bibliothèque d'animes. 
L'implémentation de la gestion utilisateur n'est pas encore faites (Je compte me servir de ce projet pour mon projet fil rouge), mais vous pourrez naviguer sur ce site comme si vous êtiez l'un des utilisateurs créer dans la BDD (Ils sont au nombre de 7).

Les fonctions utilisées pour ce projet se trouvent toutes dans le fichier `functions.php`. 

Vous trouverez à l'intérieur de nombreuses fonctions de type CRUD ainsi qu'une fonction de nettoyage des variables afin d'éviter des problèmes de sécurité liées aux injections de code SQL ou bien au Cross-Site Scripting tout en vérifiant les données.


Ce projet devrait pouvoir être utilisé avec n'importe quel serveur local qui sait lire du PHP et qui dispose d'un système de gestion de BDD (Laragon, Homestead, Mamp...), si vous ne disposez pas d'un serveur local, je vous présentes l'installation de Laragon :


 1. En premier lieu, veuillez télécharger Laragon avec ce lien : 
        -> https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon-wamp.exe


 2. Une fois l'installation de Laragon effectuée, nous allons vérifier que les outils nécessaires à l'utilisation du site sont bien installés. 
    
    Pour ce faire, il suffit d'ouvrir Laragon afin de voir si Apache/MySQL/PHP sont bien installés. 
    Dans la fenêtre, nous pourrons apercevoir le versionning d'Apache & de MySQL (Si l'installation n'a pas eu de problèmes).

        ![Image Fenêtre Laragon](projetcda_neverforgetyouranimes/public/img/laragon.png)

    Si vous ne disposez pas de ceci, pas de soucis, on va installer ça sans problèmes, il vous suffit de faire :
        Clic droit -> Outils (Ou Tools) -> Quick Add
         - > php
         - > apache
         - > *phpmyadmin

    Avec ça, vous devriez être en mesure d'héberger le site sur votre serveur local.


 3. Maintenant, nous allons ajouter notre dossier à Laragon (Ou à votre autre serveur local), pour ce qui est de Laragon, il vous suffit de cliquer sur Dossier WWW (Ou Root) afin de vous ouvrir le dossier ou vous devrez transférer le contenu du dossier de notre site (Après l'avoir télécharger et extrait).
    Si vous êtes plus à l'aise avec l'invite de commande, vous pouvez également faire un 
        `git clone https://github.com/MarKowPowLow/Projet-CDA---Neverforgetyouranimes.git`
    dans le terminal de Laragon.


 4. J'espère que vous n'êtez pas trop pressé car il vous faudra encore avoir le Laissé Passé A38 ce qui ne sera pas une mince affaire... 
    Ou sinon, nous mettrons en place notre base de données avec l'aide de votre gestionnaire de BDD (Pour Laragon, phpmyadmin !). Vous trouverez le fichier MPD dans le dossier archives (Et oui, il prend la poussière depuis).

    Pour ceux qui ne seraient pas trop quoi faire de ce fichier, je vais vous expliquer ! Ce fichier sert à créer une base de données déjà faites (Et c'était long en voulant la personnalisée quelque peu...), pour pouvoir l'utilisé, on va aller sur Laragon et cliquer sur Base de données (ou Database), cela vous ouvrira alors une page internet qui vous demandera vos logs.
    Dans le premier champ, il faudra écrire `root` et dans le second rien du tout !

    Par la suite, il va falloir se diriger dans Import et vous cliquerez sur `Choisir un fichier`.
    Vous n'aurez qu'à aller dans votre disque dur d'installation -> laragon -> www -> neverforgetyouranimes -> archives -> MPD.SQL
    Ensuite, il suffira de descendre en bas de la page et de faire `Import`.

    Vous pourrez voir maintenant en rafraichissant votre page, qu'une nouvelle base de données qui s'appelle `neverforgetyouranimes` a été créer.

    Normalement, vous n'aurez plus besoin de repasser par là, sauf si vous souhaitez ajouter un utilisateur à la main (ou des animes/épisodes).


 5. On va enfin pouvoir découvrir le site web ! Pour ce faire, il va suffir d'aller ici : 
    
        `http://projetcda_neverforgetyouranimes.test`
    
    Cependant ! Si vous allez sur le site comme ça, vous verrez de nombreuses erreurs car la gestion utilisateur n'a pas été faite, il vous faudra renseigné un id d'utilisateur, pour ce faire, vous pouvez cliquer directement sur ce lien : 

        `http://projetcda_neverforgetyouranimes.test/?id=1`

    Vous arriverez sur l'espace utilisateur de notre cher Arima Kõsei (Si cela vous intéresses et que vous aimez la musique, je vous recommandes l'anime ^.^).

    Le site sur notre page principale peut ce décomposer en deux parties pour le moment, 

    Une navbar qui nous servira à retourner sur notre Dashboard (Notre main page, la seule qui est fonctionnelle !). Dans cette navbar nous pouvons apercevoir un magnifique logo, des boutons qui emmène tous au même endroit et l'image de profil de notre cher Arima ! Si vous cliquez dessus, vous pourrez voir un menu déroulant qui emmènera notre utilisateur vers sa page de compte, une page paramètres et un bouton qui servira à se déconnecter plus tard.

     Notre body qui sera composé d'un header avec un champ de texte qui nous montrera combien d'animés sont montrés sur cette page, jusqu'à combien et combien il y en à en tout. Et à la droite un bouton pour refresh la page (au cas où on aurait ajouter un anime ou modifier un anime sur une autre page) et un bouton d'ajout d'anime qui nous mèneras à notre page Create. 

    Ensuite, nous aurons le corps principal de notre requête Read qui va lister tous les animes que l'ont suit, le status qu'on leur donne, la langue dans laquelle on le regarde, le dernier épisode vu (avec sa date de mise à jour) et le dernier épisode sorti pour la langue qu'on a choisi (avec sa date de sortie) et enfin un bouton qui nous emmènes vers la page d'édition/de suppression.

    Enfin, nous trouverons un footer qui affichera au cas où nous aurions beaucoup d'animes suivi une deuxième fois notre champ de texte et une navigation dans nos pages sur la droite.


    Maintenant, nous allons pouvoir ajouter un anime à notre liste, pour ce faire, on va cliquer sur le bouton d'ajout qui nous emmèneras au formulaire d'ajout.

    Nous avons des menus déroulants qui nous laisses choisir ce que l'ont veut (Attention, il n'y a pas des épisodes exemples pour tout les animes). En premier lieu, on va choisir l'anime puis le valider et cela va nous afficher les langages disponibles pour cet anime.
    Après avoir valider le langage, le menu déroulant du dernier épisode vu sera rempli des épisodes correspondant à la langue et l'anime choisi. 
    Et n'oubliez pas de choisir le statut que vous voulez lui donner avant d'ajouter l'anime à votre liste. 

    Une fois fait, vous aurez un petit message vous confirmant ou non le bon déroulement de l'ajout. On a plus qu'à repartir sur le Tableau de bord pour vérifier que notre anime a bien été ajouter.

    
    Enfin, nous allons découvrir la partie d'édition, quand vous cliquerez sur le bouton d'édition, nous serons emmenés sur le formulaire d'édition/de suppression de l'anime en question.

    Nous aurons le même genre de formulaire que le précédent sauf qu'il sera pré-rempli. On ne pourra pas choisir d'autres animes mais on pourra toujours choisir une autre langue (si elles existent), un autre statut et d'autres épisodes. 
    Là, nous aurons un bouton de suppression qui actionnera notre fonction delete et un bouton d'édition afin de mettre à jour les données rentrées. 

Voilà ! J'espère que ça vous aura intéressé, pour ma part, ça a été un challenge sur le CSS avec le framework tailwind (Non pas que tailwind est difficile d'utilisation, mais je déteste le CSS hahaha). 
