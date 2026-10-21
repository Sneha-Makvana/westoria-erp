<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class DummyPayrollDataSeeder extends Seeder
{
    public function run()
    {
        $branchId = 1; // Assuming branch 1 exists

        // 1. Create a Department
        $departmentId = DB::table('department')->insertGetId([
            'department_name' => 'Sales Dept ' . rand(100, 999),
            'enable_overtime' => 1,
            'overtime_multiplier' => 1.5,
            'overtime_rate_type' => 'multiplier',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Create a Designation with Incentive
        $designationId = DB::table('designation')->insertGetId([
            'department_id' => $departmentId,
            'designation_name' => 'Senior Sales Executive ' . rand(100, 999),
            'incentive_percentage' => 5.00, // 5% incentive
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Create a Staff User
        $user = User::create([
            'name' => 'Dummy Sales Staff ' . rand(100, 999),
            'email' => 'salesstaff' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'branch_id' => $branchId,
            'email_verified_at' => now()
        ]);

        // 4. Create User Details
        DB::table('user_details')->insert([
            'user_id' => $user->id,
            'department_id' => $departmentId,
            'designation_id' => $designationId,
            'salary' => 50000, // Base salary 50k
            'joining_date' => now()->subMonths(6)->toDateString(),
            'shift_time' => '10:00 AM - 07:00 PM',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 5. Add Attendance for last month
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();
        
        $attendanceData = [];
        for ($date = $startOfLastMonth->copy(); $date->lte($endOfLastMonth); $date->addDay()) {
            // Skip Sundays
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }
            
            $attendanceData[] = [
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'status' => 'present',
                'check_in_time' => '10:00:00',
                'check_out_time' => '19:00:00',
                'work_hours' => '09:00:00',
                'overtime_hours' => 0.00,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('attendances')->insert($attendanceData);

        // 6. Add Orders (Sales) for last month in the same branch
        // Let's create a few big orders so the incentive is clearly visible
        $ordersData = [];
        for ($i = 1; $i <= 5; $i++) {
            $ordersData[] = [
                'branch_id' => $branchId,
                'order_number' => 'DUMMY-ORD-' . rand(1000, 9999) . '-' . $i,
                'user_id' => 1, // Assume user 1 exists (usually admin)
                'total_amount' => 100000, // 1 Lakh per order -> Total 5 Lakhs
                'quotation_status' => 'sales',
                'isDeleted' => 0,
                'created_at' => $startOfLastMonth->copy()->addDays(rand(1, 25)),
                'updated_at' => now()
            ];
        }
        DB::table('orders')->insert($ordersData);
        
        $this->command->info('Dummy data created successfully!');
        $this->command->info('User Email: ' . $user->email);
        $this->command->info('Total Sales for Branch ' . $branchId . ' last month: 5,00,000');
        $this->command->info('Expected Incentive (5%): 25,000');
    }
}
