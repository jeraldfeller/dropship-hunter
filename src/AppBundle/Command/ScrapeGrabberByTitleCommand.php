<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 6/10/2018
 * Time: 9:35 AM
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

class ScrapeGrabberByTitleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-grabber-title')
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
            $scrapeStatus = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'g_by_title'));
            if ($scrapeStatus->getIsActive() == 1) {
                if ($processEntity->getIsActive() == 1) {
// get proxy list
                    $proxy = $this->getProxy($em);
                    $products = $this->getProductList($em);
                    $ebayUrlTemplate = 'https://www.ebay.com/sch/i.html?_from=R40&_nkw=';
                    if ($products) {
                        for ($x = 0; $x < count($products); $x++) {
                            $titleMatch = 0;
                            $productListId = $products[$x]['id'];
                            $productListEntity = $products[$x]['entity'];
                            $productTitle = strtolower(trim($products[$x]['productTitle']));

                            //$output->writeln([$productTitle]);
                            // check first the strlen of the title
                            if (strlen($productTitle) > 80) {
                                while (strlen($productTitle) > 80) {
                                    $productTitle = preg_replace('/\W\w+\s*(\W*)$/', '$1', $productTitle);
                                }
                            }

                            for($p = 1; $p <= 2; $p++){
                                $url = $ebayUrlTemplate . str_replace(' ', '+', $productTitle).'&_sacat=0&LH_ItemCondition=3&LH_BIN=1&_pgn='.$p;
                                $htmlData = $this->curlTo($url, $proxy);
                                if ($htmlData['html']) {
                                    if(is_bool($htmlData['html']) === false){
                                        $html = str_get_html($htmlData['html']);
                                        if(is_bool($html) === false){
                                            $cont = $html->find('#srp-river-results', 0);
                                            // main content
                                            $matchCountContainer = $html->find('.srp-controls__count-heading', 0);
                                            if ($matchCountContainer) {
                                                $matchCount = preg_replace("/[^0-9,.]/", "", $matchCountContainer->plaintext);
                                            } else {
                                                $matchCount = 0;
                                            }
                                            // get product list
                                            if($cont){
                                                $resultsContainer = $cont->find('.srp-results', 0);
                                                $productList = $cont->find('.s-item');
                                                if ($productList) {
                                                    $titleMatch = 0;
                                                    for ($i = 0; $i < count($productList); $i++) {
                                                        if (isset($productList[$i])) {
                                                            $itemInfoContainer = $productList[$i]->find('.s-item__info', 0);
                                                            $titleContainer = $itemInfoContainer->find('.s-item__link', 0);
                                                            if ($titleContainer) {
                                                                $title = strtolower(trim($titleContainer->plaintext));

                                                                if (strpos($title, $productTitle) !== false) {
                                                                    $output->writeln([$title]);
                                                                    $titleMatch++;
                                                                    $titleLink = $titleContainer->getAttribute('href');

                                                                    $productListLinksEntity = new GProductListLinks();
                                                                    $productListLinksEntity->setGProductList($productListEntity);
                                                                    $productListLinksEntity->setProductUrl($titleLink);
                                                                    $productListLinksEntity->setStatus('active');
                                                                    $em->persist($productListLinksEntity);
                                                                    if(($i % 100) == 0){
                                                                        $em->flush();
                                                                    }
                                                                } else {
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $em->flush();
                                                }
                                            }

                                            if ($titleMatch == 0) {
                                                if ($productListEntity) {
                                                    $productListEntity->setStatus('complete');
                                                }
                                            }
                                        }else{
                                            if ($productListEntity) {
                                                $productListEntity->setStatus('complete');
                                            }
                                        }

                                    }else {
                                        if ($productListEntity) {
                                            $productListEntity->setStatus('complete');
                                        }
                                    }
                                } else {
                                    if ($productListEntity) {
                                        $productListEntity->setStatus('complete');
                                    }
                                }
                            }


                        }

                        $em->flush();
                        $em->clear();
                    }
                }
            }
        }
    }

    public function curlTo($url, $proxy)
    {
        $proxy = null;
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

    public function getProductList($em)
    {
        $sql = $em->createQuery(
            "SELECT p
                FROM AppBundle:GProductList p
                WHERE p.status = 'active' ORDER BY p.id
                "
        )->setMaxResults(50);
        $result = $sql->getResult();

        $lists = array();
        if ($result) {
            for ($x = 0; $x < count($result); $x++) {
                // update status of product list

                $entity = $em->getRepository('AppBundle:GProductList')->find($result[$x]->getId());
                $entity->setStatus('processing');
                $em->flush();

                $lists[] = array(
                    'id' => $result[$x]->getId(),
                    'productTitle' => $result[$x]->getProductTitle(),
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