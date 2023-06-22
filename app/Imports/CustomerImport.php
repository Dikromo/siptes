<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use Importable, SkipsErrors, SkipsFailures;
    private $id, $total = 0;
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function model(array $row)
    {
        ++$this->total;
        return new Customer([
            //
            'nama'          => $row['nama'],
            'no_telp'       => $row['no_telp'],
            'perusahaan'    => $row['perusahaan'],
            'fileexcel_id'  => $this->id,
        ]);
    }
    public function getRowCount(): int
    {
        return $this->total;
    }
    public function rules(): array
    {
        return [
            // Above is alias for as it always validates in batches
            'no_telp' => ['unique:customers'],
        ];
    }
}
