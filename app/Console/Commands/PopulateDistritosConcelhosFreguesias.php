<?php

namespace App\Console\Commands;

use App\Models\Concelho;
use App\Models\Distrito;
use App\Models\Freguesia;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\Types\Null_;

class PopulateDistritosConcelhosFreguesias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:DistritosConcelhosFreguesias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popular as tabelas distritos, concelhos e freguesias consumindo um webservice externo';

    private array $distritos = [];

    private array $concelhos = [];

    private array $freguesias = [];

    /**
     * @throws RequestException
     */
    private function fetchInfoWs(string $url)
    {
        return Http::get($url)->throw(function ($response, $e) {
            $e->getMessage();
        })->json();
    }

    private function populate()
    {
        $distritos = $this->fetchInfoWs('https://geoapi.pt/distritos/municipios?json=1');
        if(empty($distritos)){
            $this->error('Ocorreu uma falha ao obter os dados!');
            return false;
        }

        foreach ($distritos as $distrito) {
            $this->distritos[] = ucfirst($distrito['distrito']);
            if(!empty($distrito['municipios'])){
                foreach ($distrito['municipios'] as $municipio) {
                    $this->concelhos[] = [
                        'nome' => ucfirst($municipio),
                        'distrito' => ucfirst($distrito['distrito']),
                    ];

                    $freguesias = $this->fetchInfoWs("https://geoapi.pt/freguesia?municipio=$municipio&json=1");
                    if(!empty($freguesias)){
                        foreach ($freguesias as $freguesia) {
                            if(isset($freguesia['nome']) AND $freguesia['municipio']){
                                $this->freguesias[] = [
                                    'nome' => ucfirst($freguesia['nome']),
                                    'concelho' => ucfirst($freguesia['municipio'])
                                ];
                            }
                        }
                    }

                }
            }
        }


        if(empty($this->distritos) OR empty($this->concelhos) OR empty($this->freguesias)){
            $this->error('Ocorreu uma falha ao obter os dados!');
            return false;
        }

        foreach ($this->distritos as $distrito) {
            Distrito::updateOrCreate(
                ['nome' => $distrito]
            );
        }

        foreach ($this->concelhos as $concelho) {
            $distrito_id = Distrito::where('nome', $concelho['distrito'])->first()->id;
            Concelho::updateOrCreate(
                ['nome' => $concelho['nome'], 'distrito_id' => $distrito_id]
            );
        }

        foreach ($this->freguesias as $freguesia) {
            $concelho_id = Concelho::where('nome', $freguesia['concelho'])->first()->id;
            Freguesia::updateOrCreate(
                ['nome' => $freguesia['nome'], 'concelho_id' => $concelho_id]
            );
        }

        return true;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $populate = $this->populate();
            if($populate){
                $this->info('As tabelas foram populadas com sucesso!');
            }else{
                $this->error('O comando falhou!');
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            $this->error('O comando falhou!');
        }
    }
}
