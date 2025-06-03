<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Request\User;

use Hyperf\Validation\Request\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'fullName' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'document' => 'required|string|max:20',
            'type' => 'required|in:COMMON,MERCHANT',
            'password' => 'required|string|min:6',
            'balance' => 'nullable|integer|min:0',
        ];
    }
}
