<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 6/10/2018
 * Time: 9:31 AM
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

class ScrapeGrabberBySellerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-grabber-links')
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
            $scrapeStatus = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'g_by_seller'));
            if ($scrapeStatus->getIsActive() == 1) {
                if ($processEntity->getIsActive() == 1) {
                    $proxy = $this->getProxy($em);
                    $products = $this->getProductListLink($em);
                    $ebayUrlTemplate = 'https://www.ebay.com/usr/';
                    if($products){
                        for($x = 0; $x < count($products); $x++) {
                            $productUrl = trim($products[$x]['productUrl']);
//                    $output->writeln([$productUrl]);
                            $id = $products[$x]['id'];
                            $productLinkEntity = $products[$x]['entity'];
                            $htmlData = $this->curlTo($productUrl, $proxy);

                            if($htmlData['html']){
                                if(is_bool($htmlData['html']) === false){
                                    $html = str_get_html($htmlData['html']);
                                    if(is_bool($html) === false){
                                        $mbgLink = $html->find('#mbgLink', 0);
                                        $itemCondition = $html->find('#vi-itm-cond', 0);
                                        $topRatedPlus = $html->find('#topratedplusimage', 0);

                                        if($mbgLink){
                                            $sellerId = strtolower(trim($mbgLink->plaintext));
                                            $entity = $em->getRepository('AppBundle:GFSellerData')->findOneBy(array('sellerId' => $sellerId));
                                            if(!$entity){
                                                $entity = new GFSellerData();
                                                $entity->setGProductListLinks($productLinkEntity);
                                                $entity->setSellerId($sellerId);
                                                $entity->setStatus('active');
                                                if($topRatedPlus != false){
                                                    $entity->setToExport(0);
                                                    //$entity->setUsedCount(0);
                                                }else{
                                                    $entity->setToExport(1);
                                                    //$entity->setUsedCount(0);
                                                }

                                                $em->persist($entity);
                                                $em->flush();
                                            }else{
                                                if($topRatedPlus != false){
                                                    $entity->setToExport(0);
                                                }
                                                $productLinkEntity->setStatus('complete');

                                                $em->flush();
                                            }
                                        }else{
                                            $productLinkEntity->setStatus('complete');
                                            $em->flush();
                                        }
                                    }else{
                                        $productLinkEntity->setStatus('complete');
                                        $em->flush();
                                    }
                                }else{
                                    $productLinkEntity->setStatus('complete');
                                    $em->flush();
                                }

                            }else{
                                $productLinkEntity->setStatus('complete');
                                $em->flush();
                            }

                        }
                        $em->flush();
                    }
                }
            }
        }

    }

    public function curlTo($url, $proxy)
    {
       // $proxy = null;
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

    public function getProductListLink($em)
    {
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:GProductListLinks p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(50);
        $result = $sql->getResult();

        $lists = array();
        if ($result) {
            for ($x = 0; $x < count($result); $x++) {
                // update status of product list

                $entity = $em->getRepository('AppBundle:GProductListLinks')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'id' => $result[$x]->getId(),
                    'productUrl' => $result[$x]->getProductUrl(),
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