<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendTime;
use App\Models\Order;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{

    protected static ?int $sort = 1;
    protected  int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Revenue Chart';
    public null|string $filter = 'week';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $data = Trend::model(Order::class)
            ->between(
                start: match ($activeFilter) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'year' => now()->subYear(),
                    default => now()->subWeek(),
                },
                end: now()
            )
            ->perWeek()
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'Last Year',
        ];
    }
}
