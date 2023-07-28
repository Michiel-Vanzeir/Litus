<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\Article as ArticleEntity;

class Article extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'additional_info', 'spot', 'amount_owned', 'amount_available', 'internal_comment', 'alertMail', 'location');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['warranty'] = $object->getWarranty() / 100;
        $data['rent'] = $object->getRent() / 100;
        $data['visibility'] = $object->getVisibilityCode();
        $data['status'] = $object->getStatusKey();
        $data['category'] = $object->getCategoryCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ArticleEntity();
        }


        $object->setWarranty($data['warranty'] !== null ? $data['warranty'] * 100 : 0);
        $object->setRent($data['rent'] !== null ? $data['rent'] * 100 : 0);

        $object->setVisibility($data['visibility']);
        $object->setStatus($data['status']);
        $object->setCategory($data['category']);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
