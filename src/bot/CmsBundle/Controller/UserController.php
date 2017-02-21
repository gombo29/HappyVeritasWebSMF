<?php

namespace bot\CmsBundle\Controller;

use bot\CmsBundle\Entity\BotConfig;
use bot\CmsBundle\Entity\BotSender;
use bot\CmsBundle\Entity\BotSenderNews;
use bot\CmsBundle\Form\BotConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use bot\CmsBundle\Form\UserSearchType;

class UserController extends Controller
{
    public function userAction(Request $request, $menu, $page)
    {
        $pagesize = 20;
        $searchEntity = new BotSender();
        $searchForm = $this->createForm(new UserSearchType(), $searchEntity);
        $search = false;
        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('botCmsBundle:BotSender')->createQueryBuilder('n');

        if ($search) {
            if ($searchEntity->getLastName() && $searchEntity->getLastName() != '') {
                $qb->andWhere('n.lastName like :lastname')
                    ->setParameter('lastname', '%' . $searchEntity->getLastName() . '%');
            }

            if ($searchEntity->getFirstName() && $searchEntity->getFirstName() != '') {
                $qb->andWhere('n.firstName like :firstName')
                    ->setParameter('firstName', '%' . $searchEntity->getFirstName() . '%');
            }


            if ($searchEntity->getGender() && $searchEntity->getGender() != '') {
                $qb->andWhere('n.gender like :gender')
                    ->setParameter('gender', $searchEntity->getGender());
            }

            if ($searchEntity->sdate && $searchEntity->edate) {
                $qb
                    ->andWhere($qb->expr()->between('n.sentDate', ':sdate', ':edate'))
                    ->setParameter('sdate', $searchEntity->sdate)
                    ->setParameter('edate', $searchEntity->edate);
            }
        }

        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(n.id)')->getQuery()->getSingleScalarResult();
        /**@var BotSender[] $botSender */
        $botSender = $qb
            ->orderBy('n.id', 'desc')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $currentRoute = $request->getUri();

        return $this->render('botCmsBundle:User:users.html.twig', array(
            'menu' => $menu,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'count' => $count,
            'page' => $page,
            'currentRoute' => $currentRoute,
            'search' => $search,
            'botSender' => $botSender,
            'searchform' => $searchForm->createView(),

        ));
    }

    public function detailAction($menu, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('botCmsBundle:BotSender')->createQueryBuilder('s');
        /**@var BotSender[] $botSdr */
        $botSdr = $qb
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        $q = $em->getRepository('botCmsBundle:BotSenderNews')->createQueryBuilder('n');
        /**@var BotSenderNews[] $botSdrNews */
        $botSdrNews = $q
            ->select('b.name , b.id , n.createdDate, count(n.id) as cnt')
            ->leftJoin('n.botBlock', 'b')
            ->where('n.botSender = :id')
            ->groupBy('b.id')
            ->setParameter('id', $id)
            ->orderBy('cnt', 'desc')
            ->getQuery()
            ->getArrayResult();

        return $this->render('botCmsBundle:User:detail.html.twig', array(
            'menu' => $menu,
            'botSdrNews' => $botSdrNews,
            'botSdr' => $botSdr,
        ));
    }

    public function configAction(Request $request, $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotConfig')->find(1);

        $form = $this->createConfigform($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Config ');
                $this->get('loggod')->writeLog('Update Bot Config');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Тохиргоо амжилттай хийгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_user_config', array('menu' => $menu)));
            }
        }

        return $this->render('botCmsBundle:User:config.html.twig', array(
            'menu' => $menu,
            'form' => $form->createView(),
        ));
    }

    private function createConfigform(BotConfig $entity)
    {
        $form = $this->createForm(new BotConfigType(), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }
}
