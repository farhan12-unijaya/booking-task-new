<?php

namespace App\Imports;

use App\OtherModel\Guestlist;
use Maatwebsite\Excel\Concerns\ToModel;

class GuestListExcel implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Guestlist([
           'booking_id'  => $row[0],
           'name'     => $row[1],
           'email'    => $row[2],
           'rsvp'    => $row[3],
           'reminder'    => $row[4]
        ]);
    }
}
