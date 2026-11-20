<?php

namespace App\Http\Controllers;

use App\Models\InfoMagang;

class InfoMagangController extends RegistrationInfoController
{
    protected string $model = InfoMagang::class;

    protected string $responseKey = 'infomagang';

    protected string $notFoundMessage = 'Info magang not found';
}
