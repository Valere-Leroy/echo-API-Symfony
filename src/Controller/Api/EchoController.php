<?php

namespace App\Controller\Api;

use App\Entity\Commentary;
use App\Entity\EchoPost;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\Validator\CommentaryHandler;
use App\Entity\Validator\EchoHandler;
use App\Entity\Vote;
use App\Repository\EchoPostRepository;
use App\Repository\TagRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/echo", name="api_echo_")
 */
class EchoController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function ApiEchoList(EchoPostRepository $echos, Request $request, AuthorizationCheckerInterface $authChecker)
    {
        //Création d'un tableau vide dans lequel seront stockés les paramètres de la requête(order, column ect.)
        $parameters = [];

        //ajoute un paramètre de modération si l'utilisateur n'est pas au moins modérateur, masquant les échos modérés.
        if (!$authChecker->isGranted('ROLE_MODERATOR')) {
            $parameters += ["moderate" => false];
        }
        //Transformation du contenu json en array.
        $data = json_decode($request->getContent(), true);

        //définit le filtre qui sera utilisé par la requete et lui donne une valeur par défaut si il est vide.
        if (isset($data['filter'])) {
            $filter = $data['filter'];
        } else {
            $filter = 'createdAt';
        }

        //définit l'ordre qui sera utilisé par la requete et lui donne une valeur par défaut si il est vide.
        if (isset($data['order'])) {
            $order = $data['order'];
        } else {
            $order = 'DESC';
        }

        //définit un terme à rechercher dans la table
        if (isset($data['search'])) {
            $parameters += ['search' => $data['search']];
        }

        //ajoute le filtre et l'ordre définit au tableau de paramètre de la requête.
        $parameters +=
            [
            'filter' => $filter,
            'order' => $order,
        ];

        // stocke les résultats de la requête selon les paramètres définit
        $queryEchos = $echos->findList($parameters);

        // prépare les données à envoyer en réponse à la requête
        $data = [
            'echos' => $queryEchos,
        ];

        //convertit les données en JSON avec un code http 200, le groupe permet de définir quels champs des entités sont affichés.
        return $this->json($data, 200, [], ['groups' => ['echoList']]);
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function ApiEchoAdd(
        Request $request,
        EchoPostRepository $echoPostRepository,
        TagRepository $tagRepository,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,
        ValidatorInterface $validator
    ) {
        //vérifie que l'utilisateur est au moins connecté
        if ($authChecker->isGranted('ROLE_USER')) {

            //Transformation du contenu json en array.
            $data = json_decode($request->getContent(), true);

            if (!empty($data["echoNew"]['title'])) {
                //vérifie qu'aucun écho ne posséde exactement le même titre ou le même contenu
                $echoCheckTitle = $echoPostRepository->findBy(["title" => $data["echoNew"]['title']]);
                if (empty($echoCheckTitle)) {
                    // Vérification  par le biais d'une entité avec les Constraints symfony
                    $echoHandler = new EchoHandler();
                    $echoHandler->setTitle($data["echoNew"]['title']);
                    if (!empty($data["echoNew"]['content'])) {
                        $echoHandler->setContent($data["echoNew"]['content']);
                    }
                    if (!empty($data["echoNew"]['image'])) {
                        $echoHandler->setImage($data["echoNew"]['image']);
                    }
                    $echoHandler->setAdress($data["echoNew"]['adress']);
                    if (!empty($data["echoNew"]['latitude'])) {
                        $echoHandler->setLatitude($data["echoNew"]['latitude']);
                    } else {
                        return $this->json('La latitude est requise', 418);
                    }
                    if (!empty($data["echoNew"]['longitude'])) {
                        $echoHandler->setLongitude($data["echoNew"]['latitude']);
                    } else {
                        return $this->json('La longitude est requise', 418);
                    }
                    if (empty($data["echoNew"]['tags'])) {
                        return $this->json('Les tags sont requis', 400);
                    }
                    // Gestion des erreurs avec le Validator Symfony
                    $titleError = $validator->validateProperty($echoHandler, 'title');
                    $contentError = $validator->validateProperty($echoHandler, 'content');
                    $imageError = $validator->validateProperty($echoHandler, 'image');
                    $adressError = $validator->validateProperty($echoHandler, 'adress');
                    $latitudeError = $validator->validateProperty($echoHandler, 'latitude');
                    $longitudeError = $validator->validateProperty($echoHandler, 'longitude');

                    // Ajout dans un array les erreurs du formulaires si > 0
                    $formErrors = [];
                    if (count($titleError) > 0) {
                        $formErrors['titleError'] = $titleError[0]->getMessage();
                    }
                    if (count($contentError) > 0) {
                        $formErrors['contentError'] = $contentError[0]->getMessage();
                    }
                    if (count($imageError) > 0) {
                        $formErrors['imageError'] = $imageError[0]->getMessage();
                    }
                    if (count($adressError) > 0) {
                        $formErrors['adressError'] = $adressError[0]->getMessage();
                    }
                    if (count($latitudeError) > 0) {
                        $formErrors['latitudeError'] = $latitudeError[0]->getMessage();
                    }
                    if (count($longitudeError) > 0) {
                        $formErrors['longitudeError'] = $longitudeError[0]->getMessage();
                    }
                    // Vérif a la main de la latitude & longitude
                    if (!is_numeric($data["echoNew"]['latitude'])) {
                        $formErrors['latitudeError'] = "La latitude n'est pas au format numéric";
                    }
                    if (!is_numeric($data["echoNew"]['longitude'])) {
                        $formErrors['longitudeError'] = "La longitude n'est pas au format numéric";
                    }
                    if ($formErrors) {
                        return $this->json($formErrors, Response::HTTP_UNAUTHORIZED);
                    }

                    //Appelle un nouvel objet de l'entité EchoPost
                    $echoNew = new EchoPost;
                    $echoNew->setTitle($data["echoNew"]['title']);
                    if (!empty($data["echoNew"]['content'])) {
                        $echoNew->setContent($data["echoNew"]['content']);
                    }
                    if (!empty($data["echoNew"]['image'])) {
                        $echoNew->setImage($data["echoNew"]['image']);
                    }
                    $echoNew->setAdress($data["echoNew"]['adress']);
                    $echoNew->setLatitude($data["echoNew"]['latitude']);
                    $echoNew->setLongitude($data["echoNew"]['longitude']);
                    //parcours les id de tag fournit, vérifie qu'un tag correspond à cet id, et le set dans l'écho
                    foreach ($data["echoNew"]['tags'] as $key => $tagId) {
                        $tag = $tagRepository->find($tagId);
                        if (isset($tag)) {
                            $echoNew->addTag($tag);
                        }
                    }
                    //l'utilisateur de l'écho avec l'utilisateur courant
                    $echoNew->setUser($this->getUser());
                    //sauvegarde ce nouvel écho
                    $em->persist($echoNew);
                    $em->flush();

                    //lance la fonction créant des notifications pour la création de ce nouvel écho
                    $this->ApiEchoNotification($echoNew);
                    //stocke un message de confirmation ainsi que l'écho tout juste créer pour la réponse JSON
                    $data = [
                    'message' => "l'écho a été créé correctement",
                    'echo' => $echoNew,
                ];
                    //convertit les données en JSON avec un code http 200, le groupe permet de définir quels champs des entités sont affichés.
                    return $this->json($data, 200, [], ['groups' => ['echoShow', 'echoList']]);
                } else {
                    return $this->json("Un écho possédant ce titre existe déjà", 401);
                }
            } else {
                return $this->json("Un écho doit posséder un titre", 401);
            }
        } else {
            return $this->json("utilisateur non connecté ou autorisé", 401);
        }
    }

    public function ApiEchoNotification(EchoPost $echo)
    {
        // appelle le manager d'entité pour ne pas avoir à le passer en paramètre de la fonction, prévient une erreur
        $em = $this->getDoctrine()->getManager();

        // appelle le repository de l'entité User pour ne pas avoir à le passer en paramètre de la fonction, prévient une erreur
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        // récupére les tags de l'écho tout juste créer
        $tags = $echo->getTags()->getValues();

        // trouve les utilisateurs abonnés à ces tags
        $users = $userRepository->findByTags($tags);

        // parcours tous ces utilisateurs pour leur créer une notification dédié à cet écho.
        foreach ($users as $key => $user) {

            //appel un nouvel objet de la classe notification
            $notification = new Notification;
            //set l'utilisateur de la notification
            $notification->setUser($user);
            //set l'écho juste créer dans notification
            $notification->setEchoPost($echo);

            //sauvegarde la nouvelle notification
            $em->persist($notification);
            $em->flush();
        }
        //valide la réussite de l'opération
        return new Response('success');
    }

    /**
     * @Route("/{id}/show", name="show", requirements={"id"="\d+"})
     */
    public function ApiEchoShow(EchoPost $echo)
    {

        //stocke l'écho pour la réponse JSON
        $data = [
            'echo' => $echo,
        ];

        //convertit les données en JSON avec un code http 200, le groupe permet de définir quels champs des entités sont affichés.
        return $this->json($data, 200, [], ['groups' => ['echoShow', 'echoList']]);
    }

    /**
     * @Route("/{id}/commentary/add", name="commentary_add", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function ApiEchoCommentaryAdd(
        EchoPost $echo,
        Request $request,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,
        ValidatorInterface $validator
    ) {
        //vérifie que l'utilisateur est au moins connecté
        if ($authChecker->isGranted('ROLE_USER')) {
            //décrypte le contenu de la requête envoyée et la stocke dans un array
            $content = $request->getContent();
            $data = json_decode($content, true);

            $commentaryHandler = new CommentaryHandler();
            $commentaryHandler->setContent($data['content']);

            // Gestion des erreurs
            $contentError = $validator->validateProperty($commentaryHandler, 'content');

            $formError = [];
            if (count($contentError) > 0) {
                $formError['contentError'] = $contentError[0]->getMessage();
            }
            // Retourne les erreurs JSON si l'array formError contient au moins une erreur
            if ($formError) {
                return $this->json($formError, Response::HTTP_FORBIDDEN);
            }

            // appel un nouvel objet de l'entité Commentary et set son user avec l'utilisateur courant et l'écho concerné par la route
            $commentary = new Commentary;
            $commentary->setEchoPost($echo);
            $commentary->setUser($this->getUser());
            $commentary->setContent($data['content']);

            $em->persist($commentary);
            $em->flush();

            // les données en cas de succès
            $data = [
                'message' => 'Le commentaire est correctement posté',
                'echo' => $echo,
            ];
            //convertit les donnés en json avec un code 200
            return $this->json($data, 200, [], ['groups' => ['echoShow', 'echoList']]);
        } else {
            return $this->json("utilisateur non connecté ou autorisé", 401);
        }
    }

    /**
     * @Route("/{id}/vote/check", name="vote_check", requirements={"id"="\d+"})
     */
    public function apiEchoVoteCheck(
        EchoPost $echo,
        VoteRepository $voteRepository,
        AuthorizationCheckerInterface $authChecker
    ) {

        // vérifie que l'utilisateur est au moins connecté
        if ($authChecker->isGranted('ROLE_USER')) {
            // set les critère d'écho et d'utilisateur pour le findBy
            $criteria = ['echoPost' => $echo, 'user' => $this->getUser()];
            $check = $voteRepository->findBy($criteria);


            // vérifie que le vote n'existe pas déjà en bdd
            if (empty($check)) {
                return $this->json(false, 200);
            }
            return $this->json(true, 200);
        } else {
            return $this->json("utilisateur non connecté ou autorisé", 401);
        }
    }

    /**
     * @Route("/{id}/vote", name="vote", requirements={"id"="\d+"})
     */
    public function apiEchoVote(
        EchoPost $echo,
        Request $request,
        EntityManagerInterface $em,
        VoteRepository $voteRepository,
        AuthorizationCheckerInterface $authChecker
    ) {

        // vérifie que l'utilisateur est au moins connecté
        if ($authChecker->isGranted('ROLE_USER')) {
            // set les critère d'écho et d'utilisateur pour le findBy
            $criteria = ['echoPost' => $echo, 'user' => $this->getUser()];
            $check = $voteRepository->findBy($criteria);

            $data = json_decode($request->getContent('vote'), true);
            $vote = $data['vote'];

            // vérifie que le vote n'existe pas déjà en bdd et qu'il est positif
            if (empty($check) && $vote === true) {

                //appel un nouvel objet de l'entité Vote et set son écho et l'utilisateur avec l'utilisateur courant
                $addVote = new Vote;
                $addVote->setEchoPost($echo);
                $addVote->setUser($this->getUser());

                //sauvegarde ce nouveau vote
                $em->persist($addVote);
                $em->flush();

                //définit le code et le message de retour de la réponse Json
                $code = 200;
                $message = "Le vote a bien été pris en compte";
            
            //gère le cas où un vote existe déjà
            } elseif (!empty($check) && $vote === true) {
                //définit le code et le message de retour de la réponse Json
                $code = 403;
                $message = "l'utilisateur a déjà voté pour cette question";

            //gére le cas de l'annulation du vote via un false
            } elseif (!empty($check) && $vote === false) {
                //supprime le vote
                $em->remove(reset($check));
                $em->flush();

                //définit le code et le message de retour de la réponse Json
                $code = 200;
                $message = "Le vote a bien été annulé";

            //prend en charge l'impossibilité d'un vote négatif
            } elseif (empty($check) && $vote === false) {

                //définit le code et le message de retour de la réponse Json
                $code = 403;
                $message = "Le vote ne peut être négatif";
            }
        } else {
            //définit le code et le message de retour de la réponse Json
            $message = "utilisateur non connecté ou autorisé";
            $code = 401;
        }

        return $this->json($message, $code);
    }
}
