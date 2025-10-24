<?php

namespace paxxion\crafteternalslug\services;

use craft\base\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService extends Component
{
    public static function export($rows) {
        $body = [];
        foreach ($rows as $row) {
            $body[] = [
                $row->id,
                $row->uid,
                $row->dateCreated,
                $row->dateUpdated,
                $row->siteId,
                $row->entryId,
                $row->entryOldUrl,
                $row->entryNewUrl,
                $row->totalHits,
            ];
        }

        // xlsx
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Eternal Slug');

        // clean buffer
        ob_end_clean();

        // xls headers
        $sheet->fromArray(
            [
                'id',
                'uid',
                'dateCreated',
                'dateUpdated',
                'siteId',
                'entryId',
                'entryOldUrl',
                'entryNewUrl',
                'totalHits',
            ],null,
            'A1'
        );
        
        // content
        $sheet->fromArray($body, null, 'A2');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="eternal-slug.xlsx"');
        header('Cache-Control: max-age=0');

        exit($writer->save('php://output'));
    }
}
