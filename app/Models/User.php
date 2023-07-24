<?php

namespace App\Models;

use App\Helpers\ContactsHelper;
use App\Helpers\DateHelper;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserPermissionsRequest;
use App\Services\AppSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property bool $backoffice_access
 * @property string $locale
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\UserLayoutSettings|null $userLayoutSettings
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBackofficeAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'contact_id',
        'name',
        'email',
        'password',
        'locale',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'backoffice_access' => 'boolean',
        'active' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $with = [
        'contact'
    ];

    protected $appends = [
        'profile_photo_small'
    ];

    /********************** RELACIONAMENTO COM OUTROS MODELS/TABELAS **********************/

    /**
     * Devolve as definições de utilizador
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userLayoutSettings()
    {
        return $this->hasOne(UserLayoutSettings::class);
    }

    /**
     * Devolve contacto associado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Devolve eventos do utilizador
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withPivot('status', 'notes');
    }

    /**
     * Devolve calendários do utilizador
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function calendars(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Calendar::class);
    }

    /**
     * Devolve calendários criados pelo utilizador
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function myCalendars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Calendar::class, 'user_id', 'id');
    }

    public function permissionRoles()
    {
        return $this->belongsToMany(PermissionRole::class, 'permission_role_users', 'user_id', 'permission_role_id')
            ->withTimestamps();
    }

    /******************************************************************************************************************/

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoSmallAttribute()
    {
        if($this->contact){
            return Cache::rememberForever('ProfilePhotoSmall_' . $this->contact->id, function () {
                return ContactsHelper::getContactProfilePhotoUrl($this->contact, 100, 100);
            });
        }
        return null;
    }

    public function killUserSessions()
    {
        return (bool)$this->sessions()->delete();
    }

    public function disableUser()
    {
        $this->active = false;
        return $this->save();
    }

    public function disableUsers(array $users_ids)
    {
        $disabled = false;
        if(!empty($users_ids)){
            foreach ($users_ids as $user_id) {
                $disabled = (new User())->find($user_id)->disableUser();
            }
        }
        return $disabled;
    }

    public function activateUser()
    {
        $this->active = true;
        return $this->save();
    }

    public function scopeActive(Builder $query){
        $query->where('active', 1);
    }

    public function scopeBackofficeAccess(Builder $query){
        $query->where('backoffice_access', 1);
    }

    public function getDepartments()
    {
        return $this->contact->companyDepartments;
    }

    /**
     * @throws \Throwable
     */
    public function createUser(StoreUserRequest $request, User $user)
    {
        try {

            DB::beginTransaction();

            $AppSettings = (new AppSettingsService())->getAppSettings();

            $user_name = $request->input('name');

            $user = $user->forceCreate([
                'name' => $user_name,
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'active' => $request->input('active'),
                'backoffice_access' => $request->input('backoffice_access'),
                'locale' => $AppSettings->locale,
                'timezone' => $AppSettings->timezone,
            ]);

            //Criação automática de perfil de contacto
            $contact = (new Contact())->createDefaultFromUser($user);
            $user->contact()->associate($contact);
            $user->save();

            DB::commit();

            return $user;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateUser(UpdateUserRequest $request)
    {
        try {

            DB::beginTransaction();

            $user_name = $request->input('name');

            $user_data = [
                'name' => $user_name,
                'email' => $request->input('email'),
                'active' => $request->input('active'),
                'backoffice_access' => $request->input('backoffice_access'),
            ];

            if(!empty($request->input('password'))){
                $user_data['password'] =  Hash::make($request->input('password'));
            }

            $this->forceFill($user_data)->save();

            //Caso ainda não exista contacto
            if(!$this->contact){
                //Criação automática de perfil de contacto
                $contact = (new Contact())->createDefaultFromUser($this);
                $this->contact()->associate($contact);
                $this->save();
            }

            //Actualizar nome de perfil (contacto)
            $this->contact->updateNames($user_name);

            DB::commit();

            return $this;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    public function scopeFilter(Builder $query, object $search_params)
    {
        if($search_params->global->value != null) {
            $query->where(function ($query) use ($search_params) {
                $query->where('name', 'LIKE', '%' . $search_params->global->value . '%')
                    ->orWhere('email', 'LIKE', '%' . $search_params->global->value . '%');
            });
        }

        if(isset($search_params->name) AND $search_params->name->value != null) {
            $query->where('name', 'LIKE', '%' . $search_params->name->value . '%');
        }

        if(isset($search_params->email) AND $search_params->email->value != null) {
            $query->where('email', 'LIKE', '%' . $search_params->email->value . '%');
        }

        if(isset($search_params->active) AND $search_params->active->value !== null) {
            if($search_params->active->value === true){
                $query->active();
            }else{
                $query->where('active', 0);

            }
        }

        if(isset($search_params->backoffice_access) AND $search_params->backoffice_access->value !== null) {
            if($search_params->backoffice_access->value === true){
                $query->backofficeaccess();
            }else{
                $query->where('backoffice_access', 0);
            }
        }
    }

    public static function getList(object $datatable_params)
    {

        $filters = $datatable_params->filters;

        $totalRecords = User::filter($filters)->count();

        $user = User::with('contact.images')->filter($filters);

        $user->select([
            'id', 'name', 'email', 'backoffice_access', 'active', 'updated_at', 'contact_id'
        ]);

        $user->withMax('sessions as last_activity', 'last_activity');

        $sortField = (empty($datatable_params->sortField) ? 'id' : $datatable_params->sortField);
        $sortOrder = (empty($datatable_params->sortOrder) ? 'asc' : ($datatable_params->sortOrder == 1 ? 'asc' : 'desc'));

        $users = $user
            ->offset($datatable_params->first)
            ->limit($datatable_params->rows)
            ->orderBy($sortField, $sortOrder)
            ->get()
            ->map(function ($row) {
                if ($row->last_activity != null) {
                    $row->last_activity = DateHelper::localDate($row->last_activity);
                }
                if ($row->updated_at != null) {
                    $row->updated_at = DateHelper::localDate($row->updated_at);
                }

                $row->profile_photo_360x360 = ContactsHelper::getContactProfilePhotoUrl($row->contact, 360, 360);
                $row->profile_photo_470x470 = ContactsHelper::getContactProfilePhotoUrl($row->contact, 470, 470);

                return $row;
            })->toArray();

        return array(
            "rows" => $users,
            "totalRecords" => $totalRecords,
        );
    }

    public static function getListOnlineUsers(object $datatable_params)
    {

        $filters = $datatable_params->filters;

        $user = User::filter($filters);
        $user->has('sessions');
        $totalRecords = $user->count();

        $user = User::with('contact.images')->filter($filters);
        $user->has('sessions');
        $user->select([
            'id', 'name', 'email', 'contact_id'
        ]);

        $user->withMax('sessions as last_activity', 'last_activity');

        $sortField = (empty($datatable_params->sortField) ? 'id' : $datatable_params->sortField);
        $sortOrder = (empty($datatable_params->sortOrder) ? 'asc' : ($datatable_params->sortOrder == 1 ? 'asc' : 'desc'));

        $users = $user
            ->offset($datatable_params->first)
            ->limit($datatable_params->rows)
            ->orderBy($sortField, $sortOrder)
            ->get()
            ->map(function ($row) {
                if ($row->last_activity != null) {
                    $row->last_activity = DateHelper::localDate($row->last_activity);
                }
                if ($row->updated_at != null) {
                    $row->updated_at = DateHelper::localDate($row->updated_at);
                }
                return $row;
            })->toArray();

        return array(
            "rows" => $users,
            "totalRecords" => $totalRecords,
        );
    }

}
