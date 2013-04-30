<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Component\Archive\Generator\MailingList;

use CommonBundle\Component\Util\File\TmpFile,
    DateTime,
    Doctrine\ORM\EntityManager,
    ZipArchive;

/**
 * A class that can be used to generate a ZIP from a given array of
 * mailing lists.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Zip
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var array The array containing the mailinglists
     */
    private $_lists;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The entityManager
     * @param arrays $lists The array containing the mailinglists
     */
    public function __construct(EntityManager $entityManager, array $lists)
    {
        $this->_entityManager = $entityManager;
        $this->_lists = $lists;
    }

    /**
     * Generate an archive to download.
     *
     * @param \CommonBundle\Component\Util\TmpFile $archive The file to write to
     */
    public function generateArchive(TmpFile $archive)
    {
        $zip = new ZipArchive();
        $now = new DateTime();

        $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
        $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
        $zip->close();

        foreach($this->_lists as $list) {
            $entries = $this->_entityManager
                ->getRepository('MailBundle\Entity\Entry')
                ->findByList($list);

            $entriesString = '';
            foreach ($entries as $entry)
                $entriesString .= $entry->getEmailAddress() . PHP_EOL;

            $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
            $zip->addFromString($list->getName(), $entriesString);
            $zip->close();
        }
    }
}