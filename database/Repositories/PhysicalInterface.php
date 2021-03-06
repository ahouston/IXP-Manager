<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;


/**
 * PhysicalInterface
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PhysicalInterface extends EntityRepository
{

    /**
     * Provide array of physical interfaces for the list Action
     *
     * @return array An array of physical interfaces
     */
    public function getForList(): array
    {
        return $this->getEntityManager()->createQuery(
            "SELECT pi.id AS id, pi.speed AS speed, pi.duplex AS duplex, pi.status AS status,
                    pi.notes AS notes, pi.autoneg AS autoneg,
                    c.name AS customer, c.id AS custid,
                    s.name AS switch, s.id AS switchid,
                    vi.id AS vintid,
                    sp.type as type, ppi.id as ppid, fpi.id as fpid,
                    sp.name AS port, l.id AS locid, l.name AS location
                    FROM \\Entities\\PhysicalInterface pi
                        LEFT JOIN pi.PeeringPhysicalInterface ppi
                        LEFT JOIN pi.FanoutPhysicalInterface fpi
                        LEFT JOIN pi.VirtualInterface vi
                        LEFT JOIN vi.Customer c
                        LEFT JOIN pi.SwitchPort sp
                        LEFT JOIN sp.Switcher s
                        LEFT JOIN s.Cabinet cab
                        LEFT JOIN cab.Location l"
            )->getArrayResult();
    }

}
