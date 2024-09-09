<?php
namespace AppBundle\Model;
use Avanzu\AdminThemeBundle\Model\MessageModel as ThemeMessage;

class MessageModel extends ThemeMessage {
  private $icon;
  private $link;
  
  public function __construct($subject = '', $link = '', $icon = 'fa fa-users', $sendAt)
  {
    parent::__construct(null, $subject, $sendAt , null);
    $this->icon = $icon;
    $this->link = $link;
  }
  
  public function getIcon() {
    return $this->icon;
  }
  
  public function getLink() {
    return $this->link;
  }

  public function getIdentifier() {
    return 1;
  }

}