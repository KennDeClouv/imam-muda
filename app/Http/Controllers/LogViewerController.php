<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    private function calculateLogStats($files)
    {
        $stats = [
            'local' => 0,
            'production' => 0,
            'error' => 0,
            'warning' => 0,
            'info' => 0,
        ];

        foreach ($files as $file) {
            $content = File::get($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (preg_match('/^\[(.*?)\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
                    $env = strtolower($matches[2] ?? 'unknown');
                    $type = strtolower($matches[3] ?? 'unknown');

                    if (isset($stats[$env])) {
                        $stats[$env]++;
                    }
                    if (isset($stats[$type])) {
                        $stats[$type]++;
                    }
                }
            }
        }

        return $stats;
    }

    public function index()
    {
        $logFiles = File::files(storage_path('logs'));
        $logs = [];

        $files = File::files(storage_path('logs'));
        $stats = $this->calculateLogStats($files);

        foreach ($logFiles as $file) {
            $logs[] = [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, precision: 2), // ukuran KB
                'modified' => $file->getMTime(),

            ];
        }

        return view('Superadmin.log.index', compact('logs', 'stats'));
    }

    public function show($filename)
    {
        $filePath = storage_path("logs/{$filename}");
        abort_unless(File::exists($filePath), 404, 'Log file not found!');

        $content = File::get($filePath);
        $logs = $this->parseLog($content);


        return response()->view('Superadmin.log.show', compact('logs', 'filename'));
    }

    private function parseLog($content)
    {
        $logEntries = [];
        $lines = explode("\n", $content);
        $currentEntry = '';

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                if (!empty($currentEntry)) {
                    $logEntries[] = $this->formatLogEntry($currentEntry);
                }
                $currentEntry = $line;
            } else {
                $currentEntry .= "\n" . $line;
            }
        }

        if (!empty($currentEntry)) {
            $logEntries[] = $this->formatLogEntry($currentEntry);
        }

        return $logEntries;
    }

    private function formatLogEntry($entry)
    {
        preg_match('/^\[(.*?)\] (\w+)\.(\w+): (.+)$/s', $entry, $matches);

        return [
            'timestamp' => $matches[1] ?? 'Unknown',    // timestamp di [2024-11-22 14:13:44]
            'env' => $matches[2] ?? 'Unknown',         // tipe environment (local, production)
            'type' => $matches[3] ?? 'Unknown',        // tipe log (ERROR, INFO, dll.)
            'message' => $matches[4] ?? $entry,        // isi log
        ];
    }

    public function destroy($filename)
    {
        File::delete(storage_path("logs/{$filename}"));
        return redirect()->route('superadmin.logs')->with('success', 'Log file deleted successfully!');
    }
    public function download($filename)
    {
        $path = storage_path("logs/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'Log file tidak ditemukan.');
        }

        return response()->download($path);
    }
}