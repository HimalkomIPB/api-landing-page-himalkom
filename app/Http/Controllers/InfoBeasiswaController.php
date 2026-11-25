<?php

namespace App\Http\Controllers;

use App\Models\InfoBeasiswa;

class InfoBeasiswaController extends RegistrationInfoController
{
    protected string $model = InfoBeasiswa::class;

    protected string $responseKey = 'infobeasiswa';

    protected string $notFoundMessage = 'Info beasiswa not found';
}
