<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only view on the table that panphp/pan writes to.
 *
 * @property int $id
 * @property string $name
 * @property int $impressions
 * @property int $hovers
 * @property int $clicks
 */
class PanAnalytics extends Model
{
    protected $table = 'pan_analytics';

    /**
     * Pan's table has no created_at/updated_at columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Some database drivers return numeric columns as strings, while the
     * widget formats them as integers.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'impressions' => 'integer',
            'hovers' => 'integer',
            'clicks' => 'integer',
        ];
    }
}
