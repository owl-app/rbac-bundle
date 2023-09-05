<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var string */
    private $authItemClass;

    /** @var string */
    private $itemTableName;

    public function __construct(string $authItemClass, string $itemTableName)
    {
        $this->authItemClass = $authItemClass;
        $this->itemTableName = $itemTableName;
    }

    /**
     * @return string[]
     *
     * @psalm-return list{'loadClassMetadata'}
     */
    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments): void
    {
        $metadata = $eventArguments->getClassMetadata();

        if ($metadata->getName() === $this->authItemClass) {
            $metadata->setPrimaryTable(['name' => $this->itemTableName]);
        }
    }
}
