<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CartStoreRequest extends FormRequest
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
      'id' => 'required|exists:App\Models\Product,id',
      'qty' => 'required|integer',
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
      'id.required' => 'Produkt wird benötigt',
      'id.exists' => 'Kein Produkt mit dieser ID vorhanden',
      'qty.required' => 'Menge wird benötigt',
      'qty.integer' => 'Nur ganze Einheiten möglich',
    ];
  }
}
