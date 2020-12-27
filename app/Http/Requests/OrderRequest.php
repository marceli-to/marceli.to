<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required',
      'firstname' => 'required',
      'street' => 'required',
      'zip' => 'required',
      'city' => 'required',
      'country_id' => 'required',
      'phone' => 'required',
      'email' => 'required|email:filter',
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages()
  {
    return [
      'name.required' => 'Name muss ausgefüllt sein.',
      'firstname.required' => 'Vorname muss ausgefüllt sein.',
      'street.required' => 'Strasse muss ausgefüllt sein.',
      'zip.required' => 'PLZ muss ausgefüllt sein.',
      'city.required' => 'Ort muss ausgefüllt sein.',
      'country.required' => 'Land muss ausgefüllt sein.',
      'phone.required' => 'Telefon muss ausgefüllt sein.',
      'email.required' => 'E-Mail muss ausgefüllt sein.',
      'email.email' => 'E-Mail ist ungültig.',
      // 'alt_city.required_if' => 'Ort muss ausgefüllt sein.',
      // 'alt_zip.required_if' => 'Ort muss ausgefüllt sein.',
      // 'alt_name.required_if' => 'Name muss ausgefüllt sein.',
      // 'alt_firstname.required_if' => 'Vorname muss ausgefüllt sein.',
      // 'alt_street.required_if' => 'Strasse muss ausgefüllt sein.'
    ];
  }
}
