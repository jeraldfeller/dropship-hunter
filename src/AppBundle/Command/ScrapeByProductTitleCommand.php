<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/14/2018
 * Time: 2:22 AM
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
include('simple_html_dom_node.php');

class ScrapeByProductTitleCommand  extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-by-title')

            // the short description shown while running "php bin/console list"
            ->setDescription('search for product title in ebay')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')

         //   ->addArgument('action', InputArgument::REQUIRED, 'What type of action do you want to execute?')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // check if process status is active
        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        if($processEntity->getIsActive() == 1){

            // get proxy list
            $proxy = $this->getProxy($em);
            $products = $this->getProductList($em);
            $ebayUrlTemplate = 'https://www.ebay.com/sch/i.html?_nkw=';

            if($products){
                for($x = 0; $x < count($products); $x++) {
                    $productListId = $products[$x]['id'];
                    $productTitle = strtolower(trim($products[$x]['productTitle']));
                    $output->writeln([$productTitle]);
                    // check first the strlen of the title
                    if(strlen($productTitle) > 80){
                        while(strlen($productTitle) > 80){
                            $productTitle = preg_replace('/\W\w+\s*(\W*)$/', '$1', $productTitle);
                        }
                    }

                    $url = $ebayUrlTemplate.str_replace(' ', '+', $productTitle);

                    $output->writeln([$productTitle]);


                    $htmlData = $this->curlTo($url, $proxy);

                    if($htmlData['html']){
                        $epoch = time();
                        $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $this->delete_all_between('<head>', '</head>', trim($htmlData['html'])));
                        $myfile = fopen('web/tmp/tmp-'.$epoch.'.html', "w") or die("Unable to open file!");
                        fwrite($myfile, $htmlNew);
                        fclose($myfile);

                        $htmlNew = file_get_contents('web/tmp/tmp-'.$epoch.'.html');

                        // deleted after
                        unlink('web/tmp/tmp-'.$epoch.'.html');
                        $html = str_get_html($htmlNew);

                        // main content
                        $matchCountContainer = $html->find('.rcnt', 0);
                        if($matchCountContainer){
                            $matchCount = $matchCountContainer->plaintext;
                        }else{
                            $matchCount = 0;
                        }
                        // get product list
                        $productList = $html->find('.lvtitle');
                        if($productList){
                            for($i = 0; $i < $matchCount; $i++){
                                $titleContainer = $productList[$i]->find('a', 0);
                                if($titleContainer){
                                    $title = strtolower(trim($titleContainer->plaintext));
                                    if($title == $productTitle){
                                        $titleLink = $titleContainer->getAttribute('href');

                                        $productListLinksEntity = new ProductListLinks();
                                        $productListLinksEntity->setProductListId($productListId);
                                        $productListLinksEntity->setProductUrl($titleLink);
                                        $productListLinksEntity->setStatus('active');
                                        $em->persist($productListLinksEntity);
                                        $em->flush();
                                        $output->writeln([$title]);
                                    }
                                }
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

    public function getProductList($em){
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:ProductList p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(10);
        $result = $sql->getResult();

        $lists = array();
        if($result){
            for($x = 0; $x < count($result); $x++){
                // update status of product list

                $entity = $em->getRepository('AppBundle:ProductList')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'id' => $result[$x]->getId(),
                    'productTitle' => $result[$x]->getProductTitle()
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