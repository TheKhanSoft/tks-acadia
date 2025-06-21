<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Assuming you have a User model

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Resource-Action Map for Permissions
        // Standard actions: view, create, edit, delete, manage
        $resourcePermissionsMap = [
            // Core Entities
            'users' => ['view', 'create', 'edit', 'delete', 'manage', 'assign roles to', 'view activity logs', 'manage preferences', 'export data'],
            'roles' => ['view', 'create', 'edit', 'delete', 'manage', 'assign permissions to'],
            'settings' => ['view', 'edit', 'manage'],

            // Academic Structure
            'academic sessions' => ['view', 'create', 'edit', 'delete', 'manage'],
            'programs' => ['view', 'create', 'edit', 'delete', 'manage', 'archive', 'assign to department'],
            'subjects' => ['view', 'create', 'edit', 'delete', 'manage', 'assign to program', 'assign to department', 'assign to faculty'],
            'courses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'departments' => ['view', 'create', 'edit', 'delete', 'manage'],
            'faculties' => ['view', 'create', 'edit', 'delete', 'manage'],
            'learning outcomes' => ['view', 'create', 'edit', 'delete', 'manage', 'assign to subject'],
            'degree levels' => ['view', 'create', 'edit', 'delete', 'manage'],
            'accreditation types' => ['view', 'create', 'edit', 'delete', 'manage'],
            'subject types' => ['view', 'create', 'edit', 'delete', 'manage'],
            'curriculum' => ['view', 'manage', 'approve changes', 'propose changes'],
            'grading schemes' => ['view', 'manage', 'create', 'edit', 'delete'],
            'academic calendars' => ['view', 'manage', 'publish', 'create events'],

            // Student Lifecycle
            'students' => ['view', 'create', 'edit', 'delete', 'manage', 'import data', 'export data'],
            'student program enrollments' => ['view', 'create', 'edit', 'delete', 'manage'],
            'student session enrollments' => ['view', 'create', 'edit', 'delete', 'manage'],
            'student statuses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'enrollment statuses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'program enrollment statuses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'student grades' => ['view', 'enter', 'edit', 'approve', 'publish', 'manage'],
            'student attendance' => ['view', 'record', 'edit', 'manage', 'export records'],
            'student documents' => ['view', 'upload', 'edit', 'delete', 'manage', 'approve'],
            'student transcripts' => ['issue', 'view', 'generate', 'manage'],
            'student disciplinary actions' => ['view', 'record', 'edit', 'manage'],
            'alumni records' => ['view', 'manage', 'contact', 'update details'],

            // Employee & Staff
            'employees' => ['view', 'create', 'edit', 'delete', 'manage', 'import data', 'export data'],
            'employee types' => ['view', 'create', 'edit', 'delete', 'manage'],
            'job natures' => ['view', 'create', 'edit', 'delete', 'manage'],
            'employee work statuses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'designations' => ['view', 'create', 'edit', 'delete', 'manage'],
            'employee offices' => ['view', 'manage', 'assign employee to office', 'remove employee from office'],
            'employee payroll' => ['view', 'manage', 'process', 'generate payslips'],
            'employee leave records' => ['view', 'manage'],
            'employee leave requests' => ['manage', 'approve', 'view'],
            'employee performance reviews' => ['view', 'manage', 'conduct'],
            'employee documents' => ['view', 'upload', 'edit', 'delete', 'manage', 'approve'],
            'staff attendance' => ['view', 'record', 'edit', 'manage', 'export records'],

            // Campus & Office Infrastructure
            'campuses' => ['view', 'create', 'edit', 'delete', 'manage'],
            'offices' => ['view', 'create', 'edit', 'delete', 'manage'],
            'office types' => ['view', 'create', 'edit', 'delete', 'manage'],
            'campus facilities' => ['view', 'manage', 'book', 'manage bookings'],
            'office equipment' => ['view', 'manage', 'assign', 'track'],

            // Financial Management
            'payment methods' => ['view', 'create', 'edit', 'delete', 'manage'],
            'financial records' => ['view', 'manage'],
            'fees' => ['view', 'manage', 'create', 'edit', 'delete'],
            'invoices' => ['view', 'generate', 'edit', 'delete', 'cancel', 'manage'],
            'fee structures' => ['view', 'manage', 'create', 'edit', 'delete', 'apply discounts to'],
            'scholarships' => ['view', 'manage', 'award', 'create types', 'edit types', 'delete types'],
            'financial aid' => ['view', 'manage', 'approve applications', 'disburse', 'create types', 'edit types', 'delete types'],
            'financial statements' => ['view', 'generate', 'manage'],
            'financial reports' => ['view', 'export data', 'manage'],
            'chart of accounts' => ['view', 'create', 'edit', 'delete', 'manage'],

            // System Settings & Configuration
            'system settings' => ['view', 'manage', 'edit general', 'edit academic', 'edit financial'],
            'email templates' => ['view', 'manage', 'create', 'edit', 'delete'],
            'system integrations' => ['view', 'manage', 'configure'],
            'system logs' => ['view', 'manage', 'clear', 'archive'],
            'application lookups' => ['view', 'manage', 'create', 'edit', 'delete'],
            'date types' => ['view', 'create', 'edit', 'delete', 'manage'],
            'payment gateways' => ['view', 'manage', 'configure', 'manage settings'],

            // Location Data Management
            'countries' => ['view', 'create', 'edit', 'delete', 'manage'],
            'states' => ['view', 'create', 'edit', 'delete', 'manage'],
            'cities' => ['view', 'create', 'edit', 'delete', 'manage'],

            // Content Management (CMS-like)
            'site content' => ['view', 'manage', 'create', 'edit', 'delete', 'publish', 'approve'],
            'FAQs' => ['view', 'manage', 'create', 'edit', 'delete'],
            'news articles' => ['view', 'manage', 'create', 'edit', 'delete', 'publish'],
            'events' => ['view', 'manage', 'create', 'edit', 'delete', 'publish', 'manage registrations'],

            // Reports & Analytics
            'system reports' => ['view', 'generate', 'customize', 'schedule', 'export', 'manage'],

            // Communication & Notifications
            'system announcements' => ['view', 'manage', 'create', 'edit', 'delete'],
            'communication templates' => ['view', 'manage', 'create', 'edit', 'delete'],

            // Library Management
            'library resources' => ['view', 'manage', 'add', 'edit', 'delete', 'import'],
            'library members' => ['view', 'manage', 'register', 'edit', 'delete'],
            'library fines' => ['view', 'manage', 'collect', 'waive', 'edit'],
            'book categories' => ['view', 'manage', 'create', 'edit', 'delete'],

            // Admissions Management
            'admission applications' => ['view', 'manage', 'review', 'approve', 'reject', 'send offers', 'edit'],
            'admission criteria' => ['view', 'manage', 'configure', 'create', 'edit', 'delete'],
            'admission statistics' => ['view', 'generate', 'manage'], // Added 'manage'
            'admission cycles' => ['view', 'manage', 'create', 'edit', 'delete'],

            // Timetable Management
            'timetables' => ['view', 'manage', 'create', 'edit', 'delete', 'publish', 'resolve clashes'],

            // Examinations Management
            'examinations' => ['view', 'manage', 'schedule', 'publish results', 'edit schedule'],
            'exam invigilators' => ['view', 'manage', 'assign to exams', 'create', 'edit', 'delete'],
            'exam venues' => ['view', 'manage', 'book for exams', 'create', 'edit', 'delete'],
            'exam marks' => ['view', 'enter', 'edit', 'approve', 'manage'],
            'exam results' => ['view', 'manage'],
            'exam rechecks' => ['view', 'process', 'manage', 'request', 'approve requests'],

            // Model Relationships Management
            'department program assignments' => ['view', 'manage', 'assign', 'remove'],
            'department subject assignments' => ['view', 'manage', 'assign', 'remove'],
            'program subject assignments' => ['view', 'manage', 'assign', 'remove'],
            'subject learning outcome assignments' => ['view', 'manage', 'assign', 'remove'],
            'session offerings' => ['view', 'manage', 'create', 'edit', 'delete'],

            // Audit Trails
            'audit trails' => ['view', 'export', 'manage'],

            // Feedback & Surveys
            'surveys' => ['view', 'manage', 'create', 'edit', 'delete', 'distribute', 'view results', 'analyze data'],
            'submitted feedback' => ['view', 'manage', 'archive', 'delete'],

            // API Access
            'api tokens' => ['view', 'manage', 'generate', 'revoke', 'edit'],
        ];

        // Create permissions from the map
        foreach ($resourcePermissionsMap as $resource => $actions) {
            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $resource;
                // Handle specific naming conventions for clarity if the general pattern isn't ideal
                if ($resource === 'employee offices' && $action === 'assign employee to office') {
                    $permissionName = 'assign employee to office'; // Already specific
                } elseif ($resource === 'employee offices' && $action === 'remove employee from office') {
                    $permissionName = 'remove employee from office'; // Already specific
                } elseif ($resource === 'learning outcomes' && $action === 'assign to subject') {
                     $permissionName = 'assign learning outcome to subject';
                } elseif ($resource === 'subjects' && $action === 'assign to program') {
                     $permissionName = 'assign subject to program';
                } elseif ($resource === 'subjects' && $action === 'assign to department') {
                     $permissionName = 'assign subject to department';
                } elseif ($resource === 'subjects' && $action === 'assign to faculty') {
                     $permissionName = 'assign subject to faculty';
                } elseif ($resource === 'programs' && $action === 'assign to department') {
                     $permissionName = 'assign program to department';
                } elseif ($resource === 'exam invigilators' && $action === 'assign to exams') {
                     $permissionName = 'assign exam invigilators to exams';
                } elseif ($resource === 'exam venues' && $action === 'book for exams') {
                     $permissionName = 'book exam venues for exams';
                } elseif (str_ends_with($resource, 'assignments') && ($action === 'assign' || $action === 'remove')) {
                    $baseResource = str_replace(' assignments', '', $resource);
                    $permissionName = $action . ' ' . $baseResource;
                }
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // Define Unique Permissions
        $uniquePermissions = [
            'access admin dashboard', 'access faculty dashboard', 'access student dashboard', 'access registrar dashboard', 'access accountant dashboard', 'access librarian dashboard', 'access hr dashboard', 'access it support dashboard',
            'view own profile', 'edit own profile', 'change own password',
            'impersonate users',
            'view own academic record', 'view own courses', 'view own grades', 'view own attendance', 'view own documents', 'request own transcript', 'view own enrollment status',
            'view own payslip', 'apply for leave', 'view own leave balance', 'view own performance review',
            'submit self-assessment',
            'process payments',
            'backup database', 'restore database from backup',
            'view reports dashboard',
            'register for events', 'submit feedback',
            'send system notifications',
            'send bulk emails', 'view email delivery history',
            'send SMS notifications', 'view SMS delivery history',
            'borrow library items', 'return library items', 'view library borrowing history', 'manage library reservations',
            'view own timetable', 'generate faculty timetables', 'generate student timetables',
            'view examination schedule',
            'use api endpoints',
            'enable maintenance mode', 'disable maintenance mode', 'view maintenance mode status',
            'configure attendance settings',
            'publish results',
            'apply for scholarship', // Student action, ensure it's here if not in map
        ];

        foreach ($uniquePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Ensure all permissions intended for roles are created.
        // This section can be used to double-check or add any missed complex names.
        // Most should be covered by the loops above.
        $allRolePermissions = [];
        $adminPermissionsList = [ // Extracted for re-use and clarity
            'access admin dashboard',
            'manage users', 'manage roles', 'manage settings',
            'manage academic sessions', 'manage programs', 'manage subjects', 'manage courses', 'manage departments', 'manage faculties',
            'manage learning outcomes', 'manage degree levels', 'manage accreditation types', 'manage subject types',
            'manage curriculum', 'manage grading schemes', 'manage academic calendars',
            'manage students', 'manage student program enrollments', 'manage student session enrollments',
            'manage student statuses', 'manage enrollment statuses', 'manage program enrollment statuses',
            'manage student grades', 'manage student attendance', 'manage student documents', 'manage student transcripts',
            'manage student disciplinary actions', 'manage alumni records',
            'manage employees', 'manage employee types', 'manage job natures', 'manage employee work statuses', 'manage designations',
            'manage employee offices', 'manage employee payroll', 'manage employee leave records', 'manage employee leave requests',
            'manage employee performance reviews', 'manage staff attendance', 'manage employee documents',
            'manage campuses', 'manage offices', 'manage office types', 'manage campus facilities', 'manage office equipment',
            'manage payment methods', 'manage financial records', 'manage fees', 'manage invoices', 'manage fee structures',
            'manage scholarships', 'manage financial aid', 'manage financial statements', 'manage financial reports', 'manage chart of accounts',
            'manage system settings', 'manage email templates', 'manage system integrations', 'manage system logs',
            'manage application lookups', 'manage date types', 'manage payment gateways',
            'manage countries', 'manage states', 'manage cities',
            'manage site content', 'manage FAQs', 'manage news articles', 'manage events',
            'manage system reports', 'manage system announcements', 'manage communication templates',
            'manage library resources', 'manage library members', 'manage library fines', 'manage book categories',
            'manage admission applications', 'manage admission criteria', 'manage admission statistics', 'manage admission cycles',
            'manage timetables', 'manage examinations', 'manage exam invigilators', 'manage exam venues', 'manage exam marks', 'manage exam results', 'manage exam rechecks',
            'manage department program assignments', 'manage department subject assignments', 'manage program subject assignments', 'manage subject learning outcome assignments',
            'manage session offerings', 'manage audit trails', 'manage surveys', 'manage submitted feedback', 'manage api tokens',
            'impersonate users', 'export user data',
            'archive programs', 'propose curriculum changes', 'publish academic calendars', 'create academic calendar events',
            'import student data', 'export student data', 'issue student transcripts', 'generate student transcripts',
            'import employee data', 'export employee data', 'process employee payroll', 'generate payslips', 'approve employee leave requests', 'conduct employee performance reviews',
            'book campus facilities', 'manage facility bookings', 'assign office equipment', 'track office equipment',
            'process payments', 'apply discounts to fees', 'award scholarships', 'disburse financial aid', 'export financial data',
            'backup database', 'restore database from backup', 'archive system logs',
            'publish site content', 'approve site content', 'manage event registrations',
            'schedule system reports', 'send system notifications', 'send bulk emails', 'send SMS notifications',
            'import library resources', 'register library members', 'waive library fines',
            'send admission offers', 'resolve timetable clashes', 'publish examination results',
            'assign exam invigilators to exams', 'book exam venues for exams', /*'generate exam reports',*/ // 'generate system reports' covers this
            'request exam recheck', 'approve exam recheck requests', // These are actions on 'exam rechecks' resource
            'distribute surveys', 'analyze survey data', 'revoke api tokens',
            'enable maintenance mode', 'disable maintenance mode', 'view maintenance mode status',
            'configure attendance settings', 'publish results',
            // Ensure specific assignment permissions are covered if not by 'manage X assignments'
            'assign roles to users', 'assign permissions to role',
            'assign learning outcome to subject', 'assign subject to faculty', 'assign employee to office', 'remove employee from office',
            'assign program to department', 'assign subject to program', 'assign subject to department',
            'assign department program', 'remove department program', // from 'department program assignments'
            'assign department subject', 'remove department subject', // from 'department subject assignments'
            'assign program subject', 'remove program subject',       // from 'program subject assignments'
            'assign subject learning outcome', 'remove subject learning outcome' // from 'subject learning outcome assignments'
        ];
        $facultyPermissionsList = [
            'access faculty dashboard', 'view own profile', 'edit own profile', 'change own password',
            'view subjects', 'view courses', 'view learning outcomes', 'view students',
            'view student program enrollments', 'view student session enrollments',
            'view student grades', 'enter student grades', 'edit student grades',
            'view student attendance', 'record student attendance', 'edit student attendance',
            'upload student documents', 'view own timetable', 'view system announcements', 'view events',
            'submit feedback', 'view own performance review', 'apply for leave', 'view own leave balance',
            'book campus facilities', 'view session offerings', 'view academic calendars', 'view grading schemes',
            'view library resources', 'view examination schedule', 'enter exam marks', 'view submitted feedback',
        ];
        $studentPermissionsList = [
            'access student dashboard', 'view own profile', 'edit own profile', 'change own password',
            'view own academic record', 'view own courses', 'view own grades', 'view own attendance', 'view own enrollment status',
            'view subjects', 'view courses', 'view learning outcomes', 'view own documents', 'upload student documents',
            'request own transcript', 'view own timetable', 'view system announcements', 'view events', 'register for events',
            'borrow library items', 'return library items', 'view library borrowing history', 'manage library reservations',
            'submit feedback', 'apply for scholarship', 'view fee structures', 'view invoices',
            'view academic calendars', 'view grading schemes', 'view examination schedule', 'view exam results', 'request exam recheck',
        ];
        $registrarPermissionsList = [
            'access registrar dashboard', 'view own profile', 'edit own profile', 'change own password',
            'manage academic sessions', 'manage programs', 'manage subjects', 'manage courses', 'manage departments',
            'manage degree levels', 'manage curriculum', 'manage academic calendars', 'manage students',
            'manage student program enrollments', 'manage student session enrollments', 'manage student statuses',
            'manage enrollment statuses', 'manage program enrollment statuses', 'manage student grades',
            'manage student transcripts', 'manage student documents', 'manage alumni records',
            'manage admission applications', 'manage admission criteria', 'manage admission cycles', 'send admission offers',
            'manage timetables', 'publish timetables', 'resolve timetable clashes', 'manage examinations',
            'publish examination results', 'view exam results', 'manage session offerings',
            'manage department program assignments', 'manage department subject assignments', 'manage program subject assignments',
            'view faculties', 'view learning outcomes', 'view grading schemes', 'generate system reports',
            'view audit trails', 'view system announcements', 'view settings', 'publish results',
        ];
        $accountantPermissionsList = [
            'access accountant dashboard', 'view own profile', 'edit own profile', 'change own password',
            'manage payment methods', 'manage financial records', 'process payments', 'manage fees', 'manage invoices',
            'manage fee structures', 'apply discounts to fees', 'manage scholarships', 'award scholarships',
            'manage financial aid', 'disburse financial aid', 'manage financial statements', 'manage financial reports',
            'export financial data', 'manage chart of accounts', 'manage payment gateways', 'configure payment gateways',
            'view student grades', 'manage employee payroll', 'process employee payroll', 'generate payslips',
            'view system announcements', 'view settings', 'view audit trails',
        ];
        $librarianPermissionsList = [
            'access librarian dashboard', 'view own profile', 'edit own profile', 'change own password',
            'manage library resources', 'add library resources', 'edit library resources', 'delete library resources', 'import library resources',
            'borrow library items', 'return library items', 'manage library reservations', 'view library borrowing history',
            'manage library members', 'register library members', 'edit library members', 'delete library members',
            'manage library fines', 'collect library fines', 'waive library fines', 'edit library fines',
            'manage book categories', 'create book categories', 'edit book categories', 'delete book categories',
            'view students', 'view employees', 'view system announcements',
        ];
        $itSupportPermissionsList = [
            'access it support dashboard', 'view own profile', 'edit own profile', 'change own password',
            'view users', 'edit users', 'manage user preferences', 'view system settings', 'view settings',
            'view system integrations', 'backup database', 'restore database from backup', 'manage system logs',
            'view system logs', 'clear system logs', 'archive system logs', 'view api tokens',
            'enable maintenance mode', 'disable maintenance mode', 'view maintenance mode status',
            'view audit trails', 'view campus facilities', 'view office equipment', 'manage office equipment',
        ];
        $hrManagerPermissionsList = [
            'access hr dashboard', 'view own profile', 'edit own profile', 'change own password',
            'manage employees', 'import employee data', 'export employee data', 'manage employee types',
            'manage job natures', 'manage employee work statuses', 'manage designations', 'manage employee offices',
            'assign employee to office', 'remove employee from office', 'manage employee payroll',
            'manage employee leave records', 'manage employee leave requests', 'approve employee leave requests',
            'manage employee performance reviews', 'conduct employee performance reviews', 'submit self-assessment',
            'manage employee documents', 'approve employee documents', 'manage staff attendance', 'export staff attendance records',
            'generate system reports', 'manage system announcements', 'send system notifications', 'view audit trails', 'view settings',
        ];

        $allDefinedPermissionsForRoles = array_unique(array_merge(
            $adminPermissionsList, $facultyPermissionsList, $studentPermissionsList, $registrarPermissionsList,
            $accountantPermissionsList, $librarianPermissionsList, $itSupportPermissionsList, $hrManagerPermissionsList
        ));

        foreach($allDefinedPermissionsForRoles as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }


        // Define Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all()); // Super Admin gets all permissions

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($adminPermissionsList);

        $facultyRole = Role::firstOrCreate(['name' => 'Faculty']);
        $facultyRole->syncPermissions($facultyPermissionsList);

        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $studentRole->syncPermissions($studentPermissionsList);

        $registrarRole = Role::firstOrCreate(['name' => 'Registrar']);
        $registrarRole->syncPermissions($registrarPermissionsList);

        $accountantRole = Role::firstOrCreate(['name' => 'Accountant']);
        $accountantRole->syncPermissions($accountantPermissionsList);

        $librarianRole = Role::firstOrCreate(['name' => 'Librarian']);
        $librarianRole->syncPermissions($librarianPermissionsList);

        $itSupportRole = Role::firstOrCreate(['name' => 'IT Support']);
        $itSupportRole->syncPermissions($itSupportPermissionsList);

        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrManagerRole->syncPermissions($hrManagerPermissionsList);

        // Example: Assign Super Admin role to the first user
        $user = User::first();
        if ($user) {
            $user->assignRole('Super Admin');
        }

        $this->command->info('');
        $this->command->info('  Roles and permissions seeded successfully (RolePermissionSeeder).');
    }
}
