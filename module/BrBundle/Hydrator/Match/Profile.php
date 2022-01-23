<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Hydrator\Match;

use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use BrBundle\Entity\Match\Profile\StudentProfile as StudentProfileEntity;
use BrBundle\Entity\Match\Profile as ProfileEntity;
use BrBundle\Entity\Match\Profile\CompanyProfile as CompanyProfileEntity;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;

/**
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class Profile extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        foreach ($object->getFeatures() as $feature){
            $data['feature_' . $feature->getFeature()->getId()] = $feature->getImportance();
        }
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            if ($data['profile_type'] === 'student') {
                $object = new StudentProfileEntity();
            } elseif ($data['profile_type'] === 'company') {
                $object = new CompanyProfileEntity();
            }
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        return $object;
    }
}
