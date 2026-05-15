<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        
        // Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleHR = Role::firstOrCreate(['name' => 'hr']);
        $roleManager = Role::firstOrCreate(['name' => 'manager']);
        $roleEmployee = Role::firstOrCreate(['name' => 'employee']);

        // Departments
        $deptHR = Department::firstOrCreate(['code' => 'HR'], ['name' => 'Human Resources', 'is_active' => true]);
        $deptEng = Department::firstOrCreate(['code' => 'ENG'], ['name' => 'Engineering', 'is_active' => true]);
        $deptSales = Department::firstOrCreate(['code' => 'SALES'], ['name' => 'Sales', 'is_active' => true]);
        $deptDesign = Department::firstOrCreate(['code' => 'DESIGN'], ['name' => 'Designing', 'is_active' => true]);
        
        // Designations
        $desigHRM = Designation::firstOrCreate(['code' => 'HRM'], ['name' => 'HR Manager', 'department_id' => $deptHR->id]);
        
        $desigEngMgr = Designation::firstOrCreate(['code' => 'EM1'], ['name' => 'Engineering Manager', 'department_id' => $deptEng->id]);
        $desigEngEmp = Designation::firstOrCreate(['code' => 'SDE1'], ['name' => 'Software Engineer', 'department_id' => $deptEng->id]);
        
        $desigSalesMgr = Designation::firstOrCreate(['code' => 'SM1'], ['name' => 'Sales Manager', 'department_id' => $deptSales->id]);
        $desigSalesEmp = Designation::firstOrCreate(['code' => 'SE1'], ['name' => 'Sales Executive', 'department_id' => $deptSales->id]);
        
        $desigDesignMgr = Designation::firstOrCreate(['code' => 'DM1'], ['name' => 'Design Manager', 'department_id' => $deptDesign->id]);
        $desigDesignEmp = Designation::firstOrCreate(['code' => 'DS1'], ['name' => 'UI/UX Designer', 'department_id' => $deptDesign->id]);

        $shift = Shift::firstOrCreate(['code' => 'GEN'], ['name' => 'General', 'start_time' => '09:00', 'end_time' => '18:00'])->id;

        // Admin (Avneet)
        if(!User::where('email', 'avneet@gmail.com')->exists()) {
            $admin = User::create([
                'name' => 'Avneet',
                'email' => 'avneet@gmail.com',
                'password' => Hash::make('password'),
                'employee_id' => 'EMP0001',
                'role' => 'admin',
                'department_id' => $deptHR->id,
                'designation_id' => $desigHRM->id,
                'shift_id' => $shift,
                'date_of_joining' => '2023-01-01',
                'date_of_birth' => now()->format('Y-m-d')
            ]);
            $admin->assignRole($roleAdmin);
            EmployeeProfile::create(['user_id' => $admin->id]);
        }

        // Dedicated HR User
        if(!User::where('email', 'hr@gmail.com')->exists()) {
            $hrUser = User::create([
                'name' => 'HR Rep',
                'email' => 'hr@gmail.com',
                'password' => Hash::make('password'),
                'employee_id' => 'HR0001',
                'role' => 'hr',
                'department_id' => $deptHR->id,
                'designation_id' => $desigHRM->id,
                'shift_id' => $shift,
                'date_of_joining' => '2023-01-10',
                'date_of_birth' => now()->subYears(28)->format('Y-m-d')
            ]);
            $hrUser->assignRole($roleHR);
            EmployeeProfile::create(['user_id' => $hrUser->id]);
        }

        // Managers and Employees will be manually created via HR Dashboard or Self-Registration
    }
}
