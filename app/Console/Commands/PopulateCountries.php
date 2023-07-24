<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PopulateCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popular a tabela contries consumindo um webservice externo';

    private array $countries = [];

    /**
     * @throws RequestException
     */
    private function fetchInfoWs(string $url)
    {
        return Http::get($url)->throw(function ($response, $e) {
            $e->getMessage();
        })->json('data');
    }

    /**
     * @throws RequestException
     */
    private function getCountries(): void
    {

        $this->countries = $this->fetchInfoWs('https://countriesnow.space/api/v0.1/countries/flag/unicode');

        $countries = $this->fetchInfoWs('https://countriesnow.space/api/v0.1/countries/info?returns=currency,flag,unicodeFlag,dialCode,capital');
        if (!empty($countries)) {
            foreach ($countries as $country) {
                Arr::map($this->countries, function ($value, $key) use ($country) {
                    if ($country['name'] === $this->countries[$key]['name']) {
                        if (isset($country['currency'])) {
                            $this->countries[$key]['currency'] = $country['currency'];
                        }
                        if (isset($country['unicodeFlag'])) {
                            $this->countries[$key]['unicodeFlag'] = $country['unicodeFlag'];
                        }
                        if (isset($country['flag'])) {
                            $this->countries[$key]['flag'] = $country['flag'];
                        }
                        if (isset($country['dialCode'])) {
                            $this->countries[$key]['dialCode'] = $country['dialCode'];
                        }
                    }
                });
            }
        }

        $countries = $this->fetchInfoWs('https://countriesnow.space/api/v0.1/countries/capital');
        if (!empty($countries)) {
            foreach ($countries as $country) {
                Arr::map($this->countries, function ($value, $key) use ($country) {
                    if ($country['name'] === $this->countries[$key]['name']) {
                        if (isset($country['capital'])) {
                            $this->countries[$key]['capital'] = $country['capital'];
                        }
                    }
                });
            }
        }

        $countries = $this->fetchInfoWs('https://countriesnow.space/api/v0.1/countries/positions');
        if (!empty($countries)) {
            foreach ($countries as $country) {
                Arr::map($this->countries, function ($value, $key) use ($country) {
                    if ($country['name'] === $this->countries[$key]['name']) {
                        if (isset($country['long'])) {
                            $this->countries[$key]['long'] = $country['long'];
                        }
                        if (isset($country['lat'])) {
                            $this->countries[$key]['lat'] = $country['lat'];
                        }
                    }
                });
            }
        }

        if (empty($this->countries)) {
            return;
        }

        //Passar Portugal para primeiro lugar
        Arr::map($this->countries, function ($value, $key) {
            if ($value['name'] == 'Portugal') {
                unset($this->countries[$key]);
                array_unshift($this->countries, $value);

            }
        });

        foreach ($this->countries as $country) {
            DB::table('countries')
                ->updateOrInsert(
                    ['iso' => $country['iso2']],
                    [
                        'iso3' => $country['iso3'],
                        'name' => $country['name'],
                        'currency_code' => $country['currency'] ?? null,
                        'flag_unicode' => $country['unicodeFlag'] ?? null,
                        'flag_url' => $country['flag'] ?? null,
                        'dial_code' => $country['dialCode'] ?? null,
                        'capital' => $country['capital'] ?? null,
                        'longitude' => $country['long'] ?? null,
                        'latitude' => $country['lat'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
        }

    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        try {
            $this->getCountries();
            $this->info('A tabela de países foi populada com sucesso!');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            $this->error('O comando falhou!');
        }
    }
}
