<?php

################################################################################
#                              Definition                                      #
################################################################################

/**
 * @SWG\Tag(name="auth", description="Auth."),
 *
 * @SWG\Definition(definition="model-login", required={"name"},
 *      @SWG\Property(property="login", type="string", required=true, description="Email"),
 *      @SWG\Property(property="password", type="string", required=true, description="Password"),
 * ),
 *
 */

################################################################################
#                               Path                                           #
################################################################################

/**
 * @SWG\Post(path="/auth/login", tags={"auth"}, summary="auth.login", description="User login.",
 *      @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(type="object",
 *              @SWG\Property(property="login", required=true, type="string",  description="Email"),
 *              @SWG\Property(property="password", required=true, type="string",  description="Password"),
 *      )),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/user")),
 *      @SWG\Response(response=422, description="Validation failed."),
 *      @SWG\Response(response=500, description="Server error."),
 * ),
 *
 * @SWG\Post(path="/auth/registration", tags={"auth"}, summary="auth.registration", description="Registration new user.",
 *       @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(type="object",
 *              @SWG\Property(property="first_name", type="string", description="First name"),
 *              @SWG\Property(property="middle_name", type="string", description="Middle name"),
 *              @SWG\Property(property="last_name", type="string", description="Last name"),
 *              @SWG\Property(property="email", type="string", description="Email"),
 *              @SWG\Property(property="password", required=true, type="string", enum={"login", "registration", "password_reset"},  description="Scenario act."),
 *              @SWG\Property(property="password_confirmation", required=true, type="string", enum={"login", "registration", "password_reset"},  description="Scenario act."),
 *       )),
 *      @SWG\Response(response=200, description="Success"),
 *      @SWG\Response(response=422, description="Validation failed."),
 *      @SWG\Response(response=500, description="Server error."),
 * ),
 *
 * @SWG\Get(path="/auth/on-reload", tags={"auth"}, summary="auth.onReload", description="Get User entry on reload page.",
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/user")),
 *      @SWG\Response(response=500, description="Server error."),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 */