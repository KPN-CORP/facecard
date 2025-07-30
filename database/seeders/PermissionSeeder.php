<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission; 

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Grup: Import Center
            ['name' => 'view_import_center', 'label' => 'View Import Center Menu', 'group' => 'Import Center'],
            ['name' => 'import_competency_assessment', 'label' => 'Import Competency Assessment', 'group' => 'Import Center'],
            ['name' => 'import_data_master', 'label' => 'Import Data Master', 'group' => 'Import Center'],
            ['name' => 'import_idp', 'label' => 'Import Individual Development Program', 'group' => 'Import Center'],
            ['name' => 'import_talent_box', 'label' => 'Import Talent Box', 'group' => 'Import Center'],
            ['name' => 'import_talent_status', 'label' => 'Import Talent Status', 'group' => 'Import Center'],
            ['name' => 'import_proposed_grade', 'label' => 'Import Proposed Grade', 'group' => 'Import Center'],
            
            // Grup: Report
            ['name' => 'view_report_menu', 'label' => 'View Report Menu', 'group' => 'Report'],
            ['name' => 'view_facecard_report', 'label' => 'View Facecard Report', 'group' => 'Report'],
            ['name' => 'view_idp_report', 'label' => 'View IDP Report', 'group' => 'Report'],
            ['name' => 'view_admin_setting', 'label' => 'View Admin Setting', 'group' => 'Report'],
        ];

    
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}