<?php

namespace App\Http\Controllers\Feelings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Feelings\FeelingCollection;
use App\Models\Feeling;

class GetAllFeelingController extends Controller
{
    public function __invoke()
    {
        return FeelingCollection::make(Feeling::all());
    }
}
