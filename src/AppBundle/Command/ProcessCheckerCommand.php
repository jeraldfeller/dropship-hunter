<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/14/2018
 * Time: 5:13 AM
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

class ProcessCheckerCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:process-checker')

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
        $entity = $em->getRepository('AppBundle:ProductList')->findAll();
        if($entity){
            for($x = 0; $x < count($entity); $x++){
                $productListId = $entity[$x]->getId();
                $completeCount = 0;
                $entityLinks = $em->getRepository('AppBundle:ProductListLinks')->findBy(array('productListId' => $productListId));
                if($entityLinks){
                    for($y = 0; $y < count($entityLinks); $y++){
                        if($entityLinks[$y]->getStatus() == 'complete'){
                            $output->writeln([$entityLinks[$y]->getProductListId(), 'complete']);
                            $completeCount++;
                        }
                    }

                    if($completeCount == count($entityLinks)){
                        $entity[$x]->setStatus('complete');
                        $em->flush();
                    }
                }

            }
        }
    }
}