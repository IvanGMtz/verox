<?php
namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Avanzu\AdminThemeBundle\Event\MessageListEvent;
use AppBundle\Model\MessageModel;
use Doctrine\ORM\EntityManager;

class MyMessageListListener {

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
    
    public function onListMessages(MessageListEvent $event) {

		foreach($this->getMessages() as $message) {
			$event->addMessage($message);
		}

	}

	protected function getMessages() {
        //(UserInterface $from = null, $subject = '', $sentAt = null, UserInterface $to = null)
		// retrieve your message models/entities here
        $em = $this->em;
        $mensajes = array();
        return $mensajes;
	}

}