<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Class UserUpdateRequest
 * @package App\Http\Request
 *
 * @mixin User
 */
class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $updatedUserId = $this->route('user');

        return [
            'name' => [
                'string',
                'sometimes',
                'required',
                'max:255',
            ],

            'default_dashboard_uri' => [
                'nullable',
                'string',
                'sometimes',
                'max:255'
            ],

            'role_id' => Rule::when($updatedUserId !== $this->user()->id, [
                'sometimes',
                'integer',
                Rule::exists('roles', 'id'),
            ]),

            'printer_id' => [
                'sometimes',
                'numeric'
            ],

            'warehouse_id' => [
                'nullable',
                'exists:warehouses,id'
            ],

            'active_transaction_id' => [
                'nullable',
                'exists:transactions,id'
            ],
        ];
    }
}
