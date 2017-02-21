<?php

namespace bot\CmsBundle\Controller;

use bot\CmsBundle\Entity\BotAutoContent;
use bot\CmsBundle\Entity\BotAutoHeader;
use bot\CmsBundle\Entity\BotBlock;
use bot\CmsBundle\Entity\BotButton;
use bot\CmsBundle\Entity\BotContent;
use bot\CmsBundle\Entity\BotGroup;
use bot\CmsBundle\Entity\BotSenderNews;
use bot\CmsBundle\Form\BotAutoContentType;
use bot\CmsBundle\Form\BotAutoHeaderType;
use bot\CmsBundle\Form\BotBlockType;
use bot\CmsBundle\Form\BotButtonType;
use bot\CmsBundle\Form\BotContentType;
use bot\CmsBundle\Form\BotGroupType;
use bot\CmsBundle\Form\BotsdrSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BotController extends Controller
{

// ==========  Bot Get List ========

    public function botAction($menu, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        /**@var BotGroup[] $botGroup */
        $botGroup = $em->getRepository('botCmsBundle:BotGroup')->findAll();

        /**@var BotBlock[] $botBlock */
        $qb = $em->getRepository('botCmsBundle:BotBlock')->createQueryBuilder('b');
        $botBlock = $qb
            ->leftJoin('b.botGroup', 'g')
            ->addSelect('g')
            ->orderBy('b.vCount', 'desc')
            ->where('b.createdDate is not null')
            ->getQuery()
            ->getArrayResult();

        /**@var BotContent[] $botContent */
        $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('c');
        $botContent = $qb
            ->leftJoin('c.botBlock', 'b')
            ->addSelect('b')
            ->leftJoin('b.botGroup', 'g')
            ->addSelect('g')
            ->where('b.id = :bid')
            ->setParameter('bid', $bid)
            ->orderBy('c.type', 'ASC')
            ->addOrderBy('c.endDate', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $contentIds = array_map(function ($n) {
            return $n['id'];
        }, $botContent);

        /**@var BotButton[] $botContent */
        $qb = $em->getRepository('botCmsBundle:BotButton')->createQueryBuilder('b');
        $botButton = $qb
            ->select('identity(b.botContent) as contentId', 'b.name', 'b.id', 'bb.name as blockName', 'bb.id as blockId')
            ->leftJoin('b.botBlock', 'bb')
            ->andWhere($qb->expr()->in('b.botContent', ':ids'))
            ->setParameter('ids', $contentIds)
            ->getQuery()
            ->getArrayResult();

        foreach ($botContent as $key => $entity) {
            $botContent[$key]['buttons'] = array();
            foreach ($botButton as $a) {
                if ($a['contentId'] == $entity['id']) {
                    $botContent[$key]['buttons'][] = $a;
                }
            }
        }

        if ($bid == 3) {
            /**@var BotAutoContent[] $botAutoContent */
            $qb = $em->getRepository('botCmsBundle:BotAutoContent')->createQueryBuilder('b');
            $botAutoContent = $qb
                ->leftJoin('b.botContent', 'c')
                ->addSelect('c')
                ->orderBy('b.id', 'desc')
                ->getQuery()
                ->getArrayResult();

            /**@var BotAutoHeader[] $botAutoHeader */
            $qbheader = $em->getRepository('botCmsBundle:BotAutoHeader')->createQueryBuilder('h');
            $botAutoHeader = $qbheader
                ->orderBy('h.id', 'desc')
                ->getQuery()
                ->getArrayResult();

        } else {
            $botAutoContent = null;
            $botAutoHeader = null;
        }

        return $this->render('botCmsBundle:Bot:bots.html.twig', array(
            'menu' => $menu,
            'botGroup' => $botGroup,
            'botBlock' => $botBlock,
            'botContent' => $botContent,
            'bid' => $bid,
            'botAutoContent' => $botAutoContent,
            'botAutoHeader' => $botAutoHeader
        ));
    }


    public function botlistAction(Request $request, $menu, $bid, $page)
    {
        $searchEntity = new BotSenderNews();
        $searchForm = $this->createForm(new BotsdrSearchType(), $searchEntity);
        $search = false;
        if ($request->get("submit") == 'submit') {
            $searchForm->bind($request);
            $search = true;
        }

        $em = $this->getDoctrine()->getManager();
        /**@var BotSenderNews[] $botSendNews */
        $qb = $em->getRepository('botCmsBundle:BotSenderNews')->createQueryBuilder('b');


        if ($search) {

            if ($searchEntity->getBotBlock() && $searchEntity->getBotBlock() != '') {
                $qb->andWhere('b.botBlock = :block')
                    ->setParameter('block', $searchEntity->getBotBlock());
            }

            if ($searchEntity->sdate && $searchEntity->edate) {
                $qb
                    ->andWhere($qb->expr()->between('b.createdDate', ':sdate', ':edate'))
                    ->setParameter('sdate', $searchEntity->sdate)
                    ->setParameter('edate', $searchEntity->edate);
            }
        }

        $botSendNews = $qb
            ->leftJoin('b.botBlock', 'g')
            ->groupBy('g.id')
            ->select('g.id, g.name, b.createdDate, count(g.id) as cnt')
            ->orderBy('cnt', 'desc')
            ->getQuery()
            ->getArrayResult();

        return $this->render('botCmsBundle:Bot:botlist.html.twig', array(
            'menu' => $menu,
            'botSendNews' => $botSendNews,
            'bid' => $bid,
            'search' => $search,
            'searchform' => $searchForm->createView(),
        ));
    }

// ==========  Bot Create ========

    public function botGroupNewAction(Request $request, $menu, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BotGroup();

        $form = $this->createbotGroupForm($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot Group id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Bot Group id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Бүлэг амжилттай нэмэгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }

        return $this->render('botCmsBundle:Bot:botGroupNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));

    }

    public function botBlockNewAction(Request $request, $gid, $menu, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BotBlock();
        $form = $this->createbotBlockform($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity->setBotGroup(($em->getReference('botCmsBundle:BotGroup', $gid)));
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot Category id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Bot Category id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Категори амжилттай нэмэгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $entity->getId())));
            }
        }

        return $this->render('botCmsBundle:Bot:botBlockNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }

    public function botContentNewAction(Request $request, $menu, $bid, $type)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new BotContent();
        $m = $request->get('m');
        if ($m == 1) {
            $m = true;
        } else {
            $m = false;
        }

        $form = $this->createbotContentform($entity, $type, false, $m, $bid);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($bid != 0) {
                    $entity->setBotBlock(($em->getReference('botCmsBundle:BotBlock', $bid)));
                }
                $entity->setType($type);
                $entity->uploadImage($this->container);

                if ($entity->getType() != 1) {
                    $publish = \DateTime::createFromFormat('Y-m-d', '2000-07-11');
                    $end = \DateTime::createFromFormat('Y-m-d', '2100-07-11');
                    $entity->setPublishDate($publish);
                    $entity->setEndDate($end);
                }

                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot Content id=[' . $entity->getId() . '] type=[' . $entity->getType() . ']');
                $this->get('loggod')->writeLog('Create Bot Content id=[' . $entity->getId() . '] type=[' . $entity->getType() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Агуулга амжилттай нэмэгдлээ!');
                if ($bid != 0) {
                    return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
                } else {
                    return $this->redirect($this->generateUrl('bot_cms_content', array('menu' => $menu, 'page' => 1)));
                }
            }
        }

        return $this->render('botCmsBundle:Bot:botContentNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid,
            'type' => $type
        ));
    }


    public function botAutoContentNewAction(Request $request, $menu, $bid)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new BotAutoContent();
        $form = $this->createbotAutoContentform($entity, false);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot Auto Content id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Bot Auto Content id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Автоматаар илгээгдэх мэдээлэл амжилттай нэмэгдлээ!');
                if ($bid != 0) {
                    return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
                } else {
                    return $this->redirect($this->generateUrl('bot_cms_content', array('menu' => $menu, 'page' => 1)));
                }
            }
        }

        return $this->render('botCmsBundle:Bot:botAutoContentNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid,
        ));
    }

    public function botButtonNewAction(Request $request, $menu, $cid, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BotButton();
        $form = $this->createbotButtonConnectform($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity->setBotContent(($em->getReference('botCmsBundle:BotContent', $cid)));
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot Button id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Bot Button id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Холбоос амжилттай нэмэгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }

        return $this->render('botCmsBundle:Bot:botButtonNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }

// ==========  Bot Update ========

    public function botGroupUpdateAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай бүлэг олдсонгүй");
        }

        $form = $this->createbotGroupForm($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Group id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Bot Group id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Бүлэг амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }
        return $this->render('botCmsBundle:Bot:botGroupUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }

    public function botBlockUpdateAction(Request $request, $menu, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotBlock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай блок олдсонгүй");
        }

        $form = $this->createbotBlockForm($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Category id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Bot Category id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Блок амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $id)));
            }
        }
        return $this->render('botCmsBundle:Bot:botBlockUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $id
        ));
    }

    public function botAutoContentUpdateAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotAutoContent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай мэдээлэл олдсонгүй");
        }

        $form = $this->createbotAutoContentform($entity, true);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Auto Content id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Bot Auto Content id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Автоматаар илгээгдэх мэдээлэл амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }
        return $this->render('botCmsBundle:Bot:botAutoContentUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }

    public function botContentUpdateAction(Request $request, $menu, $id, $bid)
    {
        $con = $request->get('con');
        $em = $this->getDoctrine()->getManager();
        /**@var BotContent[] $entity */
        $entity = $em->getRepository('botCmsBundle:BotContent')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай content олдсонгүй");
        }
        $m = $request->get('m');
        if ($m == 1) {
            $m = true;
        } else {
            $m = false;
        }

        $form = $this->createbotContentForm($entity, $entity->getType(), true, $m, $bid);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $con = $request->get('con');
                $entity->uploadImage($this->container);
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Content id=[' . $entity->getId() . '] type=[' . $entity->getType() . ']');
                $this->get('loggod')->writeLog('Update Bot Content id=[' . $entity->getId() . '] type=[' . $entity->getType() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Content амжилттай засагдлаа!');
                if ($con == 1) {
                    return $this->redirect($this->generateUrl('bot_cms_content', array('menu' => $menu, 'page' => 1)));
                } else {
                    return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
                }
            }
        }
        return $this->render('botCmsBundle:Bot:botContentUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'entity' => $entity,
            'bid' => $bid,
            'con' => $con,
            'type' => $entity->getType()
        ));
    }

    public function botButtonUpdateAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotButton')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай холбоос олдсонгүй");
        }

        $form = $this->createbotButtonConnectform($entity);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Button id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Bot Button id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Холбоос амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }
        return $this->render('botCmsBundle:Bot:botButtonUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $id
        ));
    }

