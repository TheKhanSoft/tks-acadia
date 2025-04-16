<?php

namespace App\Faker\Provider\pk_PK;

use Faker\Provider\Company as BaseCompanyProvider;

class Company extends BaseCompanyProvider
{
    /**
     * @var array Pakistani Company Suffixes
     * Add more variations
     */
    protected static $companySuffixes = [
        'Pvt Ltd', 'Ltd', 'Limited', 'Group', 'Enterprises', 'Corporation', 'Foundation', 'Trust',
        'Industries', 'Mills', 'Traders', 'Services', 'Solutions', 'Consultants', 'Builders',
        'Developers', 'Associates', 'Brothers', '& Sons', '& Co'
    ];

    /**
     * @var array Pakistani Industry Types
     * Add many more specific industries
     */
    protected static $industry = [
        'Textile', 'Software', 'Pharmaceutical', 'Construction', 'Trading', 'Logistics', 'Education',
        'Consulting', 'Engineering', 'Foods', 'Cement', 'Automotive', 'Steel', 'Chemical', 'Energy',
        'Telecommunication', 'Media', 'Garments', 'Leather', 'Surgical Instruments', 'Sports Goods',
        'Real Estate', 'Hospitality', 'Security', 'Import Export'
    ];

    /**
     * @var array Formats for generating company names
     * Uses methods from Person, Address, and this provider.
     * Ensure Person and Address providers are registered BEFORE this one.
     */
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companySuffix}}',
        '{{lastName}} and {{lastName}} {{companySuffix}}',
        '{{firstName}} {{lastName}} {{companySuffix}}',
        '{{lastName}} Group',
        '{{lastName}} Brothers',
        '{{lastName}} & Sons',
        '{{lastName}} Enterprises',
        '{{lastName}} Industries',
        '{{lastName}} Mills {{companySuffix}}',
        '{{city}} {{industry}}', // Requires Address provider
        '{{city}} {{industry}} {{companySuffix}}', // Requires Address provider
        'Pakistan {{industry}} {{companySuffix}}',
        'National {{industry}} {{companySuffix}}',
        'United {{industry}} {{companySuffix}}',
        'Global {{industry}} Solutions',
        '{{industry}} Services Pvt Ltd',
        'Al-{{lastName}} {{industry}}', // Common prefix
        'Packages {{companySuffix}}', // Example specific name structure
        'Nishat {{companySuffix}}',   // Example specific name structure
    ];

    /**
     * @var array Formats for catchphrases (optional, inherited but can be customized)
     */
    // protected static $catchPhraseFormats = [...];

    /**
     * @var array Formats for BS words (optional, inherited but can be customized)
     */
    // protected static $bsFormats = [...];


    /**
     * Generates a Pakistani industry name.
     * @return string
     */
    public function industry()
    {
        return static::randomElement(static::$industry);
    }

    /**
     * Generates a Pakistani company name using defined formats.
     * Requires Person and Address providers to be registered if formats use their methods.
     * @return string
     */
    public function company()
    {
        $format = static::randomElement(static::$formats);
        // The parse method uses the generator to find methods like 'lastName', 'city', etc.
        return $this->generator->parse($format);
    }

    /**
     * Returns a random company suffix.
     * @return string
     */
    public static function companySuffix()
    {
        return static::randomElement(static::$companySuffixes);
    }

    // Inherits catchPhrase() and bs() from BaseCompanyProvider, customize if needed.
}