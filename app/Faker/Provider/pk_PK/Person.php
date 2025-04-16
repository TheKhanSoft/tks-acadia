<?php

namespace App\Faker\Provider\pk_PK;

use Faker\Provider\Person as BasePersonProvider;

class Person extends BasePersonProvider
{
    /**
     * @var array Pakistani Male First Names
     * Add MANY more names here
     */
    protected static $firstNameMale = [
        'Kashif', 'Ahmed', 'Ali', 'Usman', 'Bilal', 'Hassan', 'Hussein', 'Omar', 'Abdullah', 'Zeeshan',
        'Faisal', 'Imran', 'Kamran', 'Naveed', 'Rashid', 'Saeed', 'Tariq', 'Yasir', 'Zahid', 'Amir',
        'Asif', 'Danish', 'Farhan', 'Haris', 'Junaid', 'Mohsin', 'Noman', 'Qasim', 'Salman', 'Waqas',
        'Adam', 'Ibrahim', 'Abu Bakr', 'Ahmed', 'Adham', 'Osama', 'Asaad', 'Osaid', 'Ashraf', 'Aktham', 'Akram', 'Amjad', 'Amin', 'Anthony', 'Anzor', 'Anas', 'Anmar', 'Anwar', 'Awas', 'Aws', 'Ayman', 'Ayham', 'Ayoub', 'Eslam', 'Ismail', 'Elias', 'Iyad', 'Ihab', 'Aban', 'Abraham', 'Atheer', 'Ihsan', 'Idris', 'Adeeb', 'Aram', 'Azad', 'Azd', 'Isaac', 'Ishaq', 'Aslan', 'Al-Baraa', 
        'Al-Bashar', 'Al-Batoush', 'Al-Harith', 'Al-Hussein', 'Al-Hamza', 'Al-Tufail', 'Al-Azm', 'Al-Laith', 'Al-Mumen Billah', 'Al-Muthanna', 'Al-Mustabid', 'Al-Mutaz', 'Al-Mutaz Billah', 'Al-Mutasim Billah', 'Al-Muntasir Billah', 'Al-Mansour', 'Al-Yaman', 'Amal', 'Amir', 'Andrew', 'Ausam', 'Ohan', 'Owais', 'Iyad Al-Din', 'Aisar', 'Ilia',
        'Bajes', 'Basel', 'Basem', 'Bandy', 'Baher', 'Badr', 'Badwan', 'Baraa', 'Barnaba', 'Burhan', 'Bassam', 'Bashar', 'Bishara', 'Bishr', 'Bashir', 'Boutros', 'Bakr', 'Bilal', 'Baligh', 'Bandar', 'Bahaa', 'Bahaa El-Din', 'Peter',
        'Tamer', 'Tahseen', 'Turki', 'Charlie', 'Taqi Al-Din', 'Tawfiq', 'Taysir', 'Timur','Thaer', 'Thamer',
        'Jaber', 'Jad', 'Jaser', 'Jasim', 'Jack', 'Jabagh', 'Jabr', 'Jabrai', 'Jubair', 'Jarrah', 'Jaris', 'Jaafar', 'Jalal', 'Jalal Al-Din', 'Jamal', 'Jamza', 'Jamil', 'Jihad', 'Jawad', 'Jawdat', 'George', 'Joseph',
        'Habis', 'Hatem', 'Harith', 'Haritha', 'Hazem', 'Hazem Mohammed', 'Hafez', 'Hakim', 'Halid', 'Hamid', 'Habib', 'Huthayfa', 'Hosam', 'Husam Al-Din', 'Hassan', 'Hosni', 'Hussein', 'Hakam', 'Hikmat', 'Helmi', 'Hamada', 'Hamad', 'Hamdallah', 'Hamdan', 'Hamdi', 'Hamza', 'Hamoud', 'Hamouda', 'Hameed', 'Hanna', 'Khaled',
        'Khidr', 'Khaldoun', 'Khalaf', 'Khalifa', 'Khalil', 'Khamis', 'Khair Al-Din', 'Khairallah', 'Khairy','Dawood', 'Dawoud','Thiab', 'Theeb',
        'Raafat', 'Raouf', 'Raad', 'Raed', 'Raif', 'Rajeh', 'Raji', 'Rashid', 'Radi', 'Ragheb', 'Rafat', 'Rafe', 'Rafi', 'Rakan', 'Raman', 'Ramez', 'Rami', 'Rameen', 'Rabie', 'Raja', 'Rajai', 'Rajab', 'Raddad', 'Rizk', 'Raslan', 'Raslan Al-Din', 'Rashad', 'Rashbid', 'Rida', 'Radwan', 'Rad', 'Raghad', 'Ragheed', 'Rimah', 'Ramzi', 'Ramadan', 'Rehab', 'Rawad', 'Rouhi', 'Rosa', 'Rony', 'Riyad', 'Ryan', 'Richard', 'Raymond',
        'Zaher', 'Zahi', 'Zayed', 'Zabd', 'Zakharia', 'Zakaria', 'Zaki', 'Zimam', 'Zuhdi', 'Zuhair', 'Ziad', 'Zaid', 'Zaidan', 'Zaidoun', 'Zain', 'Zain Al-Abideen',
        'Saed', 'Saba', 'Sari', 'Salem', 'Sameh', 'Samer', 'Sami', 'Saher', 'Sadeer', 'Sarkis', 'Sarmad', 'Serri', 'Saad', 'Saadi', 'Saud', 'Saeed', 'Sufian', 'Scott', 'Salam', 'Sultan', 'Salman', 'Salim', 'Suleiman', 'Samuel', 'Samaan', 'Samih', 'Samir', 'Sinan', 'Sanad', 'Siham Al-Din', 'Sahl', 'Saham', 'Saif', 'Saif Al-Islam', 'Saif Al-Din', 'Simon',
        'Shadi', 'Charlie', 'Shafie', 'Shaker', 'Shaman', 'Shamil', 'Shaher', 'Shehada', 'Sharabeef', 'Sharahbeel', 'Sharif', 'Shukri', 'Shihab', 'Shaham', 'Shawan', 'Shawqi', 'Shawkat',
        'Sadiq', 'Safi', 'Saleh', 'Subhi', 'Sabra', 'Sabri', 'Sakher', 'Saddam', 'Sidqi', 'Safaa', 'Safwan', 'Saqr', 'Salah', 'Salah Al-Din', 'Saliba', 'Suhaib',
        'Dirar', 'Dargham', 'Diaa', 'Diaa Al-Din','Tariq', 'Talib', 'Tahir', 'Talal', 'Taha', 'Ilyas', "Muhammad", 'Yasir',
        'Adel', 'Asim', 'Atef', 'Amer', 'Ayed', 'Obada', 'Abbas', 'Abd', 'Abdul Bari', 'Abdul Hafiz', 'Abdul Hakim', 'Abdul Halim', 'Abdul Hamid', 'Abdul Hai', 'Abdul Rahman', 'Abdul Rahim', 'Abdul Razzaq', 'Abdul Salam', 'Abdul Samee', 'Abdul Aziz', 'Abdul Afou', 'Abdul Ghani', 'Abdul Fattah', 'Abdul Qadir', 'Abdul Karim', 'Abdul Latif', 'Abdullah', 'Abdul Majid', 'Abdul Mawla', 'Abdul Nasser', 'Abdul Hadi', 'Abd Rabbo',
        'Abdullah', 'Abdul Basit', 'Abdul Jalil', 'Abdul Jawad', 'Abdul Raouf', 'Abdul Moteleb', 'Abdul Muti', 'Abdul Muhaymin', 'Abdul Wahab', 'Abdo', 'Aboud', 'Obaidullah', 'Obaida', 'Oteiba', 'Othman', 'Adab', 'Adli', 'Adnan', 'Adwan', 'Oda', 'Oday', 'Arar', 'Arabi', 'Arafat', 'Arafa', 'Orman', 'Orwa', 'Areeq', 'Areen', 'Izz Al-Din', 'Azzam', 'Izzat', 'Azmi', 'Aziz', 'Essam', 'Esmat', 'Ata', 'Atallah', 'Atiyah', 'Aql', 
        'Alaa', 'Alaa Al-Din', 'Ali', 'Olayan', 'Imad', 'Imad Al-Din', 'Ammar', 'Omar', 'Omar Osama', 'Imran', 'Amr', 'Amla', 'Amid', 'Inad', 'Awad', 'Odeh', 'Awad', 'Auf', 'Aoun', 'Awni', 'Eid', 'Eid Allah', 'Issa',
        'Ghazi', 'Ghaleb', 'Ghanem', 'Ghadeer', 'Ghassan', 'Ghaith', 'Kashif', 'Rayyan', 'Rayan', 'Samee', 'Sami', 'Rayaan', 'Rayyaan', 'Faheem', 'Fahim', 'Mukhtiar', 'Mukhtar',
        'Fouad', 'Foas', 'Faiq', 'Fakher', 'Fadi', 'Faris', 'Farouk', 'Fadel', 'Fayez', 'Fathi', 'Fajr', 'Fakhry', 'Firas', 'Farah', 'Fareed', 'Victor', 'Falah', 'Fendi', 'Fahd', 'Fahmi', 'Fawaz', 'Fawzi', 'Faisal', 'Philip',
        'Qasem', 'Qablan', 'Qatada', 'Qutaiba', 'Qusay', 'Qais','Castro', 'Kazem', 'Kamel', 'Kayed', 'Karam', 'Kareem', 'Kifah', 'Kamal', 'Kinan', 'Loay', 'Labib', 'Lutf', 'Lutfi', 'Lawrence', 'Louis', 'Laith', 'Laith Al-Din', 'Lillian',
        'Mamoun', 'Mutaman', 'Muather', 'Moamen', 'Moanes', 'Moayad', 'Majid', 'Martin', 'Marcel', 'Mazen', 'Malik', 'Maher', 'Mubarak', 'Muthanna', 'Mujahid', 'Majd', 'Majdi', 'Mojamad', 'Mohsen', 'Mohammed', 'Mahmoud', 'Mohye', 'Mohye Al-Din', 'Mokhtar', 'Mukhlis', 'Midhat', 'Medyan', 'Murad', 'Murshid', 'Murhaf', 'Marwan', 'Masad', 'Masoud', 'Muslim', 'Mishari', 'Mishal', 'Mashhour', 'Michel', 'Misbah', 'Mustaghfa', 'Mustafa', 
        'Musab', 'Mudar', 'Mutee', 'Muzaffar', 'Muzher', 'Muad', 'Muath', 'Muawiya', 'Mutaz', 'Mutasim', 'Muammar', 'Maan', 'Mutansim', 'Muawiya', 'Muin', 'Mufdi', 'Muflih', 'Miqdad', 'Makeen', 'Mulham', 'Mamdouh', 'Manaf', 'Muntasir', 'Manh', 'Munther', 'Munsif', 'Mansour', 'Munqith', 'Munir', 'Muhab', 'Mahdi', 'Mahran', 'Muhannad', 'Musa', 'Muwaffaq', 'Mias', 'Maysam', 'Michel', 'Milad', 'Mina',
        'Nael', 'Naji', 'Nader', 'Nart', 'Nasser', 'Nahed', 'Naif', 'Nibras', 'Nabil', 'Natant', 'Najati', 'Najeeb', 'Nadeem', 'Nizar', 'Nazzal', 'Nazih', 'Naseem', 'Nashat', 'Nassar', 'Nasr', 'Nasri', 'Nasouh', 'Nidal', 'Nizam', 'Numan', 'Nima', 'Naim', 'Niqola', 'Nimer', 'Nihad', 'Nahar', 'Nawaf', 'Noor', 'Noor Al-Din', 'Nawras', 'Nofan',
        'Hadi', 'Haroun', 'Hashim', 'Hakan', 'Hani', 'Hathal', 'Husham', 'Hilal', 'Hammam', 'Hemler', 'Hanaa', 'Haitham',
        'Wael', 'Wathiq', 'Wasef', 'Wagdi', 'Wajih', 'Waheed', 'Wadee', 'Ward', 'Wesam', 'Wesam Al-Din', 'Wasan', 'Waseem', 'Wasfi', 'Wadah', 'Waad', 'Wafaa', 'Waleed', 'Waheeb',
        'Yasser', 'Yaseen', 'Yamen', 'Yahya', 'Yazan', 'Yazeed', 'Yasar', 'Yashar', 'Yarub', 'Yacoub', 'Yaman', 'Yanal', 'Yousef', 'Younis'
    ];

