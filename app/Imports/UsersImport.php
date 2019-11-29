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
     * @param Row $row
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function onRow(Row $row)
    {
        $row = collect($row->toArray())->except([
            'sn',
            ''
        ])->toArray();
        $userService = app()->make(UserService::class);

        $userService->registerUser($row);
    }
}
