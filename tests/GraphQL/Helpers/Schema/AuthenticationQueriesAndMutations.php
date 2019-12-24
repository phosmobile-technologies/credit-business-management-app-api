<?php

namespace Tests\GraphQL\Helpers\Schema;


/**
 * Class AuthenticationQueriesAndMutations
 *
 * This class defines the various possible AuthenticationQueriesAndMutations for graphQL testing
 *
 * @package Tests\GraphQL\Schema
 */
class AuthenticationQueriesAndMutations
{
    /**
     * Mutation used to login a user
     *
     * @return string
     */
    public static function login() {
        $loginMutation = '
            mutation($input: LoginInput!) {
              login(input: $input) {
                access_token
                refresh_token
                expires_in
                token_type
                user {
                  id
                  first_name
                  last_name
                  email
                }
              }
            }
        ';

        return $loginMutation;
    }

    /**
     * Mutation used to register a new user
     *
     * @return string
     */
    public static function register() {
        return '
            mutation register($input: RegisterInput) {
                register(input: $input) {
                    user {
                        first_name,
                        last_name,
                        username,
                        email
                    }
                }
            }
        ';
    }

}
