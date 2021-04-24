<?php

/**
 * Created on Mon Apr 19 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class ObjectManagerProvider
{


    /**
     * @var string
     */
    protected ?string $managerName;

    /**
     * @var ManagerRegistry
     */
    protected ManagerRegistry $managerRegistry;

    /**
     *
     * @param string $managerName
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry, ?string $managerName)
    {
        $this->managerRegistry = $managerRegistry;
        $this->managerName = $managerName;
    }

    /**
     * Return Persistence Object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->managerRegistry->getManager($this->managerName);
    }
}
