<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckDataFiles extends Command
{
    protected $signature = 'app:check-data-files {--limit=100}';
    protected $description = 'Download, extract, and import a limited number of JSON items into the database';

    protected array $files = [];

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info("Fetching files to import...");
        $this->getFilesToImport();
    }

    /**
     * @return void
     */
    protected function getFilesToImport(): void
    {
        $this->files = array_filter(
            explode("\n", file_get_contents('https://challenges.coode.sh/food/data/json/index.txt')),
            fn($file) => Str::endsWith($file, '.gz')
        );

        foreach ($this->files as $file) {
            if ($this->checkIfFileIsImported($file)) continue;
            $this->downloadAndProcessFile($file);
            $this->markFileAsImported($file);
        }
    }

    /**
     * @param string $file
     * @return void
     */
    protected function downloadAndProcessFile(string $file): void
    {
        $url = 'https://challenges.coode.sh/food/data/json/' . $file;
        $limit = (int) $this->option('limit');
        $count = 0;

        $this->info("Downloading and processing {$file}...");

        $gzStream = gzopen($url, 'rb');
        $buffer = '';
        DB::beginTransaction();
        try {
            while (!gzeof($gzStream) && $count < $limit) {
                $buffer .= gzread($gzStream, 4096);
                while (($pos = strpos($buffer, "\n")) !== false && $count < $limit) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);

                    $product = json_decode($line, true);
                    if (is_array($product)) {
                        $product['imported_t'] = now();
                        if (isset($product['code'])) {
                            $product['code'] = preg_replace('/\D/', '', $product['code']);
                        }
                        Product::create($product);
                        $count++;
                    }
                }

            }
            DB::commit();
            $this->info("Imported $count items from {$file}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error importing file {$file}: " . $e->getMessage());


        } finally {
            gzclose($gzStream);
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function checkIfFileIsImported(string $file): bool
    {
        //TODO use hash instead of filename and date to only import if file is changed or new
        $exists = DB::table('data_files')
            ->where('name', $file)
            ->exists();

        if ($exists) $this->info("File {$file} already imported. Skipping...");
        return $exists;
    }

    /**
     * @param string $file
     * @return void
     */
    protected function markFileAsImported(string $file): void
    {

        DB::table('data_files')->insert([
            'name' => $file,
            'last_checked' => now()
        ]);
    }
}
