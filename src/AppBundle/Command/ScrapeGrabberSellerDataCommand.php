<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 6/10/2018
 * Time: 10:25 AM
 */

namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Doctrine\ORM\Tools\Pagination\Paginator;
use AppBundle\Controller\SessionController;
use AppBundle\Entity\GSellerData;
use AppBundle\Entity\GProductList;
use AppBundle\Entity\GProductListLinks;
use AppBundle\Entity\GFSellerData;
use AppBundle\Entity\ScrapeStatuses;

class ScrapeGrabberSellerDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-grabber-seller-data')
            // the short description shown while running "php bin/console list"
            ->setDescription('search for product title in ebay')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')//   ->addArgument('action', InputArgument::REQUIRED, 'What type of action do you want to execute?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $appActivity = $em->getRepository('AppBundle:AppActivity')->findOneBy(array('app' => 'app_3'));

        if($appActivity->getActivity() == 'play') {
            $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
            $scrapeStatus = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'g_seller_data'));
            if ($scrapeStatus->getIsActive() == 1) {
                if ($processEntity->getIsActive() == 1) {
                    $proxy = $this->getProxy($em);
                    $sellers = $this->getSellerData($em);
                    $ebayUrlTemplate = 'https://www.ebay.com/usr/';

                    if ($sellers) {
                        for ($x = 0; $x < count($sellers); $x++) {
                            $productLinkEntity = $sellers[$x]['productListLinksEntity'];
                            $sellerId = str_replace(' ', '', trim($sellers[$x]['sellerId']));
                            $id = $sellers[$x]['id'];
                            $entity = $sellers[$x]['entity'];
                            $url = $ebayUrlTemplate . $sellerId;
                            $htmlData = $this->curlTo($url, $proxy);
                            $allFeedbackUrl = 'https://feedback.ebay.com/ws/eBayISAPI.dll?ViewFeedback2&userid='.$sellerId.'&ftab=AllFeedback';
                            if ($htmlData['html']) {
                                if(is_bool($htmlData['html']) === false){
                                    $html = str_get_html($htmlData['html']);
                                    $ifs = $html->find('.soi_lk', 0);
                                    if($ifs){
                                        $ifs = $ifs->find('a', 0);
                                        if($ifs){
                                            $ifs = str_replace('http', 'https', $ifs->getAttribute('href'));
                                        }else{
                                            $ifs = false;
                                        }
                                    }else{
                                        $ifs = false;
                                    }
                                    if(is_bool($html) === false){
                                        $location = $html->find('.mem_loc', 0);
                                        $userInfo = $html->find('#user_info', 0);
                                        $memberSince = $html->find('.mem_info', 0);
                                        $score = $html->find('.score');
                                        $sellCount = $html->find('.sell_count', 0);
                                        $sellerPage = $html->find('.store_lk', 0);
                                        if ($location) {
                                            $location = $location->plaintext;
                                        } else {
                                            $location = '';
                                        }
                                        if ($userInfo) {
                                            $sellerRank = $userInfo->find('a', 1);
                                            if ($sellerRank) {
                                                $sellerRank = preg_replace("/[^0-9,.]/", "", $sellerRank->plaintext);
                                            } else {
                                                $sellerRank = 0;
                                            }
                                        } else {
                                            $sellerRank = 0;
                                        }

                                        if ($memberSince) {
                                            $memberSinceCont = $memberSince->find('.info');
                                            for ($a = 0; $a < count($memberSinceCont); $a++) {
                                                if ($a == 0) {
                                                    $memberSince = $memberSinceCont[$a]->plaintext;
                                                }
                                            }

                                        } else {
                                            $memberSince = '';
                                        }

                                        //Scores

                                        if ($score) {
                                            $htmlScore = $this->curlTo($allFeedbackUrl, $proxy);
                                            if($htmlScore['html']){
                                                if(is_bool($htmlScore['html']) === false){
                                                    $htmlScr = str_get_html($htmlScore['html']);
                                                    $feedBackTable = $htmlScr->find('#recentFeedbackRatingsTable', 0);
                                                    $fTr = $feedBackTable->find('.fbsSmallYukon');
                                                    if($fTr){
                                                        $positive = trim($fTr[0]->find('td', 2)->plaintext);
                                                        $neutral = trim($fTr[1]->find('td', 2)->plaintext);
                                                        $negative = trim($fTr[2]->find('td', 2)->plaintext);
                                                    }else{
                                                        $positive = 0;
                                                        $neutral = 0;
                                                        $negative = 0;
                                                    }
                                                }else{
                                                    $positive = 0;
                                                    $neutral = 0;
                                                    $negative = 0;
                                                }
                                            }else{
                                                $positive = 0;
                                                $neutral = 0;
                                                $negative = 0;
                                            }
                                        } else {
                                            $positive = 0;
                                            $neutral = 0;
                                            $negative = 0;
                                        }

                                        // end score
                                        if ($sellCount) {
                                            $sellCount = $sellCount->find('a', 0);
                                            if ($sellCount) {
                                                $sellCount = trim($sellCount->plaintext);
                                            }
                                        } else {
                                            $sellCount = 0;
                                        }

                                        if ($sellerPage) {
                                            $sellerPage = $sellerPage->find('a', 0);
                                            if ($sellerPage) {
                                                $sellerPage = $sellerPage->getAttribute('href');
                                            } else {
                                                $sellerPage = '';
                                            }
                                        } else {
                                            $sellerPage = '';
                                        }


                                        // get Item Condition Listings
                                        if($ifs != false){
                                            $htmlListings = $this->curlTo($ifs, $proxy);
                                            $hasCondPnl = false;
                                            $used = 0;
                                            $new = 0;
                                            if($htmlListings['html']){
                                                if(is_bool($htmlListings['html']) === false) {
                                                    $htmlLst = str_get_html($htmlListings['html']);
                                                    $panels = $htmlLst->find('.pnl');
                                                    for($p = 0; $p < count($panels); $p++){
                                                        $pnlTitle = $panels[$p]->find('h3', 0);
                                                        if($pnlTitle){
                                                            if(trim($pnlTitle->plaintext) == 'Condition'){
                                                                $hasCondPnl = true;
                                                                $panelB = $panels[$p]->find('.pnl-b', 0);
                                                                $panelB_a = $panelB->find('a');
                                                                for($pb = 0; $pb < count($panelB_a); $pb++){
                                                                    $pnlB_t = trim($panelB_a[$pb]->find('span', 0)->plaintext);
                                                                    $pnlB_c = trim($panelB_a[$pb]->find('span', 1)->plaintext);
                                                                    if($pnlB_t == 'New'){
                                                                        $new = preg_replace("/[^0-9]/", '', $pnlB_c);
                                                                    }else if($pnlB_t == 'Used'){
                                                                        $used = preg_replace("/[^0-9]/", '', $pnlB_c);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    $used = 0;
                                                    $new = 0;
                                                }

                                            }else{
                                                $used = 0;
                                                $new = 0;
                                            }

                                            if($hasCondPnl == false){
                                                $used = 0;
                                                $new = 0;
                                            }
                                        }else{
                                            $used = 0;
                                            $new = 0;
                                        }
                                        if ($entity) {
                                            $entity->setSellerLocation($location);
                                            $entity->setSellersRank($sellerRank);
                                            $entity->setMemberSince($memberSince);
                                            $entity->setPositive($positive);
                                            $entity->setNeutral($neutral);
                                            $entity->setNegative($negative);
                                            $entity->setItemsForSale($sellCount);
                                            $entity->setSellerPage($sellerPage);
                                            $entity->setStatus('complete');
                                            $entity->setUsedCount($used);
                                            $entity->setNewCount($new);
                                            $productLinkEntity->setStatus('complete');
                                            $em->flush();
                                        }
                                    }else{
                                        if ($entity) {
                                            $entity->setStatus('complete');
                                            $productLinkEntity->setStatus('complete');

                                            $em->flush();
                                        }
                                    }
                                }else{
                                    if ($entity) {
                                        $entity->setStatus('complete');
                                        $productLinkEntity->setStatus('complete');

                                        $em->flush();
                                    }
                                }
                            }else{
                                if ($entity) {
                                    $entity->setStatus('complete');
                                    $productLinkEntity->setStatus('complete');
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function curlTo($url, $proxy)
    {
        //$proxy = null;
        $agents = array(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
            'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4',
            'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'

        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($proxy != NULL) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy[mt_rand(0, count($proxy) - 1)]);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($curl,CURLOPT_USERAGENT,$agents[array_rand($agents)]);
        $contents = curl_exec($curl);
        curl_close($curl);
        return array('html' => $contents);
    }

    public function delete_all_between($beginning, $end, $string)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return str_replace($textToDelete, '', $string);
    }

    public function getSellerData($em)
    {
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:GFSellerData p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(40);
        $result = $sql->getResult();

        $lists = array();
        if ($result) {
            for ($x = 0; $x < count($result); $x++) {
                // update status of product list

                $entity = $em->getRepository('AppBundle:GFSellerData')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'id' => $result[$x]->getId(),
                    'sellerId' => $result[$x]->getSellerId(),
                    'productListLinksEntity' => $result[$x]->getGProductListLinks(),
                    'entity' => $entity
                );
            }
        }

        return $lists;
    }

    public function getProxy($em)
    {
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:ProxyList p
                "
        );
        $result = $sql->getResult();

        $lists = array();
        if ($result) {
            for ($x = 0; $x < count($result); $x++) {
                $lists[] = $result[$x]->getProxy();
            }
        }

        return $lists;
    }
}