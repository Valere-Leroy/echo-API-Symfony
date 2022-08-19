<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\EchoPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/api/tag", name="api_tag_")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function ApiTagList(TagRepository $tags)
    {

        $query = $tags->findBy(array(), ['name' => 'asc']);

        $data = [
            'tags' => $query,
        ];

        return $this->json($data, 200, [], ['groups' => ['tagList']]);
    }

    /**
     * @Route("/{id}/echo/list", name="echo_list", requirements={"id"="\d+"})
     */
    public function ApiByTagEchoList(Tag $tag, EchoPostRepository $echos, Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $parameters = [];
        if (!$authChecker->isGranted('ROLE_MODERATOR')) {
            $parameters += ["moderate" => false];
        }
        $data = json_decode($request->getContent(), true);
        $filterRequest = $data['filter'];
        $orderRequest = $data['order'];
        
        if (isset($filterRequest)) {
            $filter = $filterRequest;
        } else {
            $filter = 'createdAt';
        }

        if (isset($orderRequest)) {
            $order = $orderRequest;
        } else {
            $order = 'DESC';
        }

        $parameters = 
        [
            'filter' => $filter,
            'order' => $order,
            'tag' => $tag
        ];

        $queryEchos = $echos->findList($parameters);

        $data = [
            'tag' => $tag,
            'echos' => $queryEchos,
        ];

        return $this->json($data, 200, [], ['groups' => ['tagList', 'echoList']]);
    }

}
