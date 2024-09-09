<?php
namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BrandingService {
  private $em;
  public $enterprise;
  public function __construct(EntityManager $entityManager) {
      $this->em = $entityManager;
      $this->enterprise = $this->em->getRepository('AppBundle:Configuration')
                ->createQueryBuilder('c')
                ->select('c')
                ->orderBy('c.id','DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
  }
}