    /**
     * @var array Pakistani Female First Names
     * Add MANY more names here
     */
    protected static $firstNameFemale = [
        'Fatima', 'Ayesha', 'Zainab', 'Hina', 'Sadia', 'Sana', 'Amna', 'Rabia', 'Maria', 'Saba', 'Aisha', 'Farah', 'Hira', 'Iqra', 'Kiran', 'Madiha', 'Nida', 'Noreen', 'Saima',
        'Shazia', 'Sumaira', 'Tayyaba', 'Uzma', 'Zobia', 'Alina', 'Bushra', 'Erum', 'Jaweria', 'Mehwish', 'Sidra', 'Maryam', 'Sana', 'Noor', 'Mahnoor', 'Sadaf', 'Khadija', 'Rabia', 
        'Samina', 'Nadia', 'Yasmin', 'Yasmeen', 'Farzana', 'Shumaila', 'Nida', 'Sehrish', 'Kiran', 'Fariha', 'Uzma', 'Shazia', 'Humaira', 'Humera', 'Gulnaz', 'Parveen', 'Samreen', 
        'Huda', 'Wajiha', 'Saima', 'Rubina', 'Mehwish', 'Sidra', 'Saira', 'Anila', 'Nazia', 'Erum', 'Irum', 'Zoya', 'Kainat', 'Bisma', 'Rimsha', 'Komal', 'Naila', 'Asma', 'Aafia', 'Aafreen', 'Aalia',
        'Aliya', 'Aamina', 'Amina', 'Aamira', 'Amira', 'Abeera', 'Abida', 'Adeela', 'Adila', 'Afshan', 'Afsheen', 'Aila', 'Aimen', 'Aiman', 'Aiza', 'Aleena', 'Alishba', 'Aliza', 'Almas',
        'Ammara', 'Amreen', 'Anam', 'Anaya', 'Aneesa', 'Anisa', 'Anmol', 'Anoosha', 'Anum', 'Anusha', 'Aqsa', 'Areeba', 'Areej', 'Arfa', 'Arisha', 'Arooj', 'Arshia', 'Atia',  'Atiya', 'Attiya',
        'Ayra', 'Azka', 'Azra', 'Bareera', 'Beenish', 'Benazir', 'Bilqis', 'Dua', 'Eshaal', 'Faiza', 'Farheen', 'Fariyal', 'Fauzia', 'Fawzia', 'Fazeela', 'Fehmida', 'Feroza', 'Fiza', 'Ghazala', 
        'Hafsa', 'Hajra', 'Haleema', 'Halima', 'Hania', 'Hareem', 'Hifza', 'Hooriya', 'Hooria', 'Huma', 'Husna', 'Iffat', 'Ilham', 'Iman',  'Emaan', 'Inaya', 'Insha', 'Iram', 'Ishrat',
        'Isra', 'Jahanara', 'Jamila', 'Jameela', 'Kaneez', 'Kanwal', 'Kashaf', 'Kashmala', 'Khansa', 'Khulood', 'Kinza', 'Laiba', 'Laila', 'Layla', 'Lamia', 'Laraib', 'Lubna', 'Madeeha', 'Maham',
        'Maheen', 'Mahira', 'Mahjabeen', 'Maira', 'Maliha', 'Manahil', 'Manal', 'Madiha', 'Marwa', 'Mashal', 'Mishal', 'Masooma', 'Mavisha', 'Mawra', 'Mehak',

        'Athar', 'Alaa', 'Aya', 'Ayah', 'Abrar', 'Ahlam', 'Arwa', 'Areej', 'Asma', 'Aseel', 'Asalah', 'Afnan', 'Amani', 'Amal', 'Amira', 'Ansam', 'Anwar', 'Ekhlas', 'Esraa', 'Ekram', 'Enaam', 'Eman', 'Enas', 'Ebtihaj', 'Ebtihal', 'Azhar', 'Esrar', 'Eshraq', 'Afrah', 'Elham', 'Amal', 'Amnah', 'Amina', 
        'Batool', 'Buthaina', 'Basma', 'Bashayer', 'Bushra', 'Tala', 'Tala', 'Tasneem', 'Taghreed', 'Taqwa', 'Tuqa', 'Tamara', 'Tahani', 'Thuraya', 'Jameela', 'Jana', 'Jihad', 'Habiba', 'Hasna', 'Hala', 'Halima', 'Hanan', 'Haneen', 'Hayat',
        'Khadija', 'Kholoud', 'Dareen', 'Dalia', 'Dana', 'Danah', 'Dania', 'Doaa', 'Dalal', 'Dunya', 'Diana', 'Deema', 'Dina', 'Rua', 'Rama', 'Rana', 'Rania', 'Rawiya', 'Raya', 'Ruba', 'Rabab', 'Ruba', 'Rajaa', 'Rahma', 'Rahmah', 'Razan',
        'Rasha', 'Raghad', 'Raghda', 'Ruqayya', 'Raneem', 'Raneen', 'Rahaf', 'Rawan', 'Rola', 'Ruwaida', 'Rayan', 'Rita', 'Reem', 'Reema', 'Renad', 'Reham', 'Zakiya', 'Zahra', 'Zain', 'Zeena',
        'Zeenat', 'Zainab', 'Zeena', 'Sajida', 'Sara', 'Saja', 'Sahar', 'Suad', 'Sakina', 'Salsabeel', 'Salma', 'Salwa', 'Sama', 'Samah', 'Samara', 'Samar', 'Sumaya', 'Samira', 'Sana', 'Sundus', 'Siham', 'Sahar', 'Suha', 'Suhaila', 'Susan', 'Sawsan',
        'Sireen', 'Serena', 'Celine', 'Seema', 'Shatha', 'Shurooq', 'Sharifa', 'Shereen', 'Shareehan', 'Shifa', 'Shahad', 'Shaima', 'Sabreen', 'Saba', 'Sabah', 'Sabreen', 'Safa', 'Safaa', 'Safiya', 'Duha', 'Diaa', 'Aisha', 'Alia', 'Alya', 'Abla', 'Abeer', 'Azza', 'Aziza', 'Afaf', 'Ola', 'Aliyaa',
        'Ahad', 'Ghada', 'Ghadeer', 'Gharam', 'Ghazal', 'Ghaida', 'Faten', 'Fadia', 'Fatima', 'Faiza', 'Fathiya', 'Fadwa', 'Fida', 'Farah', 'Feryal', 'Farida', 'Fawzia', 'Fairouz', 'Vivian', 'Qamar', 'Lara', 'Lana', 'Lubna', 'Latifa', 'Lama',
        'Lamees', 'Lina', 'Laura', 'Lorina', 'Luna', 'Layan', 'Lida', 'Layla', 'Lillian', 'Leen', 'Lina', 'Leena', 'Linda', 'Maya', 'Majdoulin', 'Madiha', 'Maram', 'Marwa', 'Maryam', 'Miriam', 'Mosheera', 'Maali', 'Malak', 'Malek', 'Manar', 'Manal', 'Muna', 'Maha', 'May',
        'Mayada', 'Miyar', 'Mayan', 'Mira', 'Miral', 'Miran', 'Mervat', 'Mays', 'Maysaa', 'Maysara', 'Nadia', 'Nadine', 'Nancy', 'Nabila', 'Najat', 'Najlaa', 'Najwa', 'Nida', 'Nada',
        'Nermeen', 'Nesreen', 'Naseema', 'Nemat', 'Nemah', 'Nihad', 'Nuha', 'Nawal', 'Noor', 'Noura', 'Nouran', 'Nairouz', 'Neveen', 'Hadleen', 'Hala', 'Hania', 'Heidi', 'Heba', 'Hidaya', 'Huda', 'Hadeel', 'Hana', 'Hanaa', 'Hanadi', 'Hind', 'Haya', 'Haifa',
        'Haifaa', 'Helen', 'Weam', 'Wijdan', 'Widad', 'Wurood', 'Wesam', 'Waseem', 'Waad', 'Wafaa', 'Walaa', 'Yara', 'Yasmin', 'Yusra',
        'Anaa', 'Antoinette', 'Ayat', 'Ebaa', 'Islam', 'Arjwan', 'Eshar', 'Asra', 'Asmahan', 'Asma', 'Etidal', 'Afia', 'Al-Anoud', 'Elian', 'Elizabeth', 'Amanda', 'Amelia', 'Anahid', 'Entezar', 'Angelica', 'Awais', 'Ayam', 'Eva', 'Evan', 'Yvonne',
        'Basima', 'Pamela', 'Ban', 'Bana', 'Buthaina', 'Budoor', 'Baraa', 'Baraa', 'Bardis', 'Parween', 'Balsam', 'Belqis', 'Banan', 'Bahja', 'Bia', 'Bayan', 'Baydaa', 'Bissan',
        'Talin', 'Tamer', 'Tania Maria', 'Tahreer', 'Tamader', 'Tamam', 'Touleen',
        'Jasmine', 'Hessa', 'Khitam', 'Khawla',
        'Dalia', 'Dana Christel Jameela', 'Danielle', 'Dabna', 'Daad', 'Dola', 'Diala', 'Diane', 'Roya', 'Rabia', 'Raghida', 'Randy', 'Radeena', 'Refaiya', 'Rafah', 'Ruqayyah', 'Ramla', 'Ranad', 'Rand', 'Reham', 'Rehab', 'Rawaa', 'Robina', 'Rotana', 'Rouhia', 'Roda', 'Rozan', 'Rosanna', 'Rozeen', 'Reward', 'Raidan', 'Reeman', 'Renata',
        'Zaman', 'Zaha', 'Sarin', 'Sally', 'Sandra', 'Sandy', 'Sabata', 'Sedeen', 'Sura', 'Sereen', 'Salam', 'Sanabel', 'Sunan', 'Suhad', 'Sewar', 'Suzanna', 'Sylva',
        'Shada', 'Shatha', 'Shahinaz', 'Sabrin', 'Saleh', 'Sabahat', 'Sahar', 'Somoud',
        'Aisha', 'Adla', 'Areen', 'Anoud', 'Ghosoun', 'Ghufran', 'Ghina', 'Gheed', 'Ghaida', 'Ghaidaa', 'Guevara',
        'Fadiya', 'Fetna', 'Fidaa', 'Leena',
        'Madeline', 'Mary', 'Maria', 'Marian', 'Marina', 'Majd', 'Mahbooba', 'Marah', 'Mariana', 'Masada', 'Muhtab', 'Meerat', 'Misa', 'Maysar', 'Maysam', 'Maysoon', 'Meelaa', 'Minas',
        'Naila', 'Natasha', 'Natalie', 'Nardeen', 'Nariman', 'Nibal', 'Nibras', 'Najah', 'Najwan', 'Najoud', 'Nadeen', 'Nazmia', 'Nahida', 'Nour Al-Huda',
        'Hazar', 'Hazal', 'Hanada', 'Yaman', 'Yafa'
    ];

    /**
     * @var array Pakistani Last Names
     * Add MANY more names here
     */
    protected static $lastName = [
        'Abbasi','Afridi','Ahmed','Akhtar','Ali','Ansar','Ansari','Anwar','Awan','Baig','Bajwa','Bhatti','Bhutto','Bukhari','Butt','Chaudhry','Cheema',
        'Dar','Durrani','Farooq','Gill','Gillani','Gondal','Gujjar','Hussain','Iqbal','Jamali','Janjua','Javed','Jutt','Kayani','Kazmi','Khan','Khattak',
        'Khosa','Leghari','Lodhi','Mahar','Malik','Mehmood','Memon','Mirza','Mughal','Nawaz','Niazi','Paracha','Qazi','Qureshi','Raja','Rajput','Rana',
        'Rehman','Rizvi','Saleem','Shah','Sheikh','Siddiqui','Syed','Tarin','Tiwana','Wattoo','Wazir','Yousufzai','Zafar','Zardari',
    ];

    /**
     * @var array Pakistani Male Titles
     */
    protected static $titleMale = ['Mr.', 'Sahib', 'Dr.', 'Engr.', 'Professor', 'Syed', 'Hafiz', 'Moulvi', 'Moulana', 'Maulana', 'Haji', 'Sheikh', 'Ustad', 'Sardar', 'Chaudhry', 'Qari', 'Pir', 'Mian', 'Baba', 'Bhai'];

    /**
     * @var array Pakistani Female Titles
     */
    protected static $titleFemale = ['Mrs.', 'Miss', 'Ms.', 'Dr.', 'Professor', 'Begum', 'Mohtarma', 'Sahiba', 'Syeda', 'Hafiza', 'Ustadni', 'Sardarni', 'Chaudhrayan', 'Qaria', 'Pir', 'Mian', 'Bibi'];


    /**
     * @param string|null $gender 'male'|'female'|null
     * @return string
     */
    public function firstName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::firstNameMale();
        }
        if ($gender === static::GENDER_FEMALE) {
            return static::firstNameFemale();
        }

        return $this->generator->parse(static::randomElement(array_merge(static::$firstNameMale, static::$firstNameFemale)));
    }

    /**
     * @return string
     */
    public static function firstNameMale()
    {
        return static::randomElement(static::$firstNameMale);
    }

    /**
     * @return string
     */
    public static function firstNameFemale()
    {
        return static::randomElement(static::$firstNameFemale);
    }

    /**
     * @return string
     */
    public function lastName()
    {
        return static::randomElement(static::$lastName);
    }

     /**
     * @param string|null $gender 'male'|'female'|null
     * @return string
     */
    public function title($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::titleMale();
        }
        if ($gender === static::GENDER_FEMALE) {
            return static::titleFemale();
        }

        return $this->generator->parse(static::randomElement(array_merge(static::$titleMale, static::$titleFemale)));
    }

    /**
      * @return string
      */
    public static function titleMale()
    {
        return static::randomElement(static::$titleMale);
    }

    /**
      * @return string
      */
    public static function titleFemale()
    {
        return static::randomElement(static::$titleFemale);
    }

    /**
     * Replace default name formats with Pakistani ones
     * @var array
     */
     protected static $formats = [
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstName}} bin {{lastName}}', // Less common now but possible
     ];

    // Override name method if needed for more complex logic,
    // but often relying on BasePersonProvider's handling of $formats is sufficient.
    // public function name($gender = null)
    // {
    //    // Custom logic if needed
    // }
}