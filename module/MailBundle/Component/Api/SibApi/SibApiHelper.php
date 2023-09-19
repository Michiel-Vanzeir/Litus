<?php

namespace MailBundle\Component\Api\SibApi;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Exception\GuzzleException;
use MailBundle\Controller\Admin\PreferenceController;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api;
use GuzzleHttp\Client as HttpClient;
use Exception;

class SibApiHelper extends PreferenceController
{
    private $config;

    public function __construct(EntityManager $entityManager) {
        $api_key = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.sib_api');
        $this->config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $api_key);
    }

    public function addAttribute(string $attributeName) {
        $attributesApiInstance = new Api\AttributesApi(
            new HttpClient(),
            $this->config
        );

        $attributeCategory = "normal";
        $createAttribute = new \SendinBlue\Client\Model\CreateAttribute();
        $createAttribute->setType('boolean');

        try {
            $attributesApiInstance->createAttribute($attributeCategory, $attributeName, $createAttribute);
            return SibApiHelperResponse::successful();
        } catch (Exception $e) {
            error_log('Exception when calling Sendinblue AttributesApi->createAttribute: ' . $e->getMessage());
            return SibApiHelperResponse::unsuccessful($e);
        }
    }

    public function updateAttributeForAllContacts(string $attributeName, bool $value) {
        set_time_limit(3000);  // increase php timeout limit
        $emails = $this->getAllUserEmails();
        foreach($emails as $email) {
            $sibApiHelperResponse = $this->updateAttributeForContact($email, $attributeName, $value);
            if (!$sibApiHelperResponse->success)
                return $sibApiHelperResponse;
        }
        set_time_limit(90); // set back to default php timeout limit
        return SibApiHelperResponse::successful();
    }

    /**
     * Updates an attribute of a SendInBlue contact to a value, or leaves it unchanged if
     * the new value is the same as the old value.
     */
    public function updateAttributeForContact(string $email, string $attributeName, bool $value) {
        $apiInstance = new Api\ContactsApi(
            new HttpClient(),
            $this->config
        );

        $data["email"] = $email;
        $data["attributes"] = array($attributeName => $value);
        $data["updateEnabled"] = true;
        $createContact = new \SendinBlue\Client\Model\CreateContact($data);

        try {
            $apiInstance->createContact($createContact);
            return SibApiHelperResponse::successful();
        } catch (Exception $e) {
            error_log('Exception when calling Sendinblue ContactsApi->createContact: ' . $e->getMessage());
            return SibApiHelperResponse::unsuccessful($e);
        }
    }

    /**
     * Returns an array containing al the ids of SendInBlue contacts.
     *
     * @return array
     * @throws GuzzleException
     */
    private function getAllUserEmails() {
        $offset = 0;
        $limit = 1000;
        $emails = $this->getUserEmails($offset, $limit); // add ids from first 1000 contacts (limit imposed by sendinblue)
        while (count($emails)%1000 == 0) { // take next thousand, as long as they exist
            $offset += 1000;
            $emails = array_merge($emails, $this->getUserEmails($offset, $limit));
        }
        return $emails;
    }

    /**
     * Returns an array containing all ids of SendInBlue contacts that are between $offset and $limit in the SendInBlue contact list.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    private function getUserEmails(int $offset, int $limit)
    {
        $batch = $this->getUserBatch($offset, $limit);
        if ($batch !== null) {
            $emails = array_map(function ($item) {
                return $item['email'];
            }, $batch);
            return $emails;
        }
        else {
            throw new Exception("Users batch decoding failed.");
        }
    }

    /**
     * Returns a StreamInterface object which contains information of all SendInBlue contacts that are
     * between $offset and $limit in the SendInBlue contact list.
     */
    private function getUserBatch(int $offset, int $limit) {
        $apiInstance = new Api\ContactsApi(
            new HttpClient(),
            $this->config
        );

        try {
            $result = $apiInstance->getContacts($limit, $offset);
            return $result->getContacts();
        } catch (Exception $e) {
            error_log('Exception when calling Sendinblue ContactsApi->getContacts: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Returns an array containing all the positions of $needle in $haystack.
     *
     * @param string $haystack
     * @param string $needle
     * @return array
     */
    private function strPosAll($haystack, $needle) {
        $s = 0;
        $i = 0;
        while(is_integer($i)) {
            $i = mb_stripos($haystack, $needle, $s);
            if(is_integer($i)) {
                $aStrPos[] = $i;
                $s = $i + mb_strlen($needle);
            }
        }
        if(isset($aStrPos)) return $aStrPos;
        else return array();
    }

    public function getContactDetails($id) {
        $apiInstance = new Api\ContactsApi(
            new HttpClient(),
            $this->config
        );

        try {
            $result = $apiInstance->getContactInfo($id);
            error_log($result);
        } catch (Exception $e) {
            error_log('Exception when calling ContactsApi->getContactInfo: ' . $e->getMessage());
        }
    }

}