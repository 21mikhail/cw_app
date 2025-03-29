<?php

namespace Records;

use Illuminate\Database\Eloquent\Model as ActiveRecord;

class Email extends ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAIL = 'fail';

    public $timestamps = false;

    public static function create(User $user): self
    {

        $email = new Email();
        $email->email = $user->email;
        $email->text = "Hello, " . $user->name;
        $email->status = static::STATUS_PENDING;

        $email->save();

        return $email;
    }



    public static function seStatusSent(Email $email)
    {
        $email->status = static::STATUS_SENT;
        $email->save();
    }

}