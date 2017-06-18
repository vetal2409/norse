<?php

################################################################################
#                              Definition                                      #
################################################################################


/**
 * @SWG\Tag(name="channel", description="Каналы."),
 *
 * @SWG\Definition(definition="channel", required={"title", "content","type", "url", "channel_ids"},
 *      @SWG\Property(property="name", type="string", description="Name"),
 *      @SWG\Property(property="type", type="string", enum={"rss", "twitter"}, description="Type"),
 *      @SWG\Property(property="url", type="string", description="URL"),
 * ),
 *
 * @SWG\Definition(definition="paginateChannel", required={},
 *         @SWG\Property(property="total", type="integer", format="int32", description="Total number of items being paginated"),
 *         @SWG\Property(property="per_page", type="integer", format="int32", description="Number of items shown per page"),
 *         @SWG\Property(property="current_page", type="integer", format="int32", description="Current page number"),
 *         @SWG\Property(property="last_page", type="integer", format="int32", description="Total number of pages"),
 *         @SWG\Property(property="next_page_url", type="string", description="URL to the next page"),
 *         @SWG\Property(property="prev_page_url", type="string", description="URL to the last page"),
 *         @SWG\Property(property="from", type="integer", format="int32", description="Number of the first item in the slice"),
 *         @SWG\Property(property="to", type="integer", format="int32", description="Number of the last item in the slice"),
 *         @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/channel"), description="Array of channel entries"),
 * ),
 *
 * @SWG\Definition(definition="filterChannel", required={"per_page", "current_page"},
 *      @SWG\Property(property="per_page", default="10", type="integer", format="int32", description="Number of items shown per page"),
 *      @SWG\Property(property="current_page", default="1", type="integer", format="int32", description="Current page number"),
 *
 *      @SWG\Property(property="name", type="string", description="Name"),
 *      @SWG\Property(property="type", type="string", enum={"rss", "twitter"}, description="Type"),
 *      @SWG\Property(property="url", type="string", description="URL"),
 * ),
 */

################################################################################
#                               Path                                           #
################################################################################

/**
 * @SWG\Post(path="/channel/list", tags={"channel"}, summary="channel.list", description="List channels entries with pager.",
 * 	   @SWG\Parameter(name="body", in="body", description="Filter", @SWG\Schema(type="array", ref="#/definitions/filterChannel")),
 *
 *     @SWG\Response(response="200", description="List channel with pager", @SWG\Schema(ref="#/definitions/paginateChannel")),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Put(path="/channel/{id}", tags={"channel"}, summary="channel.update", description="Update channel entry.",
 * 	  @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *    @SWG\Parameter(name="body", in="body", description="channel object", required=true, @SWG\Schema(ref="#/definitions/channel")),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/channel")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Post(path="/channel/", tags={"channel"}, summary="channel.create", description="Create channel entry.",
 *      @SWG\Parameter(name="body", in="body", description="Created channel object", required=true, @SWG\Schema(type="array",ref="#/definitions/channel") ),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/channel")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Get(path="/channel/{id}", tags={"channel"}, summary="channel.view", description="View channel entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/channel")),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Delete(path="/channel/{id}", tags={"channel"}, summary="channel.delete", description="Delete channel entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success"),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Patch(path="/channel/{id}", summary="channel.restore", description="Restore channel entry.", tags={"channel"},
 *     @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 *     @SWG\Response(response="200", description="channel has been restored"),
 *     @SWG\Response(response=400, description="Invalid id supplied" ),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 */
