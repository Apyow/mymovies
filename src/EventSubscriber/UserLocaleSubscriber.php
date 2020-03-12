<?php
// src/EventSubscriber/UserLocaleSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;
    private string $default_locale;

    public function __construct(SessionInterface $session, string $default_locale)
    {
        $this->session = $session;
        $this->default_locale = $default_locale;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event) : void
    {
        $user = $event->getAuthenticationToken()->getUser();

        // On login, if no _local in session, set with the default local (services.yaml).
        // Without, category and domain list bug.
        if(!$this->session->get('_locale')){
            $this->session->set('_locale',$this->default_locale);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => array(array('onInteractiveLogin', 15)),
        );
    }
}
