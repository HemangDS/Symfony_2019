<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class UpdateParentCategorySlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:updateparentcategoryslugs')->setDescription('Update parent category slug and remove counts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $parentCategoryRepo = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');
        $parentCategorySlugs = $parentCategoryRepo->findByCategoryType(Constants::PARENT_CATEGORY_IDENTIFIER);
        if($parentCategorySlugs) {
            foreach($parentCategorySlugs as $parentCategorySlug) {
                //echo "parent category: ".$parentCategorySlug->getSlugName()."\n";
                $slug = $parentCategorySlug->getSlugName();
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
                        $slugExists = $parentCategoryRepo->findOneBySlugName($updatedSlug);
                        if(!$slugExists) {
                            $parentCategorySlug->setSlugName($updatedSlug);
                            $em->persist($parentCategorySlug);                            
                        }
                    }
                }
            }
        }
        
        $em->flush();
    }
}
