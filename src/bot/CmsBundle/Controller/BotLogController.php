<?php

namespace bot\CmsBundle\Controller;

use bot\CmsBundle\Form\AdminlogSearchType;
use bot\CmsBundle\Entity\BotLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BotLogController extends Controller
{
    /**
     * Lists all Adminlog entities.
     */
    public function indexAction($page, Request $request)
    {
        $entity = new BotLog();
        $form = $this->createForm(new AdminlogSearchType(), $entity);
        $search = false;
        if ($request->get("submit") == 'submit') {
            $form->bind($request);
            $search = true;
        }

        $pagesize = 20;
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('botCmsBundle:BotLog');

        $queryBuilder = $repository->createQueryBuilder('lo');

        if ($search) {
            if ($entity->username && $entity->username != '') {
                $queryBuilder->andWhere('LOWER(lo.username) like :username')->setParameter('username', strtolower('%' . $entity->username . '%'));
            }
            if ($entity->fromognoo && $entity->fromognoo != '') {
                $queryBuilder->andWhere('lo.cognoo >= :fromognoo')->setParameter('fromognoo', $entity->fromognoo);
            }
            if ($entity->toognoo && $entity->toognoo != '') {
                $queryBuilder->andWhere('lo.cognoo < :toognoo')->setParameter('toognoo', $entity->toognoo);
            }
            if ($entity->log && $entity->log != '') {
                $queryBuilder->andWhere('LOWER(lo.log) like :log')->setParameter('log', strtolower('%' . $entity->log . '%'));
            }
            if ($entity->ip && $entity->ip != '') {
                $queryBuilder->andWhere('LOWER(lo.ip) like :ip')->setParameter('ip', strtolower('%' . $entity->ip . '%'));
            }
        }

        $countQueryBuilder = clone $queryBuilder;
        $count = $countQueryBuilder->select('count(lo.id)')->getQuery()->getSingleScalarResult();

        $entities = $queryBuilder->orderBy('lo.id', 'DESC')->setFirstResult(($page - 1) * $pagesize)->setMaxResults($pagesize)->getQuery()->getArrayResult();

        return $this->render('botCmsBundle:Log:index.html.twig', array(
            'entities' => $entities,
            'count' => $count,
            'page' => $page,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'entity' => $entity,
            'form' => $form->createView(),
            'search' => $search
        ));
    }
}
