<?php
namespace TypiCMS\Modules\Events\Models;

use TypiCMS\Models\Base;

use Input;
use Carbon\Carbon;

class Event extends Base
{

    use \Dimsav\Translatable\Translatable;

    protected $dates = array('start_date', 'end_date');

    protected $fillable = array(
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        // Translatable fields
        'title',
        'slug',
        'status',
        'summary',
        'body',
    );

    /**
     * Translatable model configs.
     *
     * @var array
     */
    public $translatedAttributes = array(
        'title',
        'slug',
        'status',
        'summary',
        'body',
    );

    /**
     * The default route for admin side.
     *
     * @var string
     */
    public $route = 'events';

    /**
     * lists
     */
    public $order = 'start_date';
    public $direction = 'asc';

    /**
     * Relations
     */
    public function files()
    {
        return $this->morphMany('TypiCMS\Modules\Files\Models\File', 'fileable');
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = Carbon::parse($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Carbon::parse($value);
    }
}
