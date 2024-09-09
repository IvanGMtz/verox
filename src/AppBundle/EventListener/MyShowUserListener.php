<?php
namespace AppBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\ShowUserEvent;
use AppBundle\Entity\FosUser as BaseUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MyShowUserListener {
  private $user;
  
  public function __construct(TokenStorage $tokenStorage){
    if($tokenStorage->getToken()){
      $this->user = $tokenStorage->getToken()->getUser();
    }else{
      $this->user = null;
    }
  }
  
  public function onShowUser(ShowUserEvent $event) {
      $user = $this->getUser();
      $event->setUser($user);
      
      $event->setShowProfileLink(true);

//      $event->addLink(new NavBarUserLink('Followers', 'logout'));
//      $event->addLink(new NavBarUserLink('Sales', 'logout'));
//      $event->addLink(new NavBarUserLink('Friends', 'logout', ['id' => 2]));
  }

  protected function getUser() {
    return $this->user;
  }

}
