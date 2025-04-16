<?php

namespace App\Faker\Provider\pk_PK; // Adjust namespace

use Faker\Provider\Base;

class Address extends Base
{
    protected static $provinces = [
        'Punjab', 'Sindh', 'Khyber Pakhtunkhwa', 'Balochistan', 'Gilgit-Baltistan', 'Azad Kashmir', 'Islamabad Capital Territory'
    ];

    // IMPORTANT: Expand these lists significantly for better variety! These are just small samples.
    protected static $cities = [
        'Karachi', 'Lahore', 'Faisalabad', 'Rawalpindi', 'Gujranwala', 'Peshawar', 'Multan', 'Hyderabad', 'Islamabad', 'Quetta',
        'Sialkot', 'Sukkur', 'Larkana', 'Bahawalpur', 'Sargodha', 'Mardan', 'Sheikhupura', 'Rahim Yar Khan', 'Jhang', 'Dera Ghazi Khan',
        'Gujrat', 'Sahiwal', 'Wah Cantonment', 'Mingora', 'Okara', 'Kasur', 'Nawabshah'
        // ... Add many more cities
    ];

    protected static $streetSuffixes = [
        'Street', 'Road', 'Avenue', 'Lane', 'Block', 'Sector', 'Town', 'City', 'Colony', 'Chowk', 'Bazar', 'Markaz', 'Phase'
    ];

    protected static $villages = [
        'Saidpur', 'Nurpur Shahan', 'Malpur', 'Shah Allah Ditta', 'Kuri', 'Gaggo Mandi', 'Kamalia Village', 'Chak 44', 'Dera Ghazi Khan Village', 'Tibba Sultanpur', 'Mohenjo-daro Village', 'Umerkot Village', 'Hala Village', 'Ranipur Riyasat Village', 'Jamrud Village', 'Landi Kotal Village', 'Parachinar Village', 'Turbat Village', 'Khuzdar Village', 'Pasni Village'
        // ... Add many more representative village names
    ];

    protected static $mohallahs = [
        'Mohallah Islamabad', 'Mohallah Faisal Town', 'Mohallah Gulberg', 'Mohallah Defence (DHA)', 'Mohallah Saddar', 'Mohallah Shah Faisal Colony', 'Mohallah Korangi', 'Mohallah Lyari', 'Mohallah Krishan Nagar', 'Mohallah Walled City', 'Mohallah Model Town', 'Mohallah Iqbal Town', 'Mohallah Hayatabad', 'Mohallah University Town', 'Mohallah Cantt', 'Mohallah Jinnah Town', 'Mohallah Satellite Town', 'Mohallah Civil Lines', 'Mohallah Peoples Colony'
        // ... Add many more representative names
    ];

    // --- NEW: Add Tehsil data ---
    // IMPORTANT: Expand this list significantly! Grouping by district/province recommended for accuracy.
    protected static $tehsils = [
        // Punjab Examples
        'Lahore City Tehsil', 'Model Town Tehsil', 'Raiwind Tehsil', 'Rawalpindi Tehsil', 'Gujar Khan Tehsil', 'Faisalabad City Tehsil', 'Jaranwala Tehsil', 'Multan City Tehsil', 'Shujabad Tehsil',
        // Sindh Examples
        'Gulshan-e-Iqbal Town', 'Saddar Town', 'Hyderabad City Taluka', 'Qasimabad Taluka', 'Sukkur City Taluka', 'Rohri Taluka',
        // KPK Examples
        'Peshawar City Tehsil', 'Charsadda Tehsil', 'Mardan Tehsil', 'Swabi Tehsil', 'Abbottabad Tehsil',
        // Balochistan Examples
        'Quetta City Tehsil', 'Khuzdar Tehsil', 'Turbat Tehsil', 'Gwadar Tehsil',
        // ICT Example
        'Islamabad Tehsil',
        // ... Add many more
    ];

