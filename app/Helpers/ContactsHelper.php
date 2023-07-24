<?php

namespace App\Helpers;

use App\Enums\GenderEnum;
use App\Models\Concelho;
use App\Models\Contact;
use App\Models\ContactImage;
use App\Models\Country;
use App\Models\Distrito;
use App\Models\Freguesia;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Avatar;

class ContactsHelper
{

    static function getGenders(): array
    {
        $genders_dropdown = [];
        $genders = GenderEnum::cases();
        foreach ($genders as $gender) {
            $genders_dropdown[$gender->value] = __('generic.gender' . $gender->value);
        }
        return $genders_dropdown;
    }

    static function getCachedCountries()
    {
        return Cache::rememberForever('countries_dropdown', function () {
            return Country::select(['id', 'name', 'flag_unicode', 'dial_code'])
                ->orderBy('name', 'Asc')
                ->get();
        });
    }

    static function getCachedDistritos()
    {
        return Cache::rememberForever('distritos_dropdown', function () {
            $distritos = [];
            $rows = Distrito::select(['id', 'nome'])
                ->orderBy('nome', 'ASC')
                ->get();
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $distritos[] = [
                        'code' => $row->id,
                        'name' => $row->nome,
                    ];
                }
            }
            return $distritos;
        });
    }

    static function getCachedConcelhos(int $distrito_id = 0)
    {
        return Cache::rememberForever('concelhos_dropdown_' . $distrito_id, function () use ($distrito_id) {
            $concelhos = [];
            $Concelho = Concelho::select(['id', 'nome', 'distrito_id']);
            if($distrito_id > 0){
                $Concelho->where('distrito_id', $distrito_id);
            }
            $rows = $Concelho->orderBy('nome', 'ASC')->get();
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $concelhos[] = [
                        'code' => $row->id,
                        'name' => $row->nome,
                        'distrito_id' => $row->distrito_id,
                    ];
                }
            }
            return $concelhos;
        });
    }

    static function getCachedFreguesias($concelho_id = 0)
    {
        return Cache::rememberForever('freguesias_dropdown_' . $concelho_id, function () use ($concelho_id) {
            $freguesias = [];
            $Freguesia = Freguesia::select(['id', 'nome', 'concelho_id']);
            if($concelho_id > 0){
                $Freguesia->where('concelho_id', $concelho_id);
            }
            $rows = $Freguesia->orderBy('nome', 'ASC')->get();
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $freguesias[] = [
                        'code' => $row->id,
                        'name' => $row->nome,
                        'concelho_id' => $row->concelho_id,
                    ];
                }
            }
            return $freguesias;
        });
    }

    /**
     * Partir o nome completo para tirar primeiro e ultimo nome
     *
     * @param string $name
     * @return array
     */
    static function separateContactNames(string $name): array
    {
        $names = [];
        $name_parts = explode(" ", $name);
        $names['lastName'] = end($name_parts);
        array_pop($name_parts);
        $names['firstName'] = implode(" ", $name_parts);
        return $names;
    }

    static function getImageUrl(int $file_id, int $width, int $height, string $contact_fullname = ''): string
    {
        if ($file_id > 0) {
            return route('contacts/profilePhoto', [
                'id' => $file_id,
                'w' => $width,
                'h' => $height,
            ]);
        } else {
            return route('contacts/profilePhotoByName', [
                'name' => $contact_fullname,
                'w' => $width,
                'h' => $height,
            ]);
        }


    }

    static function getDefaultProfilePhoto(string $contact_name, int $width, int $height): string
    {
        $f_contact_name = str_replace(" ", "_", $contact_name);

        Storage::makeDirectory(config('soapp.profile_photos_folder'));
        Storage::makeDirectory(config('soapp.profile_photos_folder') . "/thumbs");

        $storage_path = storage_path("/app/" . config('soapp.profile_photos_folder'));
        $new_image_name = "avatar_{$f_contact_name}_{$width}x{$height}.jpg";
        $new_image_path = $storage_path . "/thumbs/" . $new_image_name;
        if (!file_exists($new_image_path)) {
            $fontSize = 42;
            $new_fontSize = $width * $fontSize / 100;
            $Avatar = new Avatar();
            $Avatar->create($contact_name)
                ->setShape('square')
                ->setBackground('#ebf4ff')
                ->setForeground('#839ff5')
                ->setBorder(0, '#ebf4ff')
                ->setDimension($width)
                ->setFontSize($new_fontSize)
                ->save($new_image_path, 95);
        }
        return $new_image_path;
    }

    static function getProfilePhoto(int $file_id, int $width, int $height)
    {
        Storage::makeDirectory(config('soapp.profile_photos_folder'));
        Storage::makeDirectory(config('soapp.profile_photos_folder') . "/thumbs");
        $contact_image = ContactImage::select('filename')->findOrFail($file_id);
        $storage_path = storage_path("/app/" . config('soapp.profile_photos_folder'));

        $new_image_name = str_replace(".", "", $contact_image->filename) . "_{$file_id}_{$width}x{$height}.jpg";
        $new_image_path = $storage_path . "/thumbs/" . $new_image_name;
        if (file_exists($new_image_path)) {
            return $new_image_path;
        }

        try {
            $image = new ImageResize($storage_path . "/" . $contact_image->filename);
            $image->quality_jpg = 90;
            $image->crop($width, $height, true);
            $image->save($new_image_path, IMAGETYPE_JPEG);
        } catch (ImageResizeException $e) {
            Log::error("Erro no resize da foto de contacto. " . $e->getMessage());
        }
        return $new_image_path;
    }

    static function getContactProfilePhotoUrl(Contact $contact, int $width, int $height)
    {
        $file_id = 0;
        if($contact->images->first()){
            $file_id = $contact->images->first()->id;
        }
        return self::getImageUrl($file_id, $width, $height, $contact->fullname);
    }

}
