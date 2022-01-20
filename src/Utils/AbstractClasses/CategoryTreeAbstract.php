<?php
namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract {

  public $categoriesArrayFromDb;
  public $categorylist;
  protected static $dbconnection;

  public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator) {
    $this->entityManager = $entityManager;
    $this->urlGenerator = $urlGenerator;
    $this->categoriesArrayFromDb = $this->getCategories();
  }

  abstract public function getCategoryList(array $categories_array);

  public function buildTree(int $parent_id = null):array {
    $subCategory = [];
    foreach($this->categoriesArrayFromDb as $category) {
      if($category['parent_id'] == $parent_id) {
        $children = $this->buildTree($category['id']);

        if($children) {
          $category['children'] = $children;
        }
        $subCategory[] = $category;
      }
    }
    return $subCategory;
  }

  private function getCategories():array {
    if(self::$dbconnection) {
      return self::$dbconnection;
    } else {
      $conn = $this->entityManager->getConnection();
      $sql = "SELECT * FROM category";
      //$stmt = $conn->prepare($sql);
      $stmt = $conn->fetchAllAssociative($sql);
      //$stmt->execute();
      //return self::$dbconnection = $stmt->fetchOne();
      return self::$dbconnection = $stmt;
    }
  }
}