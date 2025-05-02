<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->truncate(); // Clear the table first

        $paymentMethods = [
            ['name' => 'Bank Transfer', 'code'=> 'BANK', 'description' => 'Payment made by transferring funds directly from the university account to the employee bank account.'],
            ['name' => 'Cheque', 'code'=> 'CHEQ', 'description' => 'Payment issued by the university via personal or business cheque.'],
            ['name' => 'Cash', 'code'=> 'CASH', 'description' => 'Payment made in cash at the university finance office.'],
            ['name' => 'Online Payment Gateway', 'code'=> 'ONLINE', 'description' => 'Payment processed through the university\'s secure online payment portal.'],
            ['name' => 'Mobile Wallet', 'code'=> 'MOBILE', 'description' => 'Payment made using university-approved digital wallets like Apple Pay, Google Pay, or other mobile payment solutions.'],
            ['name' => 'Direct Deposit', 'code'=> 'DD', 'description' => 'Payment deposited directly to recipient\'s account from university funds.'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
