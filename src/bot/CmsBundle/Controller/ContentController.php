<?php

namespace bot\CmsBundle\Controller;

use bot\CmsBundle\Entity\BotContent;
use bot\CmsBundle\Form\BotContentSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
    public function contentAction(Request $request, $menu, $page)
    {
        $pagesize = 20;
        $searchEntity = new BotContent();
        $searchForm = $this->createForm(new BotContentSearchType(), $searchEntity);
        $search = false;
        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('n');

        if ($search) {
            if ($searchEntity->getTitle() && $searchEntity->getTitle() != '') {
                $qb->andWhere('n.title like :title')
                    ->setParameter('title', '%' . $searchEntity->getTitle() . '%');
            }

            if ($searchEntity->sdate && $searchEntity->edate) {
                $qb
                    ->andWhere($qb->expr()->between('n.createdDate', ':sdate', ':edate'))
                    ->setParameter('sdate', $searchEntity->sdate)
                    ->setParameter('edate', $searchEntity->edate);
            }
        }

        $qb
            ->andWhere('n.type = 1');

        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(n.id)')->getQuery()->getSingleScalarResult();

        /**@var BotContent[] $botContent */
        $botContent = $qb
            ->leftJoin('n.botBlock', 'b')
            ->addSelect('b')
            ->orderBy('n.id', 'desc')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $currentRoute = $request->getUri();
        return $this->render('botCmsBundle:Content:contents.html.twig', array(
            'menu' => $menu,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'count' => $count,
            'page' => $page,
            'currentRoute' => $currentRoute,
            'search' => $search,
            'botContent' => $botContent,
            'searchform' => $searchForm->createView(),
        ));
    }

}
