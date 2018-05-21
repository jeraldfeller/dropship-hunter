<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/14/2018
 * Time: 12:25 AM
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use AppBundle\Entity\ProxyList;
use AppBundle\Entity\ProductList;
use AppBundle\Entity\ProcessStatus;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $proxyList = json_decode($this->getProxyAction()->getContent(), true);
        // replace this example code with whatever you need

        return $this->render('default/index.html.twig', [
            'proxyList' => $proxyList,
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/main/get")
     */
    public function getProductListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:ProductList')->findAll();
        $data = array();
        $totalCount = count($entity);
        $completeCount = 0;
        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        $status = true;
        try {
            if ($entity) {
                for ($x = 0; $x < count($entity); $x++) {
                    if ($entity[$x]->getStatus() == 'complete') {
                        $completeCount++;
                    }

                }
            }
            //$status = true;
        } catch (\Exception $e) {
            $status = false;
        }
        return new Response(
            json_encode(
                array(
                    'isActive' => $processEntity->getIsActive(),
                    'totalCount' => $totalCount,
                    'completeCount' => $completeCount,
                    'data' => $data,
                    'status' => $status
                )
            )
        );
    }

    /**
     * @Route("/main/import")
     */
    public function importProductListAction()
    {
        $date = date('Y-m-d H:i:s');
        $em = $this->getDoctrine()->getManager();

        // turn off process
        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        $processEntity->setIsActive(0);
        $em->flush();

        // clear list;
        $query = $em->createQuery("
        DELETE
        FROM AppBundle:ProductList
        ");
        $query->execute();

        $query = $em->createQuery("
        DELETE
        FROM AppBundle:ProductListLinks
        ");
        $query->execute();

        $query = $em->createQuery("
        DELETE
        FROM AppBundle:SellerData
        ");
        $query->execute();

        // import new list;

        $spreadsheet_url = json_decode($_POST['param'], true);
        if (!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";
        $i = 0;
        if (($handle = fopen($spreadsheet_url['url'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($i > 0) {
                    $entity = new ProductList();
                    $entity->setProductTitle($data[0]);
                    $entity->setStatus('active');
                    $entity->setTimestamp(new \DateTime($date));
                    $em->persist($entity);

                    if (($i % 100) == 0) {
                        $em->flush();
                    }
                }
                $i++;
            }
            fclose($handle);
        }

        $em->flush();

        // active process

        $processEntity = $em->getRepository('AppBundle:ProcessStatus')->find(1);
        $processEntity->setIsActive(1);
        $actionEntity = $em->getRepository('AppBundle:ActionStatus')->findAll();
        for($a = 0; $a < count($actionEntity); $a++){
            $actionEntity[$a]->setIsActive(1);
        }
        $em->flush();

        return new Response(json_encode(true));
    }

    /**
     * @Route("/proxy/get")
     */
    public function getProxyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:ProxyList')->findAll();
        $proxyList = array();
        if ($entity) {
            for ($x = 0; $x < count($entity); $x++) {
                $proxyList[] = array('ip' => $entity[$x]->getProxy());
            }
        }


        return new Response(
            json_encode($proxyList)
        );
    }

    /**
     * @Route("/proxy/update")
     */
    public function updateProxyAction()
    {
        $data = json_decode($_POST['param'], true);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("
        DELETE
        FROM AppBundle:ProxyList
        ");
        $query->execute();


        for ($x = 0; $x < count($data['proxy']); $x++) {
            $entity = new ProxyList();
            $entity->setProxy($data['proxy'][$x]);
            $em->persist($entity);
        }
        $em->flush();

        return new Response(json_encode(true));
    }

    /**
     * @Route("/main/export")
     */
    public function exportSellerDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $file = 'Seller_Data_' . time() . '.csv';
        $entity = $em->getRepository('AppBundle:SellerData')->findAll();
        $data = array();

        if ($entity) {
            for ($x = 0; $x < count($entity); $x++) {
                $data[] = array(
                    'sellerId' => trim($entity[$x]->getSellerId()),
                    'sellerLocation' => $entity[$x]->getSellerLocation(),
                    'sellerRank' => $entity[$x]->getSellersRank(),
                    'memberSince' => '"' . $entity[$x]->getMemberSince() . '"',
                    'positive' => $entity[$x]->getPositive(),
                    'neutral' => $entity[$x]->getNeutral(),
                    'negative' => $entity[$x]->getNegative(),
                    'itemsForSale' => $entity[$x]->getItemsForSale(),
                    'sellerPage' => 'https://www.ebay.com/usr/' . trim($entity[$x]->getSellerId()),
                    'sellerStorePage' => $entity[$x]->getSellerPage()
                );
            }
        }

        $response = $this->render('export/csv-template.html.twig', array('data' => $data));

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $file);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');


        return $response;
    }
}
