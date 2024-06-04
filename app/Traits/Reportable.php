<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait Reportable
{
    public function getMonthRange(int $numOfMonths = 3): Collection
    {
        return collect(range(0, $numOfMonths - 1))->map(fn($month) => now()->subMonths($month)->month)->reverse();
    }

    /**
     * Create labels for a given time range.
     *
     * @param string $unit Unit of time to be used for creating labels. (accepts: 'days', 'weeks', 'months', 'years') (default: 'days')
     * @param int $num Number of time units to be created. (default: 7)
     * @return array Returns an array of labels for the specified time range.
     */
    public function createLabelsFor(string $unit = 'days', int $num = 7): array
    {
        return $this->getTimeRange($unit, $num)->values()->toArray();
    }

    /**
     * Get time range based on given unit and number
     *
     * @param string $unit Unit of time to be used for creating time range (accepts: 'days', 'weeks', 'months', 'years') (default: 'days')
     * @param int $num Number of time units to be created (default: 7)
     * @return Collection Returns collection of time range
     */
    public function getTimeRange(string $unit = 'days', int $num = 7): Collection
    {
        $method = 'sub' . ucfirst($unit);

        return collect(range(0, $num - 1))
            ->map(fn($n) => now()->$method($n)->format($this->getTimeFormat($unit)))
            ->reverse();
    }

    private function getTimeFormat(string $unit = 'days'): string
    {
        $timeFrames = [
            'days' => 'm-d',
            'weeks' => 'W-Y',
            'months' => 'm-Y',
            'years' => 'Y',
        ];

        return $timeFrames[$unit];
    }

    /**
     * Create datasets for chart from given data
     *
     * @param Collection $data Data to be used for creating datasets
     * @param string $field Field to be used for creating datasets
     * @param string $unit Time unit to be used for creating labels (default: 'days')
     * @param int $num Number of datasets to be created (default: 7)
     * @return array Returns array of datasets
     */
    public function createDatasetsFor(Collection $data, string $field, string $unit = 'days', int $num = 7): array
    {
        return $this->getTimeRange($unit, $num)
            ->map(fn($time) => $data->firstWhere('time', $time)?->$field ?? 0)
            ->values()
            ->toArray();
    }

    public function createDataLabels(Collection $months): array
    {
        return $months->map(fn($month) => now()->month($month)->format('M'))->values()->toArray();
    }

    public function createDatasets(Collection $months, $dataByMonth, $field)
    {
        return $months->map(fn($month) => $dataByMonth->firstWhere('month', $month)?->$field ?? 0)->values()->toArray();
    }

    public function createReportData(string $chartLabel, $total, $current, $last, $rate, $status, array $dataLabels, array $data): array
    {
        return [
            'total' => $total,
            'current' => $current,
            'last' => $last,
            'rate' => $rate,
            'status' => $status,
            'chart' => $this->toChartData($chartLabel, $dataLabels, $data),
        ];
    }

    public function toChartData(string $chartLabel, array $dataLabels, array $data, array $options = []): array
    {
        return [
            'labels' => $dataLabels,
            'datasets' => [
                [
                    'label' => $chartLabel,
                    'data' => $data,
                    ...$options,
                ]
            ]
        ];
    }

    public function calcChangeRate($new, $old = null): array
    {
        $rate = isset($old) ? round((($new - $old) / $old) * 100, 2) : 100;
        $status = $rate > 0 ? 'up' : 'down';

        return [$rate, $status];
    }
}
