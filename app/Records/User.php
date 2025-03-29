<?php

namespace Records;

use Illuminate\Database\Eloquent\Model as ActiveRecord;

class User extends ActiveRecord
{

    public $timestamps = false;


    public static function create(): self
    {
        $user = new User();

        $names = json_decode(file_get_contents('names.json'), true);
        $randomName = $names[mt_rand(0, 999)];

        $domain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        $randomEmailFromName = str_replace(array(' ', "'"), '', strtolower($randomName)) . '@' . $domain[mt_rand(0, 3)];

        $user->name = $randomName;
        $user->email = $randomEmailFromName;
        $user->save();

        return $user;

    }
}