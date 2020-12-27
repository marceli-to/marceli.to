<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
      'title.de' => 'required',
      'price'    => 'required',
      'stock'    => 'required|integer',
      'shipping_region_1' => 'required',
      'shipping_region_2' => 'required',
      'shipping_region_3' => 'required',
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
      'title.de.required' => [
        'field' => 'title',
        'error' => 'Titel wird benötigt!'
      ],
      'price.required' => [
        'field' => 'price',
        'error' => 'Preis wird benötigt!'
      ],
      'stock.required' => [
        'field' => 'stock',
        'error' => 'Verfügbare Menge wird benötigt!'
      ],
      'stock.integer' => [
        'field' => 'stock',
        'error' => 'Verfügbare Menge muss eine Zahl sein!'
      ],
      'shipping_region_1.required' => [
        'field' => 'shipping_region_1',
        'error' => 'Versandkosten CH werden benötigt!'
      ],
      'shipping_region_2.required' => [
        'field' => 'shipping_region_2',
        'error' => 'Versandkosten Europa werden benötigt!'
      ],
      'shipping_region_3.required' => [
        'field' => 'shipping_region_3',
        'error' => 'Versandkosten übrige werden benötigt!'
      ],
    ];
  }
}
