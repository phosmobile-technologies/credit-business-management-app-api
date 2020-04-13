<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Errors\GraphqlError;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangePassword
{
    /**
     * Return a value for the field.
     *
     * @param  null $rootValue                                              Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[] $args                                                The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo $resolveInfo            Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphqlError
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user        = $args["user"];
        $args        = $args["data"];
        $oldPassword = $args["old_password"];
        $password    = $args["password"];

        /**
         * Throw an exception if the user passed in the wrong old_password
         */
        if (!Hash::check($oldPassword, $user->getAuthPassword())) {
            throw new GraphqlError("Wrong password provided", "The user provided a wrong password");
        }

        // Update the user's password
        $user->fill([
            'password' => Hash::make($password)
        ])->save();

        return $user;
    }
}
