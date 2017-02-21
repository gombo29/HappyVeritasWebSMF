<?php

namespace bot\WebBundle\Controller;

use bot\CmsBundle\Entity\BotBlock;
use bot\CmsBundle\Entity\BotButton;
use bot\CmsBundle\Entity\BotContent;
use bot\CmsBundle\Entity\BotSender;
use bot\CmsBundle\Entity\BotSenderNews;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CountContentController extends Controller
{
    public function indexAction(Request $request, $newsId)
    {
        $rdt = $request->get('rdt');
        $em = $this->getDoctrine()->getManager();
        $botContent = $em->getRepository('botCmsBundle:BotContent')->find($newsId);
        if ($botContent) {
            $botContent->setVCount($botContent->getVCount() + 1);
            $em->persist($botContent);
            $em->flush();
        }


        return $this->redirect($rdt);
    }
}
