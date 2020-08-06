<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 
use App\Models\Organization;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $organization = new Organization();
        $organization->name="Instituto de Información Estadistica y Geografica de Jalisco";
        $organization->abbreviation= "IIEG";
        $organization->phone= "37771770";
        $organization->email= "contacto@iieg.gob.mx";
        $organization->status= 1;
        $organization->x= 20.6840736;
        $organization->y= -103.4471341;

        $organization->save();



        $user = Factory(App\User::class)->create([
            'name' => 'Admin',
            'last_name'=>'System',
            'email' => 'admin@example.com',
            'organization_id' =>$organization->id,
            'password' => Hash::make('1234567890')
        ]);
    }
}
