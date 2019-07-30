<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class UpdateBrandSlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:updatebrandslugs')->setDescription('Update brand slug and remove counts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $marcasRepo = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');
        $marcasSlugs = $marcasRepo->findByCategoryType(Constants::MARCAS_IDENTIFIER);
        if($marcasSlugs) {
            foreach($marcasSlugs as $marcasSlug) {
                //echo "marcas: ".$marcasSlug->getSlugName()."\n";
                $slug = $marcasSlug->getSlugName();
                if(strpos($slug, "-") !== false) {
                    $expSlug = explode("-", $slug);
                    $slugChunks = count($expSlug);
                    //echo "count: ".$slugChunks."\n";
                    //Get last chunk
                    $lastChunk = $expSlug[$slugChunks - 1];
                    //echo "last chunk: ".$lastChunk."\n";
                    if(is_numeric($lastChunk)) {
                        //echo "last numeric chunk: ".$lastChunk."\n";
                        $updatedSlug = join('-', array_slice($expSlug, 0, -1));
                        //echo "final result: ".$updatedSlug."\n";
                        //Do not update slug if exists
                        $slugExists = $marcasRepo->findOneBySlugName($updatedSlug);
                        if(!$slugExists) {
                            $marcasSlug->setSlugName($updatedSlug);
                            $em->persist($marcasSlug);                            
                        }
                    }
                }
            }
        }
        
        $em->flush();
    }
}
