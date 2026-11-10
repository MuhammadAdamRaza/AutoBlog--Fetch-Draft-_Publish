<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostFetchLog extends Model
{
    protected $fillable = [
        'post_id',
        'source_url',
        'status',
        'error',
    ];

    public function userFacingError(): ?string
    {
        if ($this->error === null) {
            return null;
        }

        if (preg_match('/status code 402|more credits|insufficient credits|could not fund/i', $this->error) === 1) {
            return 'OpenRouter needs more credits for this draft. Add credits to your OpenRouter account, then submit the link again.';
        }

        return 'We could not create the draft. Check that the source link is public and try again. If it keeps failing, check the OpenRouter account settings.';
    }
}
