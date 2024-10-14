<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class password extends Controller
{
     /**
     * Update the flight information for an existing flight.
     */
    public function update(Request $request): Controller
 {
   $user = $request->user();
    //...
    return redirect('/flights');
  }

}
