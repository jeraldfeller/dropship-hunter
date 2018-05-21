<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/14/2018
 * Time: 4:03 AM
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Doctrine\ORM\Tools\Pagination\Paginator;
use AppBundle\Controller\SessionController;


use AppBundle\Entity\ProductList;
use AppBundle\Entity\ProductListLinks;
use AppBundle\Entity\ProxyList;
use AppBundle\Entity\ProcessStatus;
use AppBundle\Entity\SellerData;

class ScrapeSellerDataCommand extends ContainerAwareCommand
{
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

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-seller-data')
            // the short description shown while running "php bin/console list"
            ->setDescription('search for seller in ebay')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')//   ->addArgument('action', InputArgument::REQUIRED, 'What type of action do you want to execute?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        $scrapeStatus = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'seller_data'));
        if($scrapeStatus->getIsActive() == 1){
            if ($processEntity->getIsActive() == 1) {
                $proxy = $this->getProxy($em);
                $sellers = $this->getSellerData($em);
                $ebayUrlTemplate = 'https://www.ebay.com/usr/';
                if ($sellers) {
                    for ($x = 0; $x < count($sellers); $x++) {
                        $productListId = $sellers[$x]['productListId'];
                        $productListLinksId = $sellers[$x]['productListLinksId'];
                        $sellerId = strtolower(str_replace(' ', '', trim($sellers[$x]['sellerId'])));
                        $id = $sellers[$x]['id'];
                        $url = $ebayUrlTemplate . $sellerId;
                        $htmlData = $this->curlTo($url, $proxy);

                        if ($htmlData['html']) {
                            if(is_bool($htmlData['html']) === false){
                                $html = str_get_html($htmlData['html']);
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
                                    if ($score) {
                                        for ($s = 0; $s < count($score); $s++) {
                                            $numScore = $score[$s]->find('.num', 0);
                                            $txtScore = $score[$s]->find('.txt', 0);
                                            if ($numScore) {
                                                $numScore = trim($numScore->plaintext);
                                                $txtScore = trim(strtolower($txtScore->plaintext));
                                                switch ($txtScore) {
                                                    case 'positive':
                                                        $positive = intval(preg_replace('/[^\d.]/', '', $numScore));
                                                        break;
                                                    case 'neutral':
                                                        $neutral = intval(preg_replace('/[^\d.]/', '', $numScore));
                                                        break;
                                                    case 'negative':
                                                        $negative = intval(preg_replace('/[^\d.]/', '', $numScore));
                                                        break;
                                                }
                                            } else {
                                                $positive = 0;
                                                $neutral = 0;
                                                $negative = 0;
                                            }
                                        }
                                    } else {
                                        $positive = 0;
                                        $neutral = 0;
                                        $negative = 0;
                                    }

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

                                    $entity = $em->getRepository('AppBundle:SellerData')->find($id);
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
                                        $productLinkEntity = $em->getRepository('AppBundle:ProductListLinks')->find($productListLinksId);
                                        $productLinkEntity->setStatus('complete');

                                        $em->flush();
                                    }
                                }else{
                                    $entity = $em->getRepository('AppBundle:SellerData')->find($id);
                                    if ($entity) {
                                        $entity->setStatus('complete');
                                        $productLinkEntity = $em->getRepository('AppBundle:ProductListLinks')->find($productListLinksId);
                                        $productLinkEntity->setStatus('complete');

                                        $em->flush();
                                    }
                                }
                            }else{
                                $entity = $em->getRepository('AppBundle:SellerData')->find($id);
                                if ($entity) {
                                    $entity->setStatus('complete');
                                    $productLinkEntity = $em->getRepository('AppBundle:ProductListLinks')->find($productListLinksId);
                                    $productLinkEntity->setStatus('complete');

                                    $em->flush();
                                }
                            }
                        }else{
                            $entity = $em->getRepository('AppBundle:SellerData')->find($id);
                            if ($entity) {
                                $entity->setStatus('complete');
                                $productLinkEntity = $em->getRepository('AppBundle:ProductListLinks')->find($productListLinksId);
                                $productLinkEntity->setStatus('complete');

                                $em->flush();
                            }
                        }
                    }
                }
            }
        }

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

    public function getSellerData($em)
    {
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:SellerData p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(50);
        $result = $sql->getResult();

        $lists = array();
        if ($result) {
            for ($x = 0; $x < count($result); $x++) {
                // update status of product list

                $entity = $em->getRepository('AppBundle:SellerData')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'id' => $result[$x]->getId(),
                    'productListLinksId' => $result[$x]->getProductListLinksId(),
                    'productListId' => $result[$x]->getProductListId(),
                    'sellerId' => $result[$x]->getSellerId()
                );
            }
        }

        return $lists;
    }

    public function curlTo($url, $proxy)
    {
        //   $proxy = null;
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
}
