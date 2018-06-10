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


use AppBundle\Entity\GProductList;
use AppBundle\Entity\GProductListLinks;
use AppBundle\Entity\ProxyList;
use AppBundle\Entity\ProcessStatus;

class ProcessCheckerGrabberCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('execute:process-checker-grabber')

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
        $appActivity = $em->getRepository('AppBundle:AppActivity')->findOneBy(array('app' => 'app_3'));
        if($appActivity->getActivity() == 'play'){
            $entity = $em->getRepository('AppBundle:GProductList')->findBy(array('status' => 'processing'));
            if($entity){
                for($x = 0; $x < count($entity); $x++){
                    $productListId = $entity[$x]->getId();
                    $completeCount = 0;
                    $entityLinks = $em->getRepository('AppBundle:GProductListLinks')->findBy(array('gProductList' => $productListId));
                    if($entityLinks){
                        for($y = 0; $y < count($entityLinks); $y++){
                            if($entityLinks[$y]->getStatus() == 'complete'){
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
}
