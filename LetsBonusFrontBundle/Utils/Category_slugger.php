<?php

namespace iFlair\LetsBonusFrontBundle\Utils;

use Doctrine\ORM\EntityManager;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Category_slugger extends Controller
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function checkParentCategoryHasCategory($parentCatId)
    {
        $hasCategory = false;

        $em = $this->em;
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT c.*  FROM  lb_category AS c where parent_category_id='.$parentCatId);

        $query->execute();
        unset($categories);

        $categories = $query->fetchAll();

        if (count($categories) > 0) {
            $hasCategory = true;
        }

        return $hasCategory;
    }

    public function checkCategoryHasChildCategory($categoryId)
    {
        $hasChildCategory = false;

        $em = $this->em;
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT cc.*  FROM  lb_child_category AS cc where category_id='.$categoryId);
        $query->execute();
        unset($childCategories);
        $childCategories = $query->fetchAll();

        if (count($childCategories) > 0) {
            $hasChildCategory = true;
        }

        return $hasChildCategory;
    }

    public function getCategoriesTrimmedToSize($categories, $size)
    {
        $trimmedCategories = array();
        foreach ($categories as $key => $categoriesArray) {
            $trimmedCategories[$key] = array_slice($categoriesArray, 0, $size, true);
        }

        return $trimmedCategories;
    }

    public function getTotalParentCategoryCount()
    {
        $em = $this->em;

        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT count(distinct(ls.vprogram_id)) as counts FROM lb_shop as ls JOIN lb_shop_parent_category as lsp ON ls.id = lsp.shop_id');

        $query->execute();
        $parentCategory = $query->fetch();
        $parentCategoryCount = $parentCategory['counts'];
        unset($connection);

        return $parentCategoryCount;
    }

    public function getTotalCategoryCountByParentCatId($id)
    {
        $em = $this->em;

        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT count(distinct(ls.vprogram_id)) as counts FROM lb_shop as ls JOIN lb_shop_category as lc ON ls.id = lc.shop_id JOIN lb_category as c ON c.id = lc.category_id WHERE c.parent_category_id = :parent_category_id');
        $query->bindValue('parent_category_id', $id);
        $query->execute();
        $category = $query->fetch();
        $categoryCount = $category['counts'];
        unset($connection);

        return $categoryCount;
    }

    public function getTotalChildCategoryCountByParentCatId($id)
    {
        $em = $this->em;

        $connection = $em->getConnection();

        $query = $connection->prepare('SELECT count(distinct(ls.vprogram_id)) as counts FROM lb_shop as ls JOIN lb_shop_child_category as lcc ON ls.id = lcc.shop_id JOIN lb_child_category as cc ON cc.id = lcc.child_category_id WHERE cc.category_id = :category_id');
        $query->bindValue('category_id', $id);
        $query->execute();
        $childCategory = $query->fetch();
        $childCategoryCount = $childCategory['counts'];

        unset($connection);

        return $childCategoryCount;
    }

    /*public function getImageUrl($entities, $imageId, $imageType='default_big')return 
    {
        $media = $entities->findOneBy(array('nimage' => $imageId));
        if(!empty($media) && !empty($imageId)) {
            $media = $media->getnImage();
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $imageUrl = $provider->generatePublicUrl($media, $imageType);
            return $imageUrl;
        }
    }*/
}
