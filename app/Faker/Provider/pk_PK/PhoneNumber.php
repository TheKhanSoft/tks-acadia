<?php

namespace App\Faker\Provider\pk_PK;

use Faker\Provider\PhoneNumber as BasePhoneNumberProvider;

class PhoneNumber extends BasePhoneNumberProvider
{
    /**
     * @var array Pakistani Mobile Number Formats
     * Covers major operators (codes 00-49 generally). Add more variations if needed.
     * Uses # for a random digit (1-9 usually for first digit of subscriber number)
     * Uses % for a random non-zero digit (1-9)
     */
    protected static $mobileFormats = [
        // +92 format
        '+92 30#-#######', // Mobilink/Jazz (00-09)
        '+92 31#-#######', // Zong (10-19)
        '+92 32#-#######', // Warid (20-29) - Often merged with Jazz
        '+92 33#-#######', // Ufone (30-39)
        '+92 34#-#######', // Telenor (40-49)
        '+92 355-#######', // SCO (Azad Kashmir & GB)
        '+92 36#-#######', // Newer ranges? (Add if known)

        // 03xx format
        '030#-#######',
        '031#-#######',
        '032#-#######',
        '033#-#######',
        '034#-#######',
        '0355-#######',
        '036#-#######',

        // No space/hyphen format
        '030########',
        '031########',
        '032########',
        '033########',
        '034########',
        '0355#######',
    ];

    /**
     * @var array Pakistani Landline Number Formats
     * Add more city codes (Area Codes) and formats.
     */
    protected static $landlineFormats = [
        // +92 format with hyphen
        '+92 21-########',  // Karachi
        '+92 42-########',  // Lahore
        '+92 51-########',  // Islamabad/Rawalpindi
        '+92 41-########',  // Faisalabad
        '+92 61-########',  // Multan
        '+92 91-########',  // Peshawar
        '+92 81-########',  // Quetta
        '+92 22-#######',   // Hyderabad (7 digits)
        '+92 71-#######',   // Sukkur (7 digits)
        '+92 55-#######',   // Gujranwala (7 digits)
        '+92 48-#######',   // Sargodha (7 digits)

        // 0xx format with hyphen
        '021-########',
        '042-########',
        '051-########',
        '041-########',
        '061-########',
        '091-########',
        '081-########',
        '022-#######',
        '071-#######',
        '055-#######',
        '048-#######',

        // 0xx format with space (less common for landlines)
        // '021 ########',
        // '042 ########',
        // '051 ########',
    ];

    /**
     * Generates a Pakistani phone number (mobile or landline).
     * @return string
     */
    public function phoneNumber()
    {
        $formats = array_merge(static::$mobileFormats, static::$landlineFormats);
        $format = static::randomElement($formats);
        return static::numerify($format);
    }

    /**
     * Generates a Pakistani mobile number.
     * @return string
     */
    public function mobileNumber()
    {
        $format = static::randomElement(static::$mobileFormats);
        return static::numerify($format);
    }

    /**
     * Generates a Pakistani landline number.
     * @return string
     */
    public function landlineNumber()
    {
        $format = static::randomElement(static::$landlineFormats);
        return static::numerify($format);
    }

    // Optional: You could add methods for specific operator codes if needed
    // public function jazzNumber() { ... }
    // public function telenorNumber() { ... }
}