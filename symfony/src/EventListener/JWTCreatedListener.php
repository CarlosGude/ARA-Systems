<?php


namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var User $user */
        $user = $event->getUser();

        if (!$request) {
            throw new RuntimeException('Invalid request.');
        }

        $payload = $event->getData();

        $payload['ip'] = $request->getClientIp();

        $payload['user_id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['email'] = $user->getEmail();

        $payload['company']['id'] = $user->getCompany()->getId();
        $payload['company']['name'] = $user->getCompany()->getName();

        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}
