<?php

namespace bot\CmsBundle\Controller;

use bot\CmsBundle\Entity\SocialContent;
use bot\CmsBundle\Entity\SocialTabCategory;
use bot\CmsBundle\Form\SocialContentSearchType;
use bot\CmsBundle\Form\SocialContentType;
use mobicom\CmsBundle\Entity\Product;
use mobicom\CmsBundle\Entity\ProductImage;
use mobicom\CmsBundle\Entity\ScratchCard;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{

// ==========  Bot Get List ========

    public function indexAction(Request $request, $page, $type)
    {
        $pagesize = 20;
        $searchEntity = new SocialContent();
        $searchForm = $this->createForm(new SocialContentSearchType(), $searchEntity);

        $search = false;
        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('botCmsBundle:SocialContent')->createQueryBuilder('n');
        if ($type != 0) {
            $qb
                ->where('n.tab = :tid')
                ->setParameter('tid', $type)
                ->orderBy('n.myorder', 'asc');
        } else {
            $qb->orderBy('n.id', 'desc');
        }

        if ($search) {
            if ($searchEntity->getTitle() && $searchEntity->getTitle() != '') {
                $qb->andWhere('n.title like :title')
                    ->setParameter('title', '%' . $searchEntity->getTitle() . '%');
            }
        }

        $countQueryBuilder = clone $qb;
        $count = $countQueryBuilder->select('count(n.id)')->getQuery()->getSingleScalarResult();

        /**@var SocialContent[] $socialContent */
        $socialContent = $qb
            ->leftJoin('n.tab', 't')
            ->addSelect('t')
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->getQuery()
            ->getArrayResult();

        $currentRoute = $request->getUri();

        return $this->render('botCmsBundle:Social:index.html.twig', array(
            'socialContent' => $socialContent,
            'page' => $page,
            'pagesize' => $pagesize,
            'pagecount' => ($count % $pagesize) > 0 ? intval($count / $pagesize) + 1 : intval($count / $pagesize),
            'count' => $count,
            'currentRoute' => $currentRoute,
            'search' => $search,
            'type' => $type,
            'searchform' => $searchForm->createView(),
        ));
    }

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('botCmsBundle:SocialContent')->createQueryBuilder('n');


        /**@var SocialContent[] $socialContent */
        $qb
            ->where('n.id = :id')
            ->setParameter('id', $id);

        $socialContent = $qb
            ->leftJoin('n.tab', 't')
            ->addSelect('t')
            ->getQuery()
            ->getArrayResult();

        $imgProIds = array_map(function ($n) {
            return $n['proImgId'];
        }, $socialContent);


        /**@var ProductImage[] $proImg */
        $qb = $em->getRepository('mobicomCmsBundle:ProductImage')->createQueryBuilder('n');
        $proImg = $qb
            ->leftJoin('n.product', 'p')
            ->addSelect('p')
            ->andWhere($qb->expr()->in('p.id', ':ids'))
            ->setParameter('ids', $imgProIds)
            ->getQuery()
            ->getArrayResult();


        foreach ($socialContent as $key => $entity) {
            $socialContent[$key]['images'] = array();
            foreach ($proImg as $a) {
                if ($a['product']['id'] == $entity['proImgId']) {
                    $socialContent[$key]['images'][] = $a;
                }
            }
        }
        return $this->render('botCmsBundle:Social:show.html.twig', array(
            'socialContent' => $socialContent,
        ));
    }


    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /**@var SocialContent[] $entity */
        $entity = new SocialContent();

        $form = $this->createContentform($entity, false);
        /**@var Product[] $proImg */
        $qb = $em->getRepository('mobicomCmsBundle:Product')->createQueryBuilder('p');

        /**@var ScratchCard[] $card */
        $qbcard = $em->getRepository('mobicomCmsBundle:ScratchCard')->createQueryBuilder('p');


        $pro = $qb
            ->andwhere('p.type = 1')
            ->getQuery()
            ->getArrayResult();

        $card = $qbcard
            ->getQuery()
            ->getArrayResult();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $imfId = $request->request->get('imgId');
                if ($imfId != null) {
                    $entity->setProImgId($imfId);
                }

                $cardId = $request->request->get('cardId');

                if ($cardId != null) {
                    $entity->setCardId($cardId);
                }

                $entity->uploadImage($this->container);
                $em->persist($entity);
                $em->flush();
                $this->get('loggod')->writeBotLog('Create Social Content id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Social Content id=[' . $entity->getId() . ']');
                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Social content амжилттай нэмэгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_social'));
            }
        }

        return $this->render('botCmsBundle:Social:new.html.twig', array(
            'form' => $form->createView(),
            'pro' => $pro,
            'card' => $card
        ));
    }


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:SocialContent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай content олдсонгүй");
        }

        /**@var Product[] $proImg */
        $qb = $em->getRepository('mobicomCmsBundle:Product')->createQueryBuilder('p');

        $pro = $qb
            ->andwhere('p.type = 1')
            ->getQuery()
            ->getArrayResult();


        /**@var ScratchCard[] $card */
        $qbcard = $em->getRepository('mobicomCmsBundle:ScratchCard')->createQueryBuilder('p');
        $card = $qbcard
            ->getQuery()
            ->getArrayResult();

        $form = $this->createContentform($entity, true);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $imfId = $request->request->get('imgId');
                if ($imfId != null) {
                    $entity->setProImgId($imfId);
                }
                $entity->uploadImage($this->container);
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Social Content id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Social Content id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Social Content амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_social'));
            }
        }
        return $this->render('botCmsBundle:Social:update.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'pro' => $pro,
            'card' => $card
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:SocialContent')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай Social Content олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Social Content id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Social Content id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Social Content амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_social', array()));
    }


    public function orderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ids = $request->request->get('ids');
        foreach ($ids as $index => $alauid) {
            /**@var SocialContent[] $entity */
            $qb = $em->getRepository('botCmsBundle:SocialContent')->createQueryBuilder('p');
            $entity = $qb
                ->where('p.id = :id')
                ->setParameter('id', $alauid)
                ->getQuery()
                ->getSingleResult();
            if (!$entity) continue;
            $entity->setMyorder($index);
            $em->persist($entity);
        }
        $em->flush();
        return new JsonResponse(array(
            'status' => 'success',
        ));
    }

    private function createContentform(SocialContent $entity, $isUpdate)
    {
        $form = $this->createForm(new SocialContentType($isUpdate), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }
}
