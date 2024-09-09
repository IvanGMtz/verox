<?php
namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Avanzu\AdminThemeBundle\Event\NotificationListEvent;
use AppBundle\Model\NotificationModel;
use Doctrine\ORM\EntityManager;

class MyNotificationListener {

	private $user;
    private $em;

    public function __construct(TokenStorage $tokenStorage, EntityManager $em){
      $this->em = $em;
      if($tokenStorage->getToken()){
        $this->user = $tokenStorage->getToken()->getUser();
      }else{
        $this->user = null;
      }
    }

	public function onListNotifications(NotificationListEvent $event) {
      foreach($this->getNotifications() as $Notification) {
        $event->addNotification($Notification);
      }
	}

	protected function getNotifications() {
      $em = $this->em;
      $user = $this->user;
      $notifications = [];
      if($user){
//          if($contactsUnseen > 0){
//            array_push($notifications, new NotificationModel('¡Tienes '.$contactsUnseen.' mensajes nuevos de personas que han contactado a través del sitio web!','info','fas fa-envelope','/admin/contact'));
//          }
      }
      return $notifications;
	}

}