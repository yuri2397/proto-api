<?php

namespace Database\Seeders;

use App\Models\ShopProductProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopProductProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            [
                "name" => "AFRICA DELICES NDIOBA THIAM",
                "phone" => "773455678",
                "address" => "Dakar, Sénégal",
                "email" => "contact@africadelices.com",
                "ninea" => "1234567890123",
                "rccm" => "SN-DKR-2023-A-12345",
                "contact_person" => "Ndioba Thiam",
                "contact_person_phone" => "773455678",
                "contact_person_email" => "ndioba.thiam@africadelices.com",
                "status" => "active"
            ],
            [
                "name" => "AGJ GROUPE",
                "phone" => "778654321",
                "address" => "Saint-Louis, Sénégal",
                "email" => "info@agjgroupe.com",
                "ninea" => "9876543210123",
                "rccm" => "SN-STL-2022-B-56789",
                "contact_person" => "Amadou Diop",
                "contact_person_phone" => "778654321",
                "contact_person_email" => "amadou.diop@agjgroupe.com",
                "status" => "inactive"
            ],
            [
                "name" => "AL NEJAH",
                "phone" => "708763542",
                "address" => "Kaolack, Sénégal",
                "email" => "alnejah@gmail.com",
                "ninea" => "1239876540123",
                "rccm" => "SN-KLK-2021-C-67890",
                "contact_person" => "Aïssata Ndiaye",
                "contact_person_phone" => "708763542",
                "contact_person_email" => "aissata.ndiaye@alnejah.com",
                "status" => "active"
            ],
            [
                "name" => "AMADOU IBRAHIMA BA",
                "phone" => "778965432",
                "address" => "Thiès, Sénégal",
                "email" => "amadou.ibrahim.ba@gmail.com",
                "ninea" => "4567890123456",
                "rccm" => "SN-THI-2020-D-89012",
                "contact_person" => "Amadou Ibrahima Ba",
                "contact_person_phone" => "778965432",
                "contact_person_email" => "contact@ibrahimaba.com",
                "status" => "active"
            ],
            [
                "name" => "ARAME JUS",
                "phone" => "709856431",
                "address" => "Ziguinchor, Sénégal",
                "email" => "aramejus@zigmail.com",
                "ninea" => "7891234560123",
                "rccm" => "SN-ZIG-2023-E-23456",
                "contact_person" => "Arame Diouf",
                "contact_person_phone" => "709856431",
                "contact_person_email" => "arame.diouf@aramejus.com",
                "status" => "inactive"
            ],
            [
                "name" => "ASTOU DIALLO",
                "phone" => "778643210",
                "address" => "Louga, Sénégal",
                "email" => "astou.diallo@lougashop.com",
                "ninea" => "3216549870123",
                "rccm" => "SN-LOU-2019-F-34567",
                "contact_person" => "Astou Diallo",
                "contact_person_phone" => "778643210",
                "contact_person_email" => "contact@astoudiallo.com",
                "status" => "active"
            ],
            [
                "name" => "BMA",
                "phone" => "778912345",
                "address" => "Rufisque, Sénégal",
                "email" => "contact@bma.sn",
                "ninea" => "1234567812345",
                "rccm" => "SN-RUF-2021-G-34567",
                "contact_person" => "Moussa Ba",
                "contact_person_phone" => "778912345",
                "contact_person_email" => "moussa.ba@bma.sn",
                "status" => "active"
            ],
            [
                "name" => "CCSN",
                "phone" => "779812367",
                "address" => "Dakar Plateau, Sénégal",
                "email" => "info@ccsn.sn",
                "ninea" => "4567890123567",
                "rccm" => "SN-DKR-2020-H-78901",
                "contact_person" => "Oumar Ndiaye",
                "contact_person_phone" => "779812367",
                "contact_person_email" => "oumar.ndiaye@ccsn.sn",
                "status" => "inactive"
            ],
            [
                "name" => "CDA",
                "phone" => "708123456",
                "address" => "Mbour, Sénégal",
                "email" => "contact@cda.sn",
                "ninea" => "9876543214567",
                "rccm" => "SN-MBO-2022-I-23456",
                "contact_person" => "Fatou Sow",
                "contact_person_phone" => "708123456",
                "contact_person_email" => "fatou.sow@cda.sn",
                "status" => "active"
            ],
            [
                "name" => "CHARBEL",
                "phone" => "777654321",
                "address" => "Saint-Louis, Sénégal",
                "email" => "charbel@business.sn",
                "ninea" => "6549873214567",
                "rccm" => "SN-STL-2018-J-56789",
                "contact_person" => "Charbel Georges",
                "contact_person_phone" => "777654321",
                "contact_person_email" => "georges.charbel@charbelbusiness.sn",
                "status" => "active"
            ],
            [
                "name" => "CHEIKH M. AMAR",
                "phone" => "770987654",
                "address" => "Kolda, Sénégal",
                "email" => "cheikh.amar@kolda.sn",
                "ninea" => "3216549871234",
                "rccm" => "SN-KOL-2019-K-89012",
                "contact_person" => "Cheikh Amar",
                "contact_person_phone" => "770987654",
                "contact_person_email" => "amar.cheikh@kolda.sn",
                "status" => "active"
            ],
            [
                "name" => "D K T",
                "phone" => "707891234",
                "address" => "Dakar, Sénégal",
                "email" => "contact@dkt.sn",
                "ninea" => "7891236549870",
                "rccm" => "SN-DKR-2023-L-12345",
                "contact_person" => "Mamadou Kane",
                "contact_person_phone" => "707891234",
                "contact_person_email" => "mamadou.kane@dkt.sn",
                "status" => "inactive"
            ],
            [
                "name" => "DELICES DE MAMAN",
                "phone" => "778912345",
                "address" => "Thiès, Sénégal",
                "email" => "info@delicesdemaman.sn",
                "ninea" => "4567891230123",
                "rccm" => "SN-THI-2021-M-56789",
                "contact_person" => "Aissatou Sy",
                "contact_person_phone" => "778912345",
                "contact_person_email" => "aissatou.sy@delicesdemaman.sn",
                "status" => "active"
            ],
            [
                "name" => "DIA ET FRERES",
                "phone" => "771234567",
                "address" => "Ziguinchor, Sénégal",
                "email" => "diafreres@business.sn",
                "ninea" => "9876541236548",
                "rccm" => "SN-ZIG-2020-N-67890",
                "contact_person" => "Alioune Dia",
                "contact_person_phone" => "771234567",
                "contact_person_email" => "alioune.dia@business.sn",
                "status" => "active"
            ],
            [
                "name" => "DOLIMA",
                "phone" => "773456789",
                "address" => "Dakar, Sénégal",
                "email" => "contact@dolima.sn",
                "ninea" => "1236547893210",
                "rccm" => "SN-DKR-2022-O-34567",
                "contact_person" => "Ndeye Diouf",
                "contact_person_phone" => "773456789",
                "contact_person_email" => "ndeye.diouf@dolima.sn",
                "status" => "inactive"
            ],
            [
                "name" => "ETS DIOUF",
                "phone" => "708654321",
                "address" => "Touba, Sénégal",
                "email" => "etsdiouf@business.sn",
                "ninea" => "3219874561230",
                "rccm" => "SN-TOU-2021-P-89012",
                "contact_person" => "Cheikh Diouf",
                "contact_person_phone" => "708654321",
                "contact_person_email" => "cheikh.diouf@business.sn",
                "status" => "active"
            ],
        ];

        foreach ($list as $item) {
            ShopProductProvider::create($item);
        }
    }
}
