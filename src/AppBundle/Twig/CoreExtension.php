<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoreExtension extends \Twig_Extension
{
    protected $container;

    protected $request;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get('request_stack')->getCurrentRequest();
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('active', array($this, 'isActive')),
            new \Twig_SimpleFilter('tags', array($this, 'getTags')),
        );
    }

    public function isActive($route, $class) {
        if ($this->request instanceof Request) {
            if ($this->request->get('_route') == $route) {
                return $class;
            }
        }

        return '';
    }

    public function getTags($content)
    {
        return $this->container->get('note.manager')->highlightTag($content);
    }

    public function getName()
    {
        return 'core_extension';
    }
}