<?php

namespace Database\Seeders;

use App\Helpers\CreacioUsuari;
use App\Models\Series;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Video;
use App\Helpers\DefaultVideos;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();
        Video::truncate();
        Role::truncate();
        Permission::truncate();
        Series::truncate();

        // Create roles and permissions
        CreacioUsuari::create_video_permissions();
        CreacioUsuari::create_user_management_permission();
        CreacioUsuari::create_series_management_permission();

        // Create users
        $superAdmin = CreacioUsuari::crearUsuariSuperAdmin();
        $regularUser = CreacioUsuari::crearUsuariRegular();
        $videoManager = CreacioUsuari::crearUsuariVideoManager();
        $defaultProfessor = CreacioUsuari::create_default_professor();
        $defaultAlumne = CreacioUsuari::create_default_alumne();

        // Assign roles to users
        $superAdmin->assignRole('super-admin');
        $videoManager->assignRole('video-manager');

        // Create default videos and series
        DefaultVideos::getDefaultValues();
    }
}
