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
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Component\Form;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Node\Entry as FormEntry,
    FormBundle\Entity\Node\Form as FormSpecification,
    FormBundle\Entity\Node\GuestInfo,
    Zend\Http\PhpEnvironment\Request,
    Zend\Mail\Transport\TransportInterface as MailTransport,
    Zend\Mvc\Controller\Plugin\Url;

/**
 * Form actions
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Form
{
    public static function save(FormEntry $formEntry = null, Person $person = null, GuestInfo $guestInfo = null, FormSpecification $formSpecification, $formData, Language $language, EntityManager $entityManager, MailTransport $mailTransport = null, Url $url = null, Request $request)
    {
        if ($person === null && $guestInfo == null) {
            $guestInfo = new GuestInfo(
                $entityManager,
                $formData['first_name'],
                $formData['last_name'],
                $formData['email'],
                $request
            );
        }

        if (null === $formEntry) {
            $formEntry = new FormEntry($person, $guestInfo, $formSpecification, isset($formData['save_as_draft']));
            $entityManager->persist($formEntry);
        }

        if ($formEntry->isGuestEntry()) {
            $formEntry->getGuestInfo()
                ->setFirstName($formData['first_name'])
                ->setLastName($formData['last_name'])
                ->setEmail($formData['email']);
        }

        $formEntry->setDraft(isset($formData['save_as_draft']));

        foreach ($formSpecification->getFields() as $field) {
            $value = isset($formData['field-' . $field->getId()]) ? $formData['field-' . $field->getId()] : '';

            $fieldEntry = $entityManager
                ->getRepository('FormBundle\Entity\Entry')
                ->findOneByFormEntryAndField($formEntry, $field);
            $removed = false;
            $readableValue = null;

            if ($field instanceof FileField) {
                $value = '';
                $filePath = $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('form.file_upload_path');

                if (isset($formData['field-' . $field->getId() . '-removed'])) {
                    $removed = true;

                    if (isset($fieldEntry)) {
                        if (file_exists($filePath . '/' . $fieldEntry->getValue())) {
                            unlink($filePath . '/' . $fieldEntry->getValue());
                        }

                        $formEntry->removeFieldEntry($fieldEntry);
                    }
                } elseif (is_array($formData['field-' . $field->getId()])) {
                    if (null === $fieldEntry || $fieldEntry->getValue() == '') {
                        do {
                            $fileName = sha1(uniqid());
                        } while (file_exists($filePath . '/' . $fileName));
                    } else {
                        $fileName = $fieldEntry->getValue();
                        if (file_exists($filePath . '/' . $fileName)) {
                            unlink($filePath . '/' . $fileName);
                        }
                    }

                    move_uploaded_file($formData['field-' . $field->getId()]['tmp_name'], $filePath . '/' . $fileName);

                    $readableValue = basename($formData['field-' . $field->getId()]['name']);
                    $value = $fileName;

                    if ($value == '' && null !== $fieldEntry) {
                        $value = $fieldEntry->getValue();
                    }
                }
            }

            if (!$removed) {
                if ($fieldEntry) {
                    $fieldEntry->setValue($value)
                        ->setReadableValue($readableValue);
                } else {
                    $fieldEntry = new FieldEntry($formEntry, $field, $value, $readableValue);
                    $formEntry->addFieldEntry($fieldEntry);
                }
            }
        }

        $entityManager->flush();

        if (!isset($formData['save_as_draft'])) {
            if ($formSpecification->hasMail() && isset($mailTransport) && isset($url)) {
                Mail::send($formEntry, $formSpecification, $language, $mailTransport, $url, $request);
            }
        }

        return true;
    }
}
