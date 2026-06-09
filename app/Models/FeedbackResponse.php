<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackResponse extends Model
{
    protected $fillable = [
        'form_id', 'customer_id', 'order_id', 'product_id',
        'reponses', 'note', 'commentaire',
    ];

    protected $casts = [
        'reponses' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(FeedbackForm::class, 'form_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
