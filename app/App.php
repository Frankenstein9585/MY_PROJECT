<?php

declare(strict_types = 1);

// Your Code
function getTransactionFiles(string $dir_path): array
{
    $files = [];

    foreach (scandir($dir_path) as $file) {
        if (is_dir($file)) {
            continue;
        }
         $files[] = $dir_path . $file;
    }

    return $files;
}
function getTransactions(string $file_name, ?callable $transactionHandler = null): array
{
   if (!file_exists($file_name)) {
       trigger_error('File "'. $file_name . '" does not exist,', E_USER_ERROR);
   }

   $file = fopen($file_name, 'r');

   $transactions = [];

   while (($line = fgetcsv($file)) !== false) {
       $transactions[] = $transactionHandler ($line);
   }

   array_shift($transactions);
   return $transactions;
}

function parseTransaction(array $transactionLine): array {
    [$date, $checkNumber, $description, $amount] = $transactionLine;
    $amount = (float) str_replace(['$', ','], '', $amount);
    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount' => $amount
    ];
}

function getTotal(array $transactions): array {
    $totals = ['net_total' => 0, 'total_income' => 0, 'total_expenditure' => 0];
    foreach ($transactions as $transaction) {
        $totals['net_total'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $totals['total_income'] += $transaction['amount'];
        } else {
            $totals['total_expenditure'] += $transaction['amount'];
        }

    }
    return $totals;
}

