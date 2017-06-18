<?php

################################################################################
#                              Definition                                      #
################################################################################

/**
 * @SWG\Tag(name="user", description="Пользователи."),
 *
 * @SWG\Definition(definition="user", required={"first_name", "middle_name","last_name", "email", "phone", "role"},
 *      @SWG\Property(property="_scenario", type="string", enum={"password_change", "device_upsert"}, description="Set 'password_change' scenario if changing password"),
 *      @SWG\Property(property="first_name", type="string", description="First name"),
 *      @SWG\Property(property="middle_name", type="string", description="Middle name"),
 *      @SWG\Property(property="last_name", type="string", description="Last name"),
 *      @SWG\Property(property="email", type="string", description="Email"),
 *      @SWG\Property(property="role", type="string", enum={"user", "admin"}, description="Role"),
 *
 *      @SWG\Property(property="password", type="string", description="Set password when you change them or create user _scenario = password_change | create "),
 *      @SWG\Property(property="old_password", type="string", description="Old password if changing password _scenario = password_change | create "),
 *      @SWG\Property(property="password_confirmation", type="string", description="Confirmed password if changing password _scenario = password_change | create "),
 * ),
 *
 * @SWG\Definition(definition="paginateUser", required={},
 *         @SWG\Property(property="total", type="integer", format="int32", description="Total number of items being paginated"),
 *         @SWG\Property(property="per_page", type="integer", format="int32", description="Number of items shown per page"),
 *         @SWG\Property(property="current_page", type="integer", format="int32", description="Current page number"),
 *         @SWG\Property(property="last_page", type="integer", format="int32", description="Total number of pages"),
 *         @SWG\Property(property="next_page_url", type="string", description="URL to the next page"),
 *         @SWG\Property(property="prev_page_url", type="string", description="URL to the last page"),
 *         @SWG\Property(property="from", type="integer", format="int32", description="Number of the first item in the slice"),
 *         @SWG\Property(property="to", type="integer", format="int32", description="Number of the last item in the slice"),
 *         @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/user"), description="Array of user entries"),
 * ),
 *
 * @SWG\Definition(definition="filterUser", required={"per_page", "current_page"},
 *      @SWG\Property(property="per_page", default="10", type="integer", format="int32", description="Number of items shown per page"),
 *      @SWG\Property(property="current_page", default="1", type="integer", format="int32", description="Current page number"),
 *
 *      @SWG\Property(property="_scenario", type="string", enum={"password_change", "device_upsert"}, description="Set 'password_change' scenario if changing password"),
 *      @SWG\Property(property="first_name", type="string", description="First name"),
 *      @SWG\Property(property="middle_name", type="string", description="Middle name"),
 *      @SWG\Property(property="last_name", type="string", description="Last name"),
 *      @SWG\Property(property="email", type="string", description="Email"),
 *      @SWG\Property(property="role", type="string", enum={"user", "admin"}, description="Role"),
 *),
 */

################################################################################
#                               Path                                           #
################################################################################

/**
 * @SWG\Post(path="/user/list", tags={"user"}, summary="user.list", description="List users entries with pager.",
 * 	   @SWG\Parameter(name="body", in="body", description="Filter", @SWG\Schema(type="array", ref="#/definitions/filterUser")),
 *
 *     @SWG\Response(response="200", description="List user with pager", @SWG\Schema(ref="#/definitions/paginateUser")),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Put(path="/user/{id}", tags={"user"}, summary="user.update", description="Update user entry.",
 * 	  @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *    @SWG\Parameter(name="body", in="body", description="user object", required=true, @SWG\Schema(ref="#/definitions/user")),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/user")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Post(path="/user/", tags={"user"}, summary="user.create", description="Create user entry.",
 *      @SWG\Parameter(name="body", in="body", description="Created user object", required=true, @SWG\Schema(type="array",ref="#/definitions/user") ),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/user")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Get(path="/user/{id}", tags={"user"}, summary="user.view", description="View user entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/user")),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Delete(path="/user/{id}", tags={"user"}, summary="user.delete", description="Delete user entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success"),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Patch(path="/user/{id}", summary="user.restore", description="Restore user entry.", tags={"user"},
 *     @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 *     @SWG\Response(response="200", description="user has been restored"),
 *     @SWG\Response(response=400, description="Invalid id supplied" ),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 */
