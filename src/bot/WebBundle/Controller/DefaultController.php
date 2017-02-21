<?php

namespace bot\WebBundle\Controller;

use bot\CmsBundle\Entity\BotAutoContent;
use bot\CmsBundle\Entity\BotBlock;
use bot\CmsBundle\Entity\BotButton;
use bot\CmsBundle\Entity\BotConfig;
use bot\CmsBundle\Entity\BotContent;
use bot\CmsBundle\Entity\BotSender;
use bot\CmsBundle\Entity\BotSenderNews;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
//    https://www.facebook.com/dialog/pagetab?
//    app_id=360354607654386
//    &redirect_uri=https://0739cc46.ngrok.io/bot/page/6
    //https://www.messenger.com/t/1413141288920562/

    public function indexAction(Request $request)
    {

        $sender = null;
        $senderId = null;
        $challenge = null;
        $hub_verify_token = null;
//        $lang = 5;
        $lang = 1;
        $em = $this->getDoctrine()->getManager();
        $access_token = $this->get('service_container')->getParameter('fb_mobicom_bot_access_token');
        $verify_token = $this->get('service_container')->getParameter('fb_mobicom_bot_verify_token');
        $APPKEY = $this->get('service_container')->getParameter('fb_mobicom_bot_app_key');
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $access_token;
        $jsonParse = new JsonParse();

        $inputRaw = file_get_contents('php://input');
        if ($request->headers->get('X-Hub-Signature')) {
            if ($request->headers->get('X-Hub-Signature') != 'sha1=' . hash_hmac('sha1', $inputRaw, $APPKEY)) {
                throw $this->createNotFoundException();
            }
        } else {
            throw $this->createNotFoundException();
        }

        if ($request->get('hub_challenge')) {
            $challenge = $request->get('hub_challenge');
            $hub_verify_token = $request->get('hub_verify_token');
        }

        if ($hub_verify_token === $verify_token) {
            echo $challenge; // заавал challenge буцаана
        }

        $input = json_decode($inputRaw, true);

        // Хэрэглэгч бүртгэх хэсэг
        if (!empty($input['entry'][0]['messaging'][0]['sender']['id'])) {
            $sender = $input['entry'][0]['messaging'][0]['sender']['id'];

            if ($sender && $sender != null) {
                /**@var BotSender[] $findSender */
                $findSender = $em->getRepository('botCmsBundle:BotSender')->findBy(array('senderId' => $sender));
                if ($findSender == null) {
                    $userInfo = $this->getUserInfo($sender, $access_token);

//                    if ($userInfo->code != 100) {
                    $sendr = new BotSender();
                    $sendr->setSenderId($sender);
                    if (isset($userInfo->first_name)) {
                        $sendr->setFirstName($userInfo->first_name);
                    }
                    if (isset($userInfo->last_name)) {
                        $sendr->setLastName($userInfo->last_name);
                    }
                    if (isset($userInfo->profile_pic)) {
                        $sendr->setProfileImg($userInfo->profile_pic);
                    }
                    if (isset($userInfo->gender)) {
                        $sendr->setGender($userInfo->gender);
                    }
                    $sendr->setLang(1);
                    $sendr->setLastLogin(new \DateTime('now'));
                    $sendr->setIsFirst(1);
//                    }else
//                    {
//                        throw $this->createNotFoundException();
//                    }
                } else {
//                    $lang = $findSender[0]->getLang();
                    $sendr = $em->getRepository('botCmsBundle:BotSender')->find($findSender[0]->getId());
                    /**@var BotConfig[] $botConfig */
                    $botConfig = $em->getRepository('botCmsBundle:BotConfig')->find(1);
                    $endDate = date("Y-m-d H:i");
                    $sendr->getLastLogin()->add(new \DateInterval('PT' . $botConfig->getEndMsgDate() . 'M'));

                    if ($sendr->getLastLogin()->format("Y-m-d H:i") != $endDate) {
                        $sendr->setLastLogin(new \DateTime('now'));
                    }
                }
                $em->persist($sendr);
                $senderId = $sendr->getId();
                $em->flush();
            }
        }

        // Default мессеж илгээх хэсэг
        if (!empty($input['entry'][0]['messaging'][0]['postback']['payload'])) {
            if ('USER_DEF_1' == $input['entry'][0]['messaging'][0]['postback']['payload']) {
//                if ($lang == null) {
//                    $json = $jsonParse->chooseLanguage();
//                    $this->CurlFunction($json, $url, $sender);
//                } elseif ($lang == 5) {
//                    $json = $jsonParse->chooseLanguage();
//                    $this->CurlFunction($json, $url, $sender);
//                } else {
                $this->welCome($url, $sender, $lang);
//                }
//            } elseif (preg_match('[CHANGE_LANGUAGE]', $input['entry'][0]['messaging'][0]['postback']['payload'])) {
//                $json = $jsonParse->chooseLanguage();
//                $this->CurlFunction($json, $url, $sender);
            } else {
                $cont = false;
                $contid = null;
                $medee = false;
                $medeeArr = array();
                foreach ($this->content(1, true) as $a) {
                    if ('USER_NEWS_' . $a['botBlock']['id'] == $input['entry'][0]['messaging'][0]['postback']['payload']) {
                        $cont = true;
                        $contid = $a['botBlock']['id'];
                    }
                }
                if ($cont == true) {

                    if ($contid > 5) {
                        $botBlk = $em->getRepository('botCmsBundle:BotBlock')->find($contid);
                        $botBlk->setVCount($botBlk->getVCount() + 1);
                        $em->persist($botBlk);
                        $blkSender = new BotSenderNews();
                        $blkSender->setBotSender($em->getReference('botCmsBundle:BotSender', $senderId));
                        $blkSender->setBotBlock($em->getReference('botCmsBundle:BotBlock', $contid));
                        $em->persist($blkSender);
                        $em->flush();
                    }

                    $arr = $this->content($contid);
                    foreach ($arr as $a) {
                        if ($a['type'] == 1) {
                            array_push($medeeArr, $a);
                            $medee = true;
                        } elseif ($a['type'] == 2) {
                            $findSender = $em->getRepository('botCmsBundle:BotSender')->findBy(array('senderId' => $sender));
                            $jsonT = $jsonParse->mainText($a, $lang, 2, $findSender[0]->getFirstName() . ' ' . $findSender[0]->getLastName());
                            $this->CurlFunction($jsonT, $url, $sender);
                        } elseif ($a['type'] == 3) {
                            $jsonP = $jsonParse->mainText($a, $lang, 3);
                            $this->CurlFunction($jsonP, $url, $sender);
                        }
                    }
                    if ($medee == true) {
                        $json = $jsonParse->mainText($medeeArr, $lang, 1);
                        $this->CurlFunction($json, $url, $sender);
                    }
                } else {
                    $this->defaultCome($url, $sender, $lang);
                }
            }
        }

        // Текст мессеж шалгах хэсэг
        if (!empty($input['entry'][0]['messaging'][0]['message'])) {

            if ($input['entry'][0]['messaging'][0]['message']['text'] != null) {

//                if (preg_match('[монгол хэл]', mb_strtolower($input['entry'][0]['messaging'][0]['message']['text']))) {
//                    $findSender = $em->getRepository('botCmsBundle:BotSender')->findBy(array('senderId' => $sender));
//                    if ($findSender && $findSender != null) {
//                        $this->updateLanguage(1, $findSender[0]->getId());
//                        $json = array('text' => 'Баярлалаа, Монгол хэл дээр тохирууллаа.');
//                        $this->CurlFunction($json, $url, $sender);
//
//                        if ($findSender[0]->getIsFirst() == 1) {
//                            $this->updateISfirst($findSender[0]->getId());
//                            $this->welCome($url, $sender, 1);
//                        } else {
//                            $this->langafterCome($url, $sender, 1);
//                        }
//
//                    } else {
//                        $json = $jsonParse->chooseLanguage();
//                        $this->CurlFunction($json, $url, $sender);
//                    }
//                }
//                else
//                    if (preg_match('[english]', strtolower($input['entry'][0]['messaging'][0]['message']['text']))) {
//                    $findSender = $em->getRepository('botCmsBundle:BotSender')->findBy(array('senderId' => $sender));
//                    if ($findSender && $findSender != null) {
//                        $this->updateLanguage(0, $findSender[0]->getId());
//                        $json = array('text' => 'Thank you, change your language to English');
//                        $this->CurlFunction($json, $url, $sender);
//                        if ($findSender[0]->getIsFirst() == 1) {
//                            $this->updateISfirst($findSender[0]->getId());
//                            $this->welCome($url, $sender, 0);
//                        } else {
//                            $this->langafterCome($url, $sender, 0);
//                        }
//
//
//                    } else {
//                        $json = $jsonParse->chooseLanguage();
//                        $this->CurlFunction($json, $url, $sender);
//                    }
//                } else {
                $json = null;
                foreach ($this->contentText() as $c) {
                    if ($lang != 0) {
                        if (preg_match('[' . $c['title'] . ']', mb_strtolower($input['entry'][0]['messaging'][0]['message']['text']))) {
                            $json = $jsonParse->mainText($c, $lang, 4);
                        }
                    } else {
                        if (preg_match('[' . $c['titleEn'] . ']', strtolower($input['entry'][0]['messaging'][0]['message']['text']))) {
                            $json = $jsonParse->mainText($c, $lang, 4);
                        }
                    }
                }

                if ($json != null) {
                    $this->CurlFunction($json, $url, $sender);
                } else {
                    $this->defaultCome($url, $sender, $lang);
                }
//                }
            }
        }
        exit();
    }


    public function cronAction()
    {

        $access_token = $this->get('service_container')->getParameter('fb_mobicom_bot_access_token');

        $em = $this->getDoctrine()->getManager();
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $access_token;
        /**@var BotConfig[] $botConfig */
        $botConfig = $em->getRepository('botCmsBundle:BotConfig')->find(1);
        /**@var BotAutoContent[] $autoContent */
        $qb = $em->getRepository('botCmsBundle:BotAutoContent')->createQueryBuilder('ac');
        $now = new \DateTime();
        $startDate = clone $now;
        $startDate->setTime($startDate->format('H'), $startDate->format('i'), 0);
        $endDate = clone $now;
        $endDate->add(new \DateInterval('P0DT0H1M0S'));
        $endDate->setTime($endDate->format('H'), $endDate->format('i'), 0);
        $autoContent = $qb
            ->leftJoin('ac.botContent', 'c')
            ->addSelect('c')
            ->where('ac.sendDate >= :date1')
            ->andWhere('ac.sendDate <= :date2')
            ->setParameter('date1', $startDate)
            ->setParameter('date2', $endDate)
            ->getQuery()
            ->getArrayResult();


        if ($autoContent) {

            /**@var BotSender[] $botSender */
            $qbHeader = $em->getRepository('botCmsBundle:BotAutoHeader')->createQueryBuilder('n');
            $botAutoHeader = $qbHeader
                ->where('n.startDate <= :now')
                ->andWhere('n.endDate >= :now')
                ->setParameter('now', new \DateTime('now'))
                ->getQuery()
                ->getArrayResult();

            /**@var BotSender[] $botSender */
            $qbBot = $em->getRepository('botCmsBundle:BotSender')->createQueryBuilder('n');
            $botSender = $qbBot
                ->where('n.lastLogin >= :now')
                ->setParameter('now', date('Y-m-d', strtotime('-' . $botConfig->getDiffTotal() . ' day')))
                ->getQuery()
                ->getArrayResult();

            $contentIds = array_map(function ($n) {
                return $n['botContent']['id'];
            }, $autoContent);

            /**@var BotButton[] $botButton */
            $qb = $em->getRepository('botCmsBundle:BotButton')->createQueryBuilder('b');
            $botButton = $qb
                ->select('identity(b.botContent) as contentId', 'b.name', 'b.nameEn', 'b.id', 'bb.name as blockName', 'bb.id as blockId')
                ->leftJoin('b.botBlock', 'bb')
                ->andWhere($qb->expr()->in('b.botContent', ':ids'))
                ->setParameter('ids', $contentIds)
                ->getQuery()
                ->getArrayResult();

            foreach ($autoContent as $key => $entity) {
                $autoContent[$key]['botContent']['buttons'] = array();
                foreach ($botButton as $a) {
                    if ($a['contentId'] == $entity['botContent']['id']) {
                        $autoContent[$key]['botContent']['buttons'][] = $a;
                    }
                }
            }

            foreach ($botSender as $b) {
                if ($botAutoHeader) {
                    if ($botAutoHeader[0]) {
                        $this->CurlFunction(array('text' => $botAutoHeader[0]['content']), $url, $b['senderId']);
                    }
                }

                $this->CurlFunction($this->contselectAuto($autoContent, $b['lang']), $url, $b['senderId']);
            }
        }

//        if ($botConfig->getCronDate() == date('H:i')) { // Авто мэдээ илгээж байна
//            /**@var BotSender[] $botSender */
//            $qbBot = $em->getRepository('botCmsBundle:BotSender')->createQueryBuilder('n');
//            $botSender = $qbBot
//                ->getQuery()
//                ->getArrayResult();
//
//            $d = '-' . $botConfig->getDiffTotal() . ' day';
//
//            $now = new \DateTime('now');
//            $time = $now->modify($d)->format('Y-m-d H:i:s');
//
//            /**@var BotSenderNews[] $botSdrNews */
//            $qb = $em->getRepository('botCmsBundle:BotSenderNews')->createQueryBuilder('n');
//            $botSdrNews = $qb
//                ->select('s.id as senderId, b.id as blockId, count(n.id) as cnt')
//                ->leftJoin('n.botBlock', 'b')
//                ->leftJoin('n.botSender', 's')
//                ->andWhere('n.createdDate > :now')
//                ->setParameter('now', $time)
//                ->groupBy('b.id')
//                ->addGroupBy('s.id')
//                ->orderBy('cnt', 'desc')
//                ->getQuery()
//                ->getArrayResult();
//
//            /**@var BotContent[] $botContent */
//            $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('c');
//            $botContent = $qb
//                ->leftJoin('c.botBlock', 'b')
//                ->addSelect('b')
////                ->where('c.type = 1')
//                ->andWhere('c.publishDate < :now')
//                ->andWhere('c.endDate > :now')
//                ->setParameter('now', new \DateTime('now'))
//                ->getQuery()
//                ->getArrayResult();
//
//            $contentIds = array_map(function ($n) {
//                return $n['id'];
//            }, $botContent);
//
//            /**@var BotButton[] $botButton */
//            $qb = $em->getRepository('botCmsBundle:BotButton')->createQueryBuilder('b');
//            $botButton = $qb
//                ->select('identity(b.botContent) as contentId', 'b.name', 'b.nameEn', 'b.id', 'bb.name as blockName', 'bb.id as blockId')
//                ->leftJoin('b.botBlock', 'bb')
//                ->andWhere($qb->expr()->in('b.botContent', ':ids'))
//                ->setParameter('ids', $contentIds)
//                ->getQuery()
//                ->getArrayResult();
//
//
//            foreach ($botContent as $key => $entity) {
//                $botContent[$key]['buttons'] = array();
//                foreach ($botButton as $a) {
//                    if ($a['contentId'] == $entity['id']) {
//                        $botContent[$key]['buttons'][] = $a;
//                    }
//                }
//            }
//
//
//            foreach ($botSdrNews as $key => $entity) {
//                $botSdrNews[$key]['content'] = array();
//                foreach ($botContent as $a) {
//                    if ($a['botBlock']['id'] == $entity['blockId']) {
//                        $botSdrNews[$key]['content'][] = $a;
//                    }
//                }
//            }
//
//            foreach ($botSender as $key => $entity) {
//                $botSender[$key]['buttons'] = array();
//                foreach ($botSdrNews as $a) {
//                    if ($a['senderId'] == $entity['id']) {
//                        $botSender[$key]['buttons'] = $a;
//                    }
//                }
//            }
//
//            foreach ($botSender as $b) {
//                if (array_key_exists('content', $b['buttons'])) {
//                    if ($b['buttons']['content'] != null) {
//                        $this->CurlFunction($this->contselect($b['buttons']['content'], $b['lang']), $url, $b['senderId']);
//                    } else {
//                        $this->welCome($url, $b['senderId'], $b['lang']);
//                    }
//                } else {
//                    $this->welCome($url, $b['senderId'], $b['lang']);
//                }
//            }
//}

        else { // Төгсгөлийн мэдээ илгээж байна
            /**@var BotSender[] $botSender */
            $botSender = $em->getRepository('botCmsBundle:BotSender')->findAll();
            $contentData = $this->contselect($this->content(4, false, 0));
            $endDate = date("Y-m-d H:i");
            foreach ($botSender as $b) {
                $b->getLastLogin()->add(new \DateInterval('PT' . $botConfig->getEndMsgDate() . 'M'));
                if ($b->getLastLogin()->format("Y-m-d H:i") == $endDate) {
                    $this->CurlFunction($contentData, $url, $b->getSenderId());
                }
            }
        }
        exit();
    }


    public function contselectAuto($arr, $lang = 1)
    {
        $jsonParse = new JsonParse();
        $medee = false;
        $medeeArr = array();
        $json = '';
        foreach ($arr as $a) {
            if ($a['botContent']['type'] == 1) {
                array_push($medeeArr, $a['botContent']);
                $medee = true;
            } elseif ($a['botContent']['type'] == 2) {
                $json = $jsonParse->mainText($a['botContent'], $lang, 2);
            } elseif ($a['botContent']['type'] == 3) {
                $json = $jsonParse->mainText($a['botContent'], $lang, 3);
            }
        }
        if ($medee == true) {
            $json = $jsonParse->mainText($medeeArr, $lang, 1);
        }
        return $json;
    }

    public function contselect($arr, $lang = 1)
    {
        $jsonParse = new JsonParse();
        $medee = false;
        $medeeArr = array();
        $json = '';
        foreach ($arr as $a) {
            if ($a['type'] == 1) {
                array_push($medeeArr, $a);
                $medee = true;
            } elseif ($a['type'] == 2) {
                $json = $jsonParse->mainText($a, $lang, 2);
            } elseif ($a['type'] == 3) {
                $json = $jsonParse->mainText($a, $lang, 3);
            }
        }
        if ($medee == true) {
            $json = $jsonParse->mainText($medeeArr, $lang, 1);
        }
        return $json;
    }

    public function testAction()
    {
        return $this->render('botWebBundle:Default:index.html.twig');
    }


    public function welCome($url, $sender, $lang)
    {
        if ($lang == null) {
            $lang = 1;
        }
        $medee = false;
        $medeeArr = array();

        $jsonParse = new JsonParse();
        $arr = $this->content(1);

        foreach ($arr as $a) {
            if ($a['type'] == 1) {
                array_push($medeeArr, $a);
                $medee = true;
            } elseif ($a['type'] == 2) {
                $em = $this->getDoctrine()->getManager();
                $findSender = $em->getRepository('botCmsBundle:BotSender')->findBy(array('senderId' => $sender));
//                var_dump($findSender);
//                exit();

                $jsonT = $jsonParse->mainText($a, $lang, 2, $findSender[0]->getFirstName() . ' ' . $findSender[0]->getLastName());
                $this->CurlFunction($jsonT, $url, $sender);
            } elseif ($a['type'] == 3) {
                $jsonP = $jsonParse->mainText($a, $lang, 3);
                $this->CurlFunction($jsonP, $url, $sender);
            }
        }

        if ($medee == true) {
            $json = $jsonParse->mainText($medeeArr, $lang, 1);
            $this->CurlFunction($json, $url, $sender);
        }
    }

    public function defaultCome($url, $sender, $lang)
    {
        $medee = false;
        $medeeArr = array();

        $jsonParse = new JsonParse();
        $arr = $this->content(2);
        foreach ($arr as $a) {
            if ($a['type'] == 1) {
                array_push($medeeArr, $a);
                $medee = true;
            } elseif ($a['type'] == 2) {
                $jsonT = $jsonParse->mainText($a, $lang, 2);
                $this->CurlFunction($jsonT, $url, $sender);
            } elseif ($a['type'] == 3) {
                $jsonP = $jsonParse->mainText($a, $lang, 3);
                $this->CurlFunction($jsonP, $url, $sender);
            }
        }

        if ($medee == true) {
            $json = $jsonParse->mainText($medeeArr, $lang, 1);
            $this->CurlFunction($json, $url, $sender);
        }
    }

    public function langafterCome($url, $sender, $lang)
    {
        $medee = false;
        $medeeArr = array();

        $jsonParse = new JsonParse();
        $arr = $this->content(5);
        foreach ($arr as $a) {
            if ($a['type'] == 1) {
                array_push($medeeArr, $a);
                $medee = true;
            } elseif ($a['type'] == 2) {
                $jsonT = $jsonParse->mainText($a, $lang, 2);
                $this->CurlFunction($jsonT, $url, $sender);
            } elseif ($a['type'] == 3) {
                $jsonP = $jsonParse->mainText($a, $lang, 3);
                $this->CurlFunction($jsonP, $url, $sender);
            }
        }

        if ($medee == true) {
            $json = $jsonParse->mainText($medeeArr, $lang, 1);
            $this->CurlFunction($json, $url, $sender);
        }
    }


    public function CurlFunction($json, $url, $sender)
    {
        $jsonData = json_encode(
            array(
                'recipient' => array(
                    'id' => $sender
                ),
                'message' => $json
            )
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }


    public function getUserInfo($userid, $token)
    {
        $ch = curl_init('https://graph.facebook.com/v2.6/' . $userid . '?fields=first_name,last_name,profile_pic,gender&access_token=' . $token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function updateLanguage($lang, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $findSenderUpdating = $em->getRepository('botCmsBundle:BotSender')->find($id);
        $findSenderUpdating->setLang($lang);
        $em->persist($findSenderUpdating);
        $em->flush();
    }

    public function updateISfirst($id)
    {
        $em = $this->getDoctrine()->getManager();
        $findSenderUpdating = $em->getRepository('botCmsBundle:BotSender')->find($id);
        $findSenderUpdating->setIsFirst(0);
        $em->persist($findSenderUpdating);
        $em->flush();
    }

    public function contentText()
    {
        $em = $this->getDoctrine()->getManager();
        /**@var BotBlock[] $botBlock */
        $qb = $em->getRepository('botCmsBundle:BotBlock')->createQueryBuilder('b');
        $botBlock = $qb
            ->where('b.botGroup = :gid')
            ->setParameter('gid', 2)
            ->getQuery()
            ->getArrayResult();

        $blockIds = array_map(function ($n) {
            return $n['id'];
        }, $botBlock);


        /**@var BotContent[] $botContent */
        $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('b');
        $botContent = $qb
            ->andWhere($qb->expr()->in('b.botBlock', ':ids'))
            ->setParameter('ids', $blockIds)
            ->getQuery()
            ->getArrayResult();

        $contentIds = array_map(function ($n) {
            return $n['id'];
        }, $botContent);

        /**@var BotButton[] $botButton */
        $qb = $em->getRepository('botCmsBundle:BotButton')->createQueryBuilder('b');
        $botButton = $qb
            ->select('identity(b.botContent) as contentId', 'b.name', 'b.nameEn', 'b.id', 'bb.name as blockName', 'bb.id as blockId')
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

        return $botContent;
    }

    public function content($id, $all = false, $cron = 0)
    {
        $em = $this->getDoctrine()->getManager();
        if ($all == true) {
            /**@var BotContent[] $botContent */
            $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('c');
            $botContent = $qb
                ->leftJoin('c.botBlock', 'b')
                ->addSelect('b')
                ->andWhere('c.publishDate < :now')
                ->andWhere('c.endDate > :now')
                ->setParameter('now', new \DateTime('now'))
                ->getQuery()
                ->getArrayResult();
        } else {

            /**@var BotContent[] $botContent */
            $qb = $em->getRepository('botCmsBundle:BotContent')->createQueryBuilder('c');
            $qb
                ->leftJoin('c.botBlock', 'b')
                ->addSelect('b')
                ->where('b.id = :bid')
                ->setParameter('bid', $id)
                ->orderBy('c.type', 'asc')
                ->andWhere('c.publishDate < :now')
                ->andWhere('c.endDate > :now')
                ->setParameter('now', new \DateTime('now'));

            if ($cron != 0) {
                $qb
                    ->setMaxResults($cron);
            }

            $botContent = $qb
                ->getQuery()
                ->getArrayResult();
        }

        $contentIds = array_map(function ($n) {
            return $n['id'];
        }, $botContent);

        /**@var BotButton[] $botButton */
        $qb = $em->getRepository('botCmsBundle:BotButton')->createQueryBuilder('b');
        $botButton = $qb
            ->select('identity(b.botContent) as contentId', 'b.name', 'b.nameEn', 'b.id', 'bb.name as blockName', 'bb.id as blockId')
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

        return $botContent;
    }
}

// ####### Start text #######
//curl -X GET "https://graph.facebook.com/v2.6/1326665127403664?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=EAAILNPEu81MBAOk7aJ476TTekxnMBDonR7ZCmZBGENpfdCeAiRNHtgOamOi7kDu6AKKMH7GsyLwUeoyYlDZAALgGdyoAo3ZCtfvvZCbtyOcJwhaWjQ7Ij83hgur0wJscGk5FEPHxXgniHdttbhVZAVuTZBOfSifClGQmU7YdToQTgZDZD"

// ####### Greeting text #######
//curl -X POST -H "Content-Type: application/json" -d '{
//  "setting_type":"greeting",
//  "greeting":{
//    "text":"Тавтай морилно уу {{user_first_name}}! Та эндээс өөрт хэрэгтэй мэдээлэлээ авна уу! Биднийг сонгосон таньд баярлалаа!"
//  }
//}' "https://graph.facebook.com/v2.6/me/thread_settings?access_token=EAAILNPEu81MBAOk7aJ476TTekxnMBDonR7ZCmZBGENpfdCeAiRNHtgOamOi7kDu6AKKMH7GsyLwUeoyYlDZAALgGdyoAo3ZCtfvvZCbtyOcJwhaWjQ7Ij83hgur0wJscGk5FEPHxXgniHdttbhVZAVuTZBOfSifClGQmU7YdToQTgZDZD"

// ######## Menu #########

//curl -X POST -H "Content-Type: application/json" -d '{
//  "setting_type" : "call_to_actions",
//  "thread_state" : "existing_thread",
//  "call_to_actions":[
//    {
//      "type":"web_url",
//      "title":"www.mobicom.mn",
//      "url":"https://www.mobicom.mn"
//    }
//  ]
//}' "https://graph.facebook.com/v2.6/me/thread_settings?access_token=EAAILNPEu81MBAOk7aJ476TTekxnMBDonR7ZCmZBGENpfdCeAiRNHtgOamOi7kDu6AKKMH7GsyLwUeoyYlDZAALgGdyoAo3ZCtfvvZCbtyOcJwhaWjQ7Ij83hgur0wJscGk5FEPHxXgniHdttbhVZAVuTZBOfSifClGQmU7YdToQTgZDZD"


// ####### Welcome message ########
//
//curl -X POST -H "Content-Type: application/json" -d '{
//  "setting_type" : "call_to_actions",
//  "thread_state" : "new_thread",
//  "call_to_actions":[
//    {
//         "payload":"USER_DEF_1",
//    }
//  ]
//}' "https://graph.facebook.com/v2.6/me/thread_settings?access_token=EAAILNPEu81MBAOk7aJ476TTekxnMBDonR7ZCmZBGENpfdCeAiRNHtgOamOi7kDu6AKKMH7GsyLwUeoyYlDZAALgGdyoAo3ZCtfvvZCbtyOcJwhaWjQ7Ij83hgur0wJscGk5FEPHxXgniHdttbhVZAVuTZBOfSifClGQmU7YdToQTgZDZD"

