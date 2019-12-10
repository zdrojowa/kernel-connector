<?php

namespace Zdrojowa\KernelConnector\Producers;

use Kosinski\Amqp\Contracts\Producer;
use Selene\Contracts\Acl\Presence\AclPresence;
use Zdrojowa\AuthenticationLink\AuthenticationLink;
use Zdrojowa\AuthenticationLink\Exceptions\InvalidSystemException;

/**
 * Class AclPresenceProducer
 * @package Zdrojowa\KernelConnector\Producers
 */
final class AclPresenceProducer extends Producer
{

    /**
     * @var string
     */
    private ?string $parent;
    /**
     * @var int
     */
    private int $systemId;

    /**
     * @var string
     */
    protected string $queueName = 'authorization-permissions';

    /**
     * @var mixed|string
     */
    private string $anchor;

    /**
     * @var int|mixed
     */
    private int $system;

    /**
     * @var mixed|string
     */
    private string $name;

    /**
     * AclPresenceProducer constructor.
     *
     * @param AclPresence $aclPresence
     * @param AuthenticationLink $authenticationLink
     * @param int $systemId
     * @param string|null $parentAnchor
     *
     */
    public function __construct(AclPresence $aclPresence, AuthenticationLink $authenticationLink, int $systemId, string $parentAnchor = null)
    {
        $this->anchor = $aclPresence->getAnchor();
        $this->name = $aclPresence->getName();
        $this->parent = $parentAnchor;
        $this->system = $systemId;

    }

    /**
     * @return string
     */
    public function produce(): string
    {
        return json_encode((object)$this->toArray());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'anchor' => $this->anchor,
            'system' => $this->system,
            'parent' => $this->parent,
            'name' => $this->name,
        ];
    }
}
