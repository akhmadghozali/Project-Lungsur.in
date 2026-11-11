<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id', 'reported_listing_id', 'reported_user_id',
        'reason', 'details', 'status', 'resolved_by', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function reporter()        { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reportedUser()    { return $this->belongsTo(User::class, 'reported_user_id'); }
    public function resolver()        { return $this->belongsTo(User::class, 'resolved_by'); }
    public function reportedListing() { return $this->belongsTo(Listing::class, 'reported_listing_id')->withTrashed(); }
}