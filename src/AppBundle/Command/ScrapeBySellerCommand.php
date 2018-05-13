<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/14/2018
 * Time: 3:46 AM
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

class ScrapeBySellerCommand  extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-by-seller')

            // the short description shown while running "php bin/console list"
            ->setDescription('search for seller in ebay')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')

            //   ->addArgument('action', InputArgument::REQUIRED, 'What type of action do you want to execute?')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        if($processEntity->getIsActive() == 1){
            $proxy = $this->getProxy($em);
            $products = $this->getProductListLink($em);
            $ebayUrlTemplate = 'https://www.ebay.com/usr/';
            if($products){
                for($x = 0; $x < count($products); $x++) {
                    $productListId = $products[$x]['productListId'];
                    $productUrl = trim($products[$x]['productUrl']);

                    $htmlData = $this->curlTo($productUrl, $proxy);
                    if($htmlData['html']){
                        $html = str_get_html($htmlData['html']);
                        $mbgLink = $html->find('#mbgLink', 0);
                        if($mbgLink){
                            $output->writeln([$mbgLink->plaintext]);
                            // check if seller exist
                            $entity = $em->getRepository('AppBundle:SellerData')->findOneBy(array('sellerId' => trim($mbgLink->plaintext)));
                            if(!$entity){
                                $entity = new SellerData();
                                $entity->setProductListId($productListId);
                                $entity->setSellerId(trim($mbgLink->plaintext));
                                $entity->setStatus('active');
                                $em->persist($entity);
                                $em->flush();
                            }
                        }


                    }
                }
            }
        }

    }


    public function curlTo($url, $proxy){
       // $proxy = null;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($proxy != NULL) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy[mt_rand(0,count($proxy) - 1)]);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $contents = curl_exec($curl);
        curl_close($curl);
        return array('html' => $contents);
    }

    public function delete_all_between($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return str_replace($textToDelete, '', $string);
    }

    public function getProductListLink($em){
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:ProductListLinks p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(10);
        $result = $sql->getResult();

        $lists = array();
        if($result){
            for($x = 0; $x < count($result); $x++){
                // update status of product list

                $entity = $em->getRepository('AppBundle:ProductListLinks')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'productListId' => $result[$x]->getProductListId(),
                    'productUrl' => $result[$x]->getProductUrl()
                );
            }
        }

        return $lists;
    }

    public function getProxy($em){
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:ProxyList p
                "
        );
        $result = $sql->getResult();

        $lists = array();
        if($result){
            for($x = 0; $x < count($result); $x++){
                $lists[] = $result[$x]->getProxy();
            }
        }

        return $lists;
    }
}