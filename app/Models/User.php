<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'customer_id', 'user_type', 'organization_id', 'name', 'country_code', 'mobile', 'email', 'email_verified_at', 'password', 'address', 'city', 'pincode', 'vehicle_assign_status', 'driving_licence_front', 'driving_licence_back', 'driving_licence_status', 'aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'aadhar_card_status', 'pan_card_front', 'pan_card_back', 'pan_card_status', 'current_address_proof_front', 'current_address_proof_back', 'current_address_proof_status', 'passbook_front', 'passbook_status', 'status', 'profile_image', 'profile_image_status', 'suspended_by', 'kyc_uploaded_at', 'kyc_verified_by', 'is_verified', 'kyc_verified_at', 'date_of_rejection', 'rejected_by', 'org_kyc_verified_at', 'org_kyc_verified_by', 'org_is_verified', 'org_date_of_rejection', 'org_rejected_by', 'remember_token', 'fcm_token', 'device_type'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function rejectedBy(){
        return $this->belongsTo(Admin::class, 'rejected_by', 'id');
  }
  public function userAddress(){
        return $this->hasMany(UserAddress::class);
  }
  public function latest_order(){
    return $this->hasOne(Order::class)->latest('id');
  }
  public function await_order(){
    return $this->hasOne(Order::class)
    ->whereIn('rent_status', ['active','ready to assign','suspended'])
    ->orderBy('id', 'DESC');
  }
  public function pending_order(){
    return $this->hasOne(Order::class)
    ->where('rent_status', 'pending')
    ->orderBy('id', 'DESC');
  }
  public function ready_to_assign_order(){
    return $this->hasOne(Order::class)
    ->where('payment_status', 'completed')
    ->where('rent_status', 'ready to assign')
    ->orderBy('id', 'DESC');
  }
  public function active_order(){
    return $this->hasOne(Order::class)
    ->where('payment_status', 'completed')
    ->where('rent_status', 'active')
    ->orderBy('id', 'DESC');
  }
  public function cancel_requested_order(){
    return $this->hasOne(Order::class)
    ->where('payment_status', 'completed')
    ->where('rent_status', 'active')
    ->where('cancel_request', 'Yes')
    ->orderBy('id', 'DESC');
  }
  public function accessToken()
  {
      return $this->hasOne(PersonalAccessToken::class, 'tokenable_id')->where('tokenable_type', User::class);
  }

  public function active_vehicle(){
    return $this->hasOne(AsignedVehicle::class)
    ->whereIn('status', ['assigned','overdue'])
    ->orderBy('id', 'DESC');
  }
  public function assigned_vehicle(){
    return $this->hasOne(AsignedVehicle::class)
    ->where('status', 'assigned')
    ->orderBy('id', 'DESC');
  }
  public function overdue_vehicle(){
    return $this->hasOne(AsignedVehicle::class)
    ->where('status', 'overdue')
    ->orderBy('id', 'DESC');
  }
  public function doc_logs(){
        return $this->hasMany(UserKycLog::class);
  }
  
  public function organization_details(){
      return $this->belongsTo(Organization::class, 'organization_id', 'id');
  }
}
