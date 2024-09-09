<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\EventListener;

/**
 * Description of LoginListener
 *
 * @author Diego
 */

use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    /** @var Router */
    protected $router;

    /** @var TokenStorage */
    protected $token;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var Logger */
    protected $logger;

    /**
     * @param Router $router
     * @param TokenStorage $token
     * @param EventDispatcherInterface $dispatcher
     * @param Logger $logger
     */
    public function __construct(Router $router, TokenStorage $token, EventDispatcherInterface $dispatcher, Logger $logger)
    {
        $this->router       = $router;
        $this->token        = $token;
        $this->dispatcher   = $dispatcher;
        $this->logger       = $logger;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $roles = $this->token->getToken()->getRoles();
        $user = $this->token->getToken()->getUser();

        $rolesTab = array_map(function($role){
            return $role->getRole();
        }, $roles);

        $this->logger->info(var_export($rolesTab, true));

//        if($user->getChangePassword()){
//          $route = $this->router->generate('fosuser_edit',['id'=> $user->getId()]);
//        }else{
          if($user->hasRole('ROLE_SUPER_ADMIN')){
            $route = $this->router->generate('ordenes_index');
          }elseif($user->hasRole('ROLE_DESIGN')){
            $route = $this->router->generate('ordenes_index');
          }elseif($user->hasRole('ROLE_INVENTARIO')){
            $route = $this->router->generate('inventarioorden_pend_entrega');
          }elseif($user->hasRole('ROLE_ADMIN_VENTAS')){
            $route = $this->router->generate('productoinventario_index');
          }elseif($user->hasRole('ROLE_VENDEDOR')){
            $route = $this->router->generate('despachoorden_index');
          }elseif($user->hasRole('ROLE_DESPACHOS')){
            $route = $this->router->generate('store_reporte');
          }else{
            $route = $this->router->generate('ordenes_index');
          }
//        }

        $event->getResponse()->headers->set('Location', $route);
    }
}
