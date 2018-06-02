<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/21/2018
 * Time: 4:51 PM
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


class ScrapeStatusCheckerCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:scrape-status-checker')

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
        $appActivity = $em->getRepository('AppBundle:AppActivity')->find(1);

        if($appActivity['activity'] == 'start'){
            // check by action
            $entity = $em->getRepository('AppBundle:ProductList')->findBy(array('status' => 'active'));
            if(count($entity) == 0){
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'by_title'));
                $actionEntity->setIsActive(0);
            }else{
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'by_title'));
                $actionEntity->setIsActive(1);
            }
            $entity = $em->getRepository('AppBundle:ProductListLinks')->findBy(array('status' => 'active'));
            if(count($entity) == 0){
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'by_seller'));
                $actionEntity->setIsActive(0);
            }else{
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'by_seller'));
                $actionEntity->setIsActive(1);
            }
            $entity = $em->getRepository('AppBundle:SellerData')->findBy(array('status' => 'active'));
            if(count($entity) == 0){
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'seller_data'));
                $actionEntity->setIsActive(0);
            }else{
                $actionEntity = $em->getRepository('AppBundle:ScrapeStatuses')->findOneBy(array('action' => 'seller_data'));
                $actionEntity->setIsActive(1);
            }

            $em->flush();
        }

    }
}