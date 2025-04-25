<?php

namespace App\Faker\Provider\pk_PK;

use Faker\Provider\Internet as BaseInternetProvider;
use App\Faker\Provider\pk_PK\Person;

class Internet extends BaseInternetProvider
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    protected static $firstName;
    protected static $lastName;

    public function __construct($generator)
    {
        parent::__construct($generator);
        
        $gender = $this->generator->optional()->passthrough(static::GENDER_MALE);
        
        $maleNames = [Person::firstNameMale()];
        $femaleNames = [Person::firstNameFemale()];
        
        if ($gender === static::GENDER_MALE) {
            static::$firstName = Person::firstNameMale();
        }
        else if ($gender === static::GENDER_FEMALE) {
            static::$firstName = Person::firstNameFemale();
        }
        else {
            static::$firstName = $this->generator->parse(static::randomElement(array_merge($maleNames, $femaleNames)));
        }

        static::$lastName = Person::firstNameMale();
    }

    /**
     * @var array Pakistani TLDs
     * Add more common TLDs if needed.
     */
    // Note: The TLDs are not exhaustive and can be expanded based on common usage in Pakistan.
    protected static $tld = ['com.pk', 'com', 'net', 'edu.pk', 'gov', 'org', 'gov.pk', 'org.pk', 'biz', 'co', 'pk'];

    /**
     * @var array Pakistani User Name Formats
     * Add more formats if needed.
     */
    protected static $userNameFormats = [
        '{{lastName}}.{{firstName}}',
        '{{firstName}}.{{lastName}}',
        '{{firstName}}##',
        '?{{lastName}}','?{{firstName}}',
        '{{firstName}}{{lastName}}',
        '{{firstName}}{{lastName}}##',
        '{{firstName}}_{{lastName}}',
        '{{lastName}}_{{firstName}}',
    ];

    protected static $domainNameFormats = [
        '{{lastName}}.{{firstName}}',
        '{{firstName}}.{{lastName}}',
        '{{firstName}}##',
        '?{{lastName}}','?{{firstName}}',
        '{{firstName}}{{lastName}}',
        '{{firstName}}{{lastName}}##',
        '{{firstName}}_{{lastName}}',
        '{{lastName}}_{{firstName}}',
        '{{firstName}}-{{lastName}}',
        '{{lastName}}-{{firstName}}',
    ];

    /**
     * @var array Pakistani Username Formats
     */
    public function username()
    {
        $format = static::randomElement(static::$userNameFormats);
        $username = static::bothify($this->generator->parse($format));
        return str_replace(' ', '', $username);
    }

      /**
     * @var array Pakistani Email Formats
     * Add more formats if needed.
     */
    public function companyEmail()
    {
        $username = str_replace(' ', '', static::randomElement(static::$userNameFormats));
        return strtolower(static::bothify($this->generator->parse($username)) . '@example.' . static::randomElement(static::$tld));
    }

    /**
     * @var array Pakistani TLDs
     * Add more common TLDs if needed.
     */
    public function domain()
    {
        $domainName = str_replace(' ', '', static::randomElement(static::$domainNameFormats));
        $domain = static::bothify($this->generator->parse($domainName)). '.' . static::randomElement(static::$tld);
        return strtolower($domain);
    }

     /**
     * @var array Pakistani TLDs
     * Add more common TLDs if needed.
     */
    public function domainName()
    {
        
        return $this->domain();
    }
}