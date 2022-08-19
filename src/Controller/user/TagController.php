<?php

namespace App\Controller\user;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
* @Route("/tag", name="tag_")
*/
class TagController extends AbstractController
{
    

    /**
    * @Route("/list", name="list", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function TagList(TagRepository $tagRepository)
    {

            return $this->render('user/user.tag.edit.html.twig', [
                'tags' => $tagRepository->findAll()
            ]);
        
    }

    /**
    * @Route("/{id}/user/ajax", name="tag_ajax", requirements={"id"="\d+"}, methods={"POST"})
    */
    public function TagUserEditAjax(Tag $tag, Request $request, EntityManagerInterface $em)
    {

        $currentUser = $this->getUser();
        $sub = $request->request->get('sub');
        $subBoolean = ($sub === 'true');
        if ($subBoolean) {
            $currentUser->removeTag($tag);
        } else {
            $currentUser->addTag($tag);
        }

        $em->flush();
        
        return $this->json("tag correctement modifié", 200);
    }

}
