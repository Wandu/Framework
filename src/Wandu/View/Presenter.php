<?php
namespace Wandu\View;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\ContainerInterface;
use Wandu\View\Contacts\PresenterInterface;

class Presenter implements PresenterInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;

    /** @var \Wandu\Http\Contracts\SessionInterface */
    protected $session;
    
    /** @var string */
    protected $path = '';

    public function __construct(ContainerInterface $container, ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->session = $request->getAttribute('session');
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $values = [])
    {
        return render(
            $this->path . $template,
            $values + $this->getInitializeAttributes()
        );
    }

    /**
     * @return array
     */
    protected function getInitializeAttributes()
    {
        return [
            'container' => $this->container,
            'request' => $this->request,
            'session' => $this->session,
        ];
    }
}
