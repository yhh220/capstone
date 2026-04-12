<?php

namespace Database\Seeders;

use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            // Proton
            ['brand' => 'Proton', 'model' => 'Saga',      'year_from' => 2008, 'year_to' => 2025],
            ['brand' => 'Proton', 'model' => 'Persona',   'year_from' => 2016, 'year_to' => 2025],
            ['brand' => 'Proton', 'model' => 'Iriz',      'year_from' => 2014, 'year_to' => 2025],
            ['brand' => 'Proton', 'model' => 'X50',       'year_from' => 2020, 'year_to' => 2025],
            ['brand' => 'Proton', 'model' => 'X70',       'year_from' => 2018, 'year_to' => 2025],
            ['brand' => 'Proton', 'model' => 'Exora',     'year_from' => 2009, 'year_to' => 2020],
            ['brand' => 'Proton', 'model' => 'Preve',     'year_from' => 2012, 'year_to' => 2018],
            // Perodua
            ['brand' => 'Perodua', 'model' => 'Myvi',    'year_from' => 2005, 'year_to' => 2025],
            ['brand' => 'Perodua', 'model' => 'Axia',    'year_from' => 2014, 'year_to' => 2025],
            ['brand' => 'Perodua', 'model' => 'Bezza',   'year_from' => 2016, 'year_to' => 2025],
            ['brand' => 'Perodua', 'model' => 'Aruz',    'year_from' => 2019, 'year_to' => 2025],
            ['brand' => 'Perodua', 'model' => 'Ativa',   'year_from' => 2021, 'year_to' => 2025],
            ['brand' => 'Perodua', 'model' => 'Alza',    'year_from' => 2009, 'year_to' => 2025],
            // Honda
            ['brand' => 'Honda', 'model' => 'City',       'year_from' => 2008, 'year_to' => 2025],
            ['brand' => 'Honda', 'model' => 'Civic',      'year_from' => 2006, 'year_to' => 2025],
            ['brand' => 'Honda', 'model' => 'Jazz',       'year_from' => 2008, 'year_to' => 2025],
            ['brand' => 'Honda', 'model' => 'HR-V',       'year_from' => 2015, 'year_to' => 2025],
            ['brand' => 'Honda', 'model' => 'CR-V',       'year_from' => 2007, 'year_to' => 2025],
            ['brand' => 'Honda', 'model' => 'Accord',     'year_from' => 2008, 'year_to' => 2025],
            // Toyota
            ['brand' => 'Toyota', 'model' => 'Vios',      'year_from' => 2003, 'year_to' => 2025],
            ['brand' => 'Toyota', 'model' => 'Yaris',     'year_from' => 2005, 'year_to' => 2025],
            ['brand' => 'Toyota', 'model' => 'Camry',     'year_from' => 2006, 'year_to' => 2025],
            ['brand' => 'Toyota', 'model' => 'Hilux',     'year_from' => 2005, 'year_to' => 2025],
            ['brand' => 'Toyota', 'model' => 'Fortuner',  'year_from' => 2005, 'year_to' => 2025],
            ['brand' => 'Toyota', 'model' => 'Corolla',   'year_from' => 2007, 'year_to' => 2025],
            // Mazda
            ['brand' => 'Mazda', 'model' => 'Mazda 3',    'year_from' => 2009, 'year_to' => 2025],
            ['brand' => 'Mazda', 'model' => 'Mazda 6',    'year_from' => 2007, 'year_to' => 2023],
            ['brand' => 'Mazda', 'model' => 'CX-3',       'year_from' => 2015, 'year_to' => 2025],
            ['brand' => 'Mazda', 'model' => 'CX-5',       'year_from' => 2012, 'year_to' => 2025],
            ['brand' => 'Mazda', 'model' => 'CX-30',      'year_from' => 2019, 'year_to' => 2025],
        ];

        foreach ($models as $data) {
            CarModel::firstOrCreate(
                ['brand' => $data['brand'], 'model' => $data['model']],
                ['year_from' => $data['year_from'], 'year_to' => $data['year_to']]
            );
        }
    }
}
