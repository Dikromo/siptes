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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

//class CustomerImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
class CustomerImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
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
        $cekProvider = substr($row['no_telp'], 0, 4);
        $dataProvider = '';
        $simpati = array('0811', '0812', '0813', '0851', '0852', '0853', '0821', '0822', '0823');
        $indosat = array('0814', '0815', '0816', '0855', '0856', '0857', '0858');
        $xl = array('0817', '0818', '0819', '0859', '0877', '0878');
        $axis = array('0838');
        $three = array('0896', '0895', '0897', '0898', '0899');
        $smart = array('0881', '0882', '0888');
        $telkom = array('0212', '0213', '0214', '0215', '0216', '0217', '0218');
        $esia = array('0219');

        switch ($cekProvider) {
            case in_array($cekProvider, $simpati):
                $dataProvider = 'SIMPATI';
                break;
            case in_array($cekProvider, $indosat):
                $dataProvider = 'INDOSAT';
                break;
            case in_array($cekProvider, $xl):
                $dataProvider = 'XL';
                break;
            case in_array($cekProvider, $axis):
                $dataProvider = 'AXIS';
                break;
            case in_array($cekProvider, $three):
                $dataProvider = 'THREE';
                break;
            case in_array($cekProvider, $smart):
                $dataProvider = 'SMART';
                break;
            case in_array($cekProvider, $telkom):
                $dataProvider = 'TELKOM';
                break;
            case in_array($cekProvider, $esia):
                $dataProvider = 'ESIA';
                break;
            default:
                $dataProvider = 'Tidak Ditemukan';
                break;
        }
        return new Customer([
            //
            'nama'          => trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $row['nama'])),
            'no_telp'       => $row['no_telp'],
            'perusahaan'    => $row['perusahaan'] == null ? '' : $row['perusahaan'],
            'kota'          => $row['kota'] == null ? '' : $row['kota'],
            'zipcode'       => $row['zipcode'] == null ? '' : $row['zipcode'],
            'provider'      => $dataProvider,
            'fileexcel_id'  => $this->id,
        ]);
    }
    public function getRowCount(): int
    {
        return $this->total;
    }
    // public function rules(): array
    // {
    //     return [
    //         // Above is alias for as it always validates in batches
    //         'no_telp' => ['unique:customers'],
    //     ];
    // }
    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
