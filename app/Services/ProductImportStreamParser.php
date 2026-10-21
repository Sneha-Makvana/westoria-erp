<?php

namespace App\Services;

use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProductImportStreamParser
{
    public function __construct(
        private readonly ProductImportRowMapper $mapper
    ) {
    }

    /**
     * @return array{error: ?string, total_count: int, valid_count: int}
     */
    public function streamToJsonl(
        string $sourcePath,
        string $extension,
        string $jsonlPath,
        callable $mapRow,
        callable $isValidRow,
        callable $onProgress
    ): array {
        $extension = strtolower($extension);

        if (in_array($extension, ['xlsx'], true) && class_exists(XlsxReader::class)) {
            return $this->streamOpenSpout(new XlsxReader(), $sourcePath, $jsonlPath, $mapRow, $isValidRow, $onProgress);
        }

        if ($extension === 'csv' && class_exists(CsvReader::class)) {
            return $this->streamOpenSpout(new CsvReader(), $sourcePath, $jsonlPath, $mapRow, $isValidRow, $onProgress);
        }

        return $this->streamWithPhpSpreadsheet($sourcePath, $jsonlPath, $mapRow, $isValidRow, $onProgress);
    }

    /**
     * @return array{error: ?string, total_count: int, valid_count: int}
     */
    private function streamOpenSpout($reader, string $sourcePath, string $jsonlPath, callable $mapRow, callable $isValidRow, callable $onProgress): array
    {
        $reader->open($sourcePath);

        $handle = fopen($jsonlPath, 'wb');
        if ($handle === false) {
            $reader->close();

            return ['error' => 'Unable to prepare preview storage.', 'total_count' => 0, 'valid_count' => 0];
        }

        $headerMap    = null;
        $headerRowNum = null;
        $maxCol       = 20;
        $totalCount   = 0;
        $validCount   = 0;

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowIndex = 0;
                try {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    $rowValues = $row->toArray();
                    $maxCol    = max($maxCol, count($rowValues), 20);

                    if ($headerMap === null) {
                        if ($rowIndex > 20) {
                            fclose($handle);
                            $reader->close();

                            return ['error' => 'Could not find the header row in the file.', 'total_count' => 0, 'valid_count' => 0];
                        }

                        $cells    = $this->mapper->buildHeaderCells($rowValues, $maxCol);
                        $detected = $this->mapper->detectHeaderMap($cells);
                        if ($detected !== null) {
                            $headerMap    = $detected['header_map'];
                            $headerRowNum = $rowIndex;
                            $maxCol       = max($detected['max_col'], $maxCol);
                        }

                        continue;
                    }

                    if ($rowIndex <= $headerRowNum) {
                        continue;
                    }

                    $rowCells = $this->mapper->buildRowCells($rowValues, $maxCol);
                    if ($this->mapper->isEmptyRow($rowCells)) {
                        continue;
                    }

                    try {
                        $mapped = $mapRow($rowCells, $headerMap);
                    } catch (\Throwable $e) {
                        continue;
                    }

                    $encoded = json_encode([
                        'row_number' => $rowIndex,
                        'mapped'     => $mapped,
                    ], JSON_UNESCAPED_UNICODE);
                    if ($encoded === false) {
                        continue;
                    }

                    fwrite($handle, $encoded . "\n");

                    $totalCount++;
                    if ($isValidRow($mapped)) {
                        $validCount++;
                    }

                    if ($totalCount % 1000 === 0) {
                        $onProgress($totalCount, $validCount);
                    }
                }
                } catch (\Throwable $e) {
                    if ($totalCount === 0) {
                        throw $e;
                    }
                }

                break;
            }
        } finally {
            fclose($handle);
            $reader->close();
        }

        if ($headerMap === null) {
            @unlink($jsonlPath);

            return ['error' => 'Could not find the header row in the file.', 'total_count' => 0, 'valid_count' => 0];
        }

        $onProgress($totalCount, $validCount);

        return ['error' => null, 'total_count' => $totalCount, 'valid_count' => $validCount];
    }

    /**
     * @return array{error: ?string, total_count: int, valid_count: int}
     */
    private function streamWithPhpSpreadsheet(string $sourcePath, string $jsonlPath, callable $mapRow, callable $isValidRow, callable $onProgress): array
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($sourcePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($sourcePath);
        $sheet       = $spreadsheet->getActiveSheet();

        $handle = fopen($jsonlPath, 'wb');
        if ($handle === false) {
            $spreadsheet->disconnectWorksheets();

            return ['error' => 'Unable to prepare preview storage.', 'total_count' => 0, 'valid_count' => 0];
        }

        $headerMap    = null;
        $headerRowNum = null;
        $maxCol       = 20;
        $totalCount   = 0;
        $validCount   = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $rowCells = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $colIndex = 1;
            foreach ($cellIterator as $cell) {
                try {
                    $rowCells[$colIndex] = $this->mapper->stringifyCell($cell->getCalculatedValue());
                } catch (\Throwable $e) {
                    $rowCells[$colIndex] = $this->mapper->stringifyCell($cell->getValue());
                }
                $colIndex++;
            }

            $maxCol = max($maxCol, count($rowCells), 20);

            if ($headerMap === null) {
                if ($rowIndex > 20) {
                    break;
                }

                $cells    = $this->mapper->buildHeaderCellsFromIndexed($rowCells, $maxCol);
                $detected = $this->mapper->detectHeaderMap($cells);
                if ($detected !== null) {
                    $headerMap    = $detected['header_map'];
                    $headerRowNum = $rowIndex;
                    $maxCol       = max($detected['max_col'], $maxCol);
                }

                continue;
            }

            if ($rowIndex <= $headerRowNum) {
                continue;
            }

            if ($this->mapper->isEmptyRow($rowCells)) {
                continue;
            }

            try {
                $mapped = $mapRow($rowCells, $headerMap);
            } catch (\Throwable $e) {
                continue;
            }

            $encoded = json_encode([
                'row_number' => $rowIndex,
                'mapped'     => $mapped,
            ], JSON_UNESCAPED_UNICODE);
            if ($encoded === false) {
                continue;
            }

            fwrite($handle, $encoded . "\n");

            $totalCount++;
            if ($isValidRow($mapped)) {
                $validCount++;
            }

            if ($totalCount % 1000 === 0) {
                $onProgress($totalCount, $validCount);
            }
        }

        fclose($handle);
        $spreadsheet->disconnectWorksheets();

        if ($headerMap === null) {
            @unlink($jsonlPath);

            return ['error' => 'Could not find the header row in the file.', 'total_count' => 0, 'valid_count' => 0];
        }

        $onProgress($totalCount, $validCount);

        return ['error' => null, 'total_count' => $totalCount, 'valid_count' => $validCount];
    }
}
