<?php

namespace App\Helpers;

use App\Enums\CompanyStatusEnum;
use App\Models\Company;
use App\Models\CompanyImage;
use App\Models\CompanySector;
use App\Models\CompanyZone;
use App\Models\Contact;
use App\Models\ContactImage;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Avatar;

class CompanyHelper
{

    static function getCompanyStatuses(): array
    {
        $new_statuses = [];
        $statuses = CompanyStatusEnum::cases();
        foreach ($statuses as $status) {
            $new_statuses[$status->value] = __('generic.' . $status->value);
        }
        return $new_statuses;
    }

    static function getCompanyStatusesDropdown(): array
    {
        $status_dropdown = [];
        $statuses = self::getCompanyStatuses();
        foreach ($statuses as $key => $status) {
            $status_dropdown[] = [
                'code' => $key,
                'name' => $status,
            ];
        }
        return $status_dropdown;
    }

    static function getCachedCompanyZonesDropdown()
    {
        return Cache::rememberForever('company_zones_dropdown', function () {
            $company_zones = [];
            $rows = CompanyZone::select(['id', 'name'])
                ->orderBy('name', 'ASC')
                ->get();
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $company_zones[] = [
                        'code' => $row->id,
                        'name' => $row->name,
                    ];
                }
            }
            return $company_zones;
        });
    }

    static function getCachedCompanySectorsDropdown()
    {
        return Cache::rememberForever('company_sectors_dropdown', function () {
            $company_sectors = [];
            $rows = CompanySector::select(['id', 'code', 'name'])
                ->orderBy('code', 'ASC')
                ->get();
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $company_sectors[] = [
                        'code' => $row->id,
                        'name' => $row->code . " - " . $row->name,
                    ];
                }
            }
            return $company_sectors;
        });
    }


    static function getImageUrl(int $file_id, int $width, int $height, string $name = ''): string
    {
        if ($file_id > 0) {
            return route('companies/photo', [
                'id' => $file_id,
                'w' => $width,
                'h' => $height,
            ]);
        } else {
            return route('companies/photoByName', [
                'name' => $name,
                'w' => $width,
                'h' => $height,
            ]);
        }
    }

    static function getDefaultCompanyPhoto(string $name, int $width, int $height): string
    {
        $f_name = str_replace(" ", "_", $name);

        Storage::makeDirectory(Company::PHOTOS_FOLDER);
        Storage::makeDirectory(Company::PHOTOS_FOLDER . "/thumbs");

        $storage_path = storage_path("/app/" . Company::PHOTOS_FOLDER);
        $new_image_name = "photo_{$f_name}_{$width}x{$height}.jpg";
        $new_image_path = $storage_path . "/thumbs/" . $new_image_name;
        if (!file_exists($new_image_path)) {
            $fontSize = 42;
            $new_fontSize = $width * $fontSize / 100;
            $Avatar = new Avatar();
            $Avatar->create($name)
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

    static function getCompanyPhoto(int $file_id, int $width, int $height)
    {
        Storage::makeDirectory(Company::PHOTOS_FOLDER);
        Storage::makeDirectory(Company::PHOTOS_FOLDER . "/thumbs");
        $company_image = CompanyImage::select('filename')->findOrFail($file_id);
        $storage_path = storage_path("/app/" . Company::PHOTOS_FOLDER);

        $new_image_name = str_replace(".", "", $company_image->filename) . "_{$file_id}_{$width}x{$height}.jpg";
        $new_image_path = $storage_path . "/thumbs/" . $new_image_name;
        if (file_exists($new_image_path)) {
            return $new_image_path;
        }

        try {
            $image = new ImageResize($storage_path . "/" . $company_image->filename);
            $image->quality_jpg = 90;
            $image->crop($width, $height, true);
            $image->save($new_image_path, IMAGETYPE_JPEG);
        } catch (ImageResizeException $e) {
            Log::error("Erro no resize da foto. " . $e->getMessage());
        }
        return $new_image_path;
    }

    static function getCompanyPhotoUrl(Company $company, int $width, int $height)
    {
        $file_id = 0;
        if($company->images->first()){
            $file_id = $company->images->first()->id;
        }
        return self::getImageUrl($file_id, $width, $height, $company->name);
    }

}
