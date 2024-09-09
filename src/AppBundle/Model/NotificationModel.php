<?php
namespace AppBundle\Model;
use Avanzu\AdminThemeBundle\Model\NotificationModel as ThemeNotification;

class NotificationModel extends ThemeNotification {
  
  protected $link;

  public function __construct($message = null, $type = 'info', $icon = 'fa fa-warning', $link='#!')
  {
    parent::__construct($message, $type, $icon);
    $this->link = $link;
  }
  
  /**
     * @param mixed $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }
  
}