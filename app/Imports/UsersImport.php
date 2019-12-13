<?php

namespace App\Imports;

use App\Services\UserService;
use App\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class UsersImport implements OnEachRow, WithHeadingRow
{

    /**
     * Transform a date value into a Carbon object.
     *
     * @param $value
     * @param string $format
     * @return \Carbon\Carbon|null
     * @throws \Exception
     */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    /**
     * @param Row $row
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function onRow(Row $row)
    {
        // Remove unwanted columns from import file.
        $row = collect($row->toArray())->except([
            'sn',
            ''
        ]);

        // Ensure that all fields are trimmed
        $row = $row->map(function ($item, $key) {
            // Trim some fields
            if (in_array($key, ['gender', 'status', 'marital_status']) && isset($item)) {
                return trim($item);
            } else {
                return $item;
            }
        });

        $row = $row->toArray();

        $row['date_of_birth'] = $this->transformDate($row['date_of_birth']);

        $userService = app()->make(UserService::class);
        $userService->registerUser($row);
    }
}
