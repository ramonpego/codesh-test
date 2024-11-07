<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckDataFiles extends Command
{
    protected array $files = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-data-files {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download, extract, and import a limited number of JSON items into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Get file names to import...");
        $this->getFilesToImport();

    }

    /**
     * @return void
     */
    protected function getFilesToImport(): void
    {
        $this->files = array_filter(explode("\n", file_get_contents('https://challenges.coode.sh/food/data/json/index.txt')), function ($file) {
            return Str::endsWith($file, '.gz');
        });
        $this->info("Downloading and extracting files...");
        $this->downloadFiles();
        $this->cleanDirectories();
    }

    /**
     * @return void
     */
    protected function downloadFiles(): void
    {
        $this->verifyFolders();
        foreach ($this->files as $file){
            if ($this->checkIfFileIsImported($file)) {
                continue;
            }
            $url = 'https://challenges.coode.sh/food/data/json/' . $file;
            $gzFilePath = Storage::path('compressed/'.$file);
            $jsonFileName = Str::before($file, '.gz');
            $jsonFilePath = Storage::path('json/'.$jsonFileName);
            $gzFile = fopen($gzFilePath, 'w');
            $jsonFile = fopen($jsonFilePath, 'w');

            $this->info("Downloading {$file}...");
            $gzStream = fopen($url, 'r');
            stream_copy_to_stream($gzStream, $gzFile);
            fclose($gzStream);
            fclose($gzFile);
            $this->info("Extracting and saving {$file} into JSON file...");
            $gzFile = gzopen($gzFilePath, 'rb');
            while (!gzeof($gzFile)) {
                fwrite($jsonFile, gzread($gzFile, 4096));
            }
            gzclose($gzFile);
            fclose($jsonFile);
            unlink($gzFilePath);
            $this->info("Importing the JSON file into the database...");
            $this->importFileToDatabase($jsonFilePath);
            $this->markFileAsImported($file);
        }

    }

    /**
     * @param string $file
     * @return bool
     */
    protected function checkIfFileIsImported(string $file): bool
    {
        if (DB::table('data_files')->where('name', $file)->whereDate('last_checked','=', today())->exists()) {
            $this->info("File {$file} already imported. Skipping...");
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    protected function verifyFolders(): void
    {
        if (!Storage::directoryExists('compressed')) {
            Storage::makeDirectory('compressed');
        }
        if (!Storage::directoryExists('json')) {
            Storage::makeDirectory('json');
        }
    }

    /**
     * @return void
     */
    protected function cleanDirectories(): void
    {
        $this->info("Cleaning directories...");-
        Storage::deleteDirectory('compressed');
        Storage::deleteDirectory('json');
    }

    /**
     * @param $jsonFilePath
     * @return void
     */
    protected function importFileToDatabase($jsonFilePath): void
    {
        $jsonFile = fopen($jsonFilePath, 'r');
        $count = 1;
        while (!feof($jsonFile)) {
            $line = fgets($jsonFile);
            $product = json_decode($line, true);
            if ($product) {
                $product['imported_t'] = now();
                Product::query()->create($product);
            }
            if ($count >= $this->option('limit')) {
                break;
            }
            $count++;

        }
        fclose($jsonFile);
        unlink($jsonFilePath);
        $this->info('File downloaded, extracted, and saved successfully with streams!');
    }

    protected function markFileAsImported(string $file)
    {
        DB::table('data_files')->insert(
            [
                'name' => $file,
                'last_checked' => now()
            ],

        );
    }
}
