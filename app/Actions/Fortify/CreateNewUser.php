<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $nameSplit = collect(explode(' ', $user->name));
        $customer = \Lunar\Models\Customer::create([
            // 'title' => 'Mr.',
            'first_name' => $nameSplit[0],
            'last_name' => $nameSplit->slice(1)->join(' '),
            // 'company_name' => 'Stark Enterprises',
            // 'vat_no' => null,
            // 'meta' => [
            //     'account_no' => 'TNYSTRK1234'
            // ],
        ]);

        $customer->users()->attach($user);

        return $user;
    }
}
