<?php

namespace bot\WebBundle\Controller;

use bot\CmsBundle\Entity\SocialContent;
use mobicom\CmsBundle\Entity\ProductImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{

    public function indexAction(Request $request, $tabid)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('botCmsBundle:SocialContent')->createQueryBuilder('n');


        /**@var SocialContent[] $socialContent */
        $qb
            ->where('n.tab = :tabid')
            ->setParameter('tabid', $tabid);

        $socialContent = $qb
            ->andWhere('n.publishDate < :now')
            ->andWhere('n.endDate > :now')
            ->setParameter('now', new \DateTime('now'))
            ->orderBy('n.myorder', 'asc')
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


        return $this->render('botWebBundle:Page:index.html.twig', array(
            'socialContent' => $socialContent,
            'tabid' => $tabid,
        ));
    }
}

//============= Урамшуулал ==============
//https://www.facebook.com/dialog/pagetab?app_id=359423554107227&redirect_uri=https://www.mobicom.mn/bot/page/1

//============= Урьдчисан төлбөрт ==============
//https://www.facebook.com/dialog/pagetab?app_id=1522909221362134&redirect_uri=https://www.mobicom.mn/bot/page/2

//============= Дараа төлбөрт ==============
//https://www.facebook.com/dialog/pagetab?app_id=812365878890133&redirect_uri=https://www.mobicom.mn/bot/page/3

//============= Гар утас ==============
//https://www.facebook.com/dialog/pagetab?app_id=492356124288012&redirect_uri=https://www.mobicom.mn/bot/page/4

//============= Интернэт дата ==============
//https://www.facebook.com/dialog/pagetab?app_id=254197418349232&redirect_uri=https://www.mobicom.mn/bot/page/5

//============= Цэнэглэгч карт ==============
//https://www.facebook.com/dialog/pagetab?app_id=741298866024114&redirect_uri=https://www.mobicom.mn/bot/page/6