// ==========  Bot Delete ========

    public function botGroupDeleteAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotGroup')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай бүлэг олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Group id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Group id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Бүлэг амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
    }

    public function botBlockDeleteAction(Request $request, $menu, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotBlock')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай блок олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Category id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Category id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Блок амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => 1)));
    }

    public function botAutoContentDeleteAction(Request $request, $menu, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotAutoContent')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай мэдээлэл олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Auto Content id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Auto Content id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Автоматаар илгээгдэх мэдээлэл амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => 3)));
    }


    public function botContentDeleteAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotContent')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай content олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Content id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Content id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Content амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
    }


    public function botButtonDeleteAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotButton')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай холбоос олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {

            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Button id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Button id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Холбоос амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
    }


    public function botAutoHeaderNewAction(Request $request, $bid, $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BotAutoHeader();
        $form = $this->createbotAutoHeaderform($entity, false);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Create Bot auto header id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Create Bot auto header id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Auto header амжилттай нэмэгдлээ!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }

        return $this->render('botCmsBundle:Bot:botAutoHeaderNew.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }

    public function botAutoHeaderUpdateAction(Request $request, $menu, $id, $bid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotAutoHeader')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("$id-тай мэдээлэл олдсонгүй");
        }

        $form = $this->createbotAutoHeaderform($entity, true);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $this->get('loggod')->writeBotLog('Update Bot Auto Header id=[' . $entity->getId() . ']');
                $this->get('loggod')->writeLog('Update Bot Auto Header id=[' . $entity->getId() . ']');

                $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Автоматаар илгээгдэх мэдээлэл Header амжилттай засагдлаа!');

                return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => $bid)));
            }
        }
        return $this->render('botCmsBundle:Bot:botAutoHeaderUpdate.html.twig', array(
            'form' => $form->createView(),
            'menu' => $menu,
            'bid' => $bid
        ));
    }


    public function botAutoHeaderDeleteAction(Request $request, $menu, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('botCmsBundle:BotAutoHeader')->find($id);
        $id = null;
        if (!$entity) {
            throw $this->createNotFoundException("$id-тай мэдээлэл олдсонгүй!");
        }

        if ($request->getMethod() === 'POST') {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->get('loggod')->writeBotLog('Delete Bot Auto Header id=[' . $id . ']');
        $this->get('loggod')->writeLog('Delete Bot Auto Header id=[' . $id . ']');

        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Автоматаар илгээгдэх мэдээлэл header амжилттай устгагдлаа!');
        return $this->redirect($this->generateUrl('bot_cms_bot', array('menu' => $menu, 'bid' => 3)));
    }


// ==========  Bot forms ========

    private function createbotGroupForm(BotGroup $entity)
    {
        $form = $this->createForm(new BotGroupType(), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }

    private function createbotBlockform(BotBlock $entity)
    {
        $form = $this->createForm(new BotBlockType(), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }

    private function createbotContentform(BotContent $entity, $type, $isUpdate, $m, $bid)
    {
        $form = $this->createForm(new BotContentType($type, $isUpdate, $m, $bid), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }

    private function createbotAutoContentform(BotAutoContent $entity, $isUpdate)
    {
        $form = $this->createForm(new BotAutoContentType($isUpdate), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }

    private function createbotButtonConnectform(BotButton $entity)
    {
        $form = $this->createForm(new BotButtonType(), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }

    private function createbotAutoHeaderform(BotAutoHeader $entity, $isUpdate)
    {
        $form = $this->createForm(new BotAutoHeaderType($isUpdate), $entity, array(
            'method' => 'POST'
        ));
        return $form;
    }
}
