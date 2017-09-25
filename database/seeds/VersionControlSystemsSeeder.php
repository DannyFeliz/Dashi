<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VersionControlSystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('version_control_systems')->insert([
            ['user_agent' => "GitHub-Hookshot", "name" => "Github"],
            ['user_agent' => "Bitbucket-Webhooks", "name" => "Bitbucket"]
        ]);
    }
}
