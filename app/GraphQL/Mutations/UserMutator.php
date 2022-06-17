<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\Image;
use App\Models\SocialLogin;
use App\Models\User;
use App\Notifications\ResetPasswordtNotification;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


final class UserMutator
{
    public function register($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::create($args);
        $token = $user->createToken('auth_token')->plainTextToken;
        return  ['token' => $token, 'user' => $user];
    }

    function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::where('email', $args['email'])->firstOrFail();

        if (!Hash::check($args['password'], $user->password))
            throw new GraphQLException("invalid credentiels", "Invalid Input");

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    function logout($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $user->currentAccesstoken()->delete();

        return "logout successfuly";
    }

    public function forgotPassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::where('email', $args['email'])->firstOrFail();
        $count = DB::table('password_resets')
            ->where('email', $args["email"])
            ->whereDate('created_at', now())
            ->count();

        if ($count < 3) {
            $code = Str::random(6);

            $ip_adress = $context->request->ip();

            DB::table('password_resets')->insert([
                'email' => $args['email'],
                'code' => $code,
                'ip_adress' => $ip_adress,
                'user_agent' => $context->request->header('user-agent'),
                'created_at' => now(),
            ]);
            $user->notify(new ResetPasswordtNotification($code));

            return "Check your email";
        } else return "vous avez dépasser le nombre limite de changement de password .";
    }

    public function confirmCode($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updatePassword = DB::table('password_resets')
            ->where('code', $args['code'])->orderBy('created_at', 'desc')
            ->first();

        if ($args['email'] == $updatePassword->email) {
            if ($args['code'] != $updatePassword->code) {
                throw new Exception("Invalid confirmation code");
            } else return "You can move to next step";
        }

        return "Confirmation code not available for this user ";
    }

    public function resetPassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updatePassword = DB::table('password_resets')
            ->where([
                ['code', $args['code']],
                ['email', $args['email']]
            ])->orderBy('created_at', 'desc')
            ->first();
        $user = User::where('email', $updatePassword->email)->firstOrFail();
        $user->update($args);
        $user->save();

        return $user;
    }

    public function editProfil($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();
        $user->update($args['input']);
        $user->save();
        return $user;
    }

    public function editPassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        if ($user->password != null) {
            if (!(Hash::check($args['current_password'], $user->password)))

                throw new GraphQLException("error", "Your current password does not matches with the password.", "error");

            if (strcmp($args['current_password'], $args['password']) == 0)

                throw new GraphQLException("New Password cannot be same as your current password.", "error");
        }

        $user->update($args);
        $user->save();
        return $user;
    }


    public function updateUserImage($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->request->user();

        $file = $args['file'];

        $path = $file->storePublicly('avatars');

        $user->image()->create([
            'url' => $path
        ]);

        return $user;
    }

    public function loginSocial($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::when(array_key_exists('email', $args), function ($query) use($args) {
            $query->where('email', $args['email']);
        })
            ->when(array_key_exists('phone', $args), function ($query) use($args) {
                $query->orWhere('phone', $args['phone']);
            })
            ->whereHas('socialLogins', function ($query) use($args) {
                $query->where('provider_id', $args['provider_id']);
            })
            ->first();

        if (empty($user)) {
            $user = User::create($args);
            $user->socialLogins()->create($args);
            $file = $args['url'];
            $file = $file->storePublicly('avatars');
            $user->image()->create($args);
        }

        $token = $user->createToken($context->request->ip())->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }
}
