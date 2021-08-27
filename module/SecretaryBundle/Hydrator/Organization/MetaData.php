<?php

namespace SecretaryBundle\Hydrator\Organization;

use CommonBundle\Entity\User\Status\University as UniversityStatus;
use RuntimeException;
use SecretaryBundle\Entity\Organization\MetaData as MetaDataEntity;

class MetaData extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $hydrator = $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic');

        $data = array(
            'academic'          => $hydrator->extract($object->getAcademic()),
            'organization_info' => $this->stdExtract($object, self::$stdKeys),
        );

        $data['organization_info']['become_member'] = $object->becomeMember();

        // Sure thing, if we're here, the user already checked the conditions
        $data['organization_info']['conditions'] = true;

        $organization = $object->getAcademic()
            ->getOrganization($this->getCurrentAcademicYear(true));
        $data['organization_info']['organization'] = $organization !== null ? $organization->getId() : 0;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        $year = $this->getCurrentAcademicYear(false);

        $hydrator = $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic');

        if ($object === null) {
            if (!isset($data['academic'])) {
                throw new RuntimeException('Cannot create metadata without an academic');
            }

            $academic = $data['academic'];

            $academicEntity = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($academic['university']['identification']);

            $academic = $hydrator->hydrate($academic, $academicEntity);

            $object = new MetaDataEntity($academic, $year);
        } else {
            if (isset($data['academic'])) {
                $hydrator->hydrate($data['academic'], $object->getAcademic());
            }
        }

        $academic = $object->getAcademic();

        $data = $data['organization_info'];

        if ($data['become_member'] == '') {
            $data['become_member'] = false;
        }

        if ($academic->canHaveUniversityStatus($year)) {
            $academic->addUniversityStatus(
                new UniversityStatus(
                    $academic,
                    'student',
                    $year
                )
            );
        }

        $this->stdHydrate($data, $object, array('become_member'));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
