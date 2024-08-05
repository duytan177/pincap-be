<?php

namespace App\Http\Controllers\Reactions\Feelings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reactions\Feelings\FeelingCollection;
use App\Models\Feeling;

class GetAllFeelingController extends Controller
{
    public function __invoke()
    {
        return FeelingCollection::make(Feeling::all());
    }
}