    // --- NEW: Add District data ---
    // IMPORTANT: Expand this list significantly! Should align with provinces.
    protected static $districts = [
        'Lahore', 'Rawalpindi', 'Faisalabad', 'Multan', 'Gujranwala', 'Sialkot', 'Bahawalpur', 'Sargodha', // Punjab
        'Karachi East', 'Karachi West', 'Karachi South', 'Karachi Central', 'Hyderabad', 'Sukkur', 'Larkana', // Sindh
        'Peshawar', 'Mardan', 'Swat', 'Abbottabad', 'Dera Ismail Khan', // KPK
        'Quetta', 'Khuzdar', 'Turbat (Kech)', 'Gwadar', 'Lasbela', // Balochistan
        'Islamabad' // ICT
        // ... Add many more districts
    ];

    // --- NEW: Postal Code format ---
    protected static $postcodeFormats = ['#####']; // Standard 5-digit Pakistan postcode

    // --- NEW: House/Plot Number formats ---
    protected static $houseNumberFormats = [
        'House No. %##',
        'H. No. %##',
        'Plot No. %##',
        'No. %##/%L', // e.g., 123/A
        'Bungalow No. %##',
        'Flat No. %##',
        'Shop No. %##',
        '%##', // Just the number
        '%##-%L', // e.g., 15-B
    ];

    // --- NEW: Street Name examples ---
    // IMPORTANT: Add more realistic street names
    protected static $streetNames = [
        'Main', 'Link', 'Service', 'Circular', 'Grand Trunk (GT)', 'Canal Bank', 'Defence', 'Model Town', 'Iqbal', 'Jinnah', 'Fatima Jinnah', 'Shahrah-e-Faisal', 'M. A. Jinnah', 'University', 'Airport'
        // ... Add many more common street names or patterns
    ];


    // --- Existing/Updated Methods ---
    public function province() { return static::randomElement(static::$provinces); }
    public function city() { return static::randomElement(static::$cities); }
    public function streetSuffix() { return static::randomElement(static::$streetSuffixes); }
    public function village() { return static::randomElement(static::$villages); }
    public function mohallah() { return static::randomElement(static::$mohallahs); }

    // --- NEW Methods ---
    public function tehsil() { return static::randomElement(static::$tehsils); }
    public function district() { return static::randomElement(static::$districts); }
    public function postcode() { return static::numerify($this->generator->randomElement(static::$postcodeFormats)); }
    public function streetName() { return static::randomElement(static::$streetNames) . ' ' . $this->generator->randomElement(['Road', 'Street', 'Avenue', 'Lane', 'Boulevard']); } // Combine name + suffix

    // --- Improved Methods ---

    /**
     * Generates a Pakistani house/plot number.
     */
    public function houseNumber()
    {
        $format = static::randomElement(static::$houseNumberFormats);
        return $this->generator->bothify($format); // Use bothify to handle # (number) and % (non-zero number), L (letter)
    }

    /**
     * Generates a typical first line of a Pakistani address (Street level).
     * Combines house number, street name/suffix, sector/block etc.
     */
    public function streetAddress()
    {
        $format = $this->generator->randomElement([
             // Common formats - adjust probabilities or add more as needed
            '{{houseNumber}}, {{streetName}}',
            '{{houseNumber}}, {{streetName}}, %L-Block', // %L generates a random uppercase letter (A-Z)
            '{{houseNumber}}, Street No. %##',
            '{{houseNumber}}, {{streetName}}, Sector %L-%#',
            '{{houseNumber}}, {{streetName}}, Phase %#',
        ]);

        // Process the format string using the generator's format method
        return $this->generator->parse($format);
    }

     /**
      * NOTE: For a more complete "full address", it's often better to combine
      * the components (streetAddress, mohallah, village, city, tehsil, district, province, postcode)
      * in your Factory or Seeder, using optional() where appropriate, as the exact
      * format varies significantly.
      *
      * Example combination logic (put this kind of logic in your Factory):
      *
      * $line1 = $this->faker->streetAddress();
      * $line2 = $this->faker->optional(0.6)->mohallah(); // 60% chance Mohallah exists
      * $line3 = $this->faker->optional(0.3)->village(); // 30% chance Village exists (less common with Mohallah)
      * $cityTehsil = $this->faker->city() . ', ' . $this->faker->optional(0.5)->tehsil(); // 50% chance Tehsil is mentioned
      * $districtProvince = $this->faker->district() . ', ' . $this->faker->province();
      * $post = $this->faker->postcode();
      *
      * $fullAddress = implode("\n", array_filter([$line1, $line2, $line3, $cityTehsil, $districtProvince, $post]));
      */
}