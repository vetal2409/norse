<?php

################################################################################
#                              Definition                                      #
################################################################################

/**
 * @SWG\Tag(name="feed", description="Фиды."),
 *
 * @SWG\Definition(definition="feed", required={"title", "content","type", "url", "channel_ids"},
 *      @SWG\Property(property="title", type="string", description="Feed title"),
 *      @SWG\Property(property="content", type="string", description="Feed content"),
 *      @SWG\Property(property="link", type="string", description="link to feed"),
 *      @SWG\Property(property="url", type="string", description="Parsed from channel url"),
 *      @SWG\Property(property="type", type="string", enum={"rss", "twitter"}, description="Feed type"),
 *      @SWG\Property(property="channel_ids", type="array", description="Array ids channels."),
 * ),
 *
 * @SWG\Definition(definition="paginateFeed", required={},
 *         @SWG\Property(property="total", type="integer", format="int32", description="Total number of items being paginated"),
 *         @SWG\Property(property="per_page", type="integer", format="int32", description="Number of items shown per page"),
 *         @SWG\Property(property="current_page", type="integer", format="int32", description="Current page number"),
 *         @SWG\Property(property="last_page", type="integer", format="int32", description="Total number of pages"),
 *         @SWG\Property(property="next_page_url", type="string", description="URL to the next page"),
 *         @SWG\Property(property="prev_page_url", type="string", description="URL to the last page"),
 *         @SWG\Property(property="from", type="integer", format="int32", description="Number of the first item in the slice"),
 *         @SWG\Property(property="to", type="integer", format="int32", description="Number of the last item in the slice"),
 *         @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/feed"), description="Array of feed entries"),
 * ),
 *
 * @SWG\Definition(definition="filterFeed", required={"per_page", "current_page"},
 *      @SWG\Property(property="per_page", default="10", type="integer", format="int32", description="Number of items shown per page"),
 *      @SWG\Property(property="current_page", default="1", type="integer", format="int32", description="Current page number"),
 *
 *      @SWG\Property(property="title", type="string", description="Feed title"),
 *      @SWG\Property(property="content", type="string", description="Feed content"),
 *      @SWG\Property(property="link", type="string", description="link to feed"),
 *      @SWG\Property(property="url", type="string", description="Parsed from channel url"),
 *      @SWG\Property(property="type", type="string", enum={"rss", "twitter"}, description="Feed type"),
 *      @SWG\Property(property="channel_ids", type="array", description="Array ids channels."),
 * ),
 */

################################################################################
#                               Path                                           #
################################################################################

/**
 * @SWG\Post(path="/feed/list", tags={"feed"}, summary="feed.list", description="List feeds entries with pager.",
 * 	   @SWG\Parameter(name="body", in="body", description="Filter", @SWG\Schema(type="array", ref="#/definitions/filterFeed")),
 *
 *     @SWG\Response(response="200", description="List feed with pager", @SWG\Schema(ref="#/definitions/paginateFeed")),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Put(path="/feed/{id}", tags={"feed"}, summary="feed.update", description="Update feed entry.",
 * 	  @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *    @SWG\Parameter(name="body", in="body", description="feed object", required=true, @SWG\Schema(ref="#/definitions/feed")),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/feed")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Post(path="/feed/", tags={"feed"}, summary="feed.create", description="Create feed entry.",
 *      @SWG\Parameter(name="body", in="body", description="Created feed object", required=true, @SWG\Schema(type="array",ref="#/definitions/feed") ),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/feed")),
 *      @SWG\Response(response=400, description="Validation failed"),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Get(path="/feed/{id}", tags={"feed"}, summary="feed.view", description="View feed entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success", @SWG\Schema(ref="#/definitions/feed")),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Delete(path="/feed/{id}", tags={"feed"}, summary="feed.delete", description="Delete feed entry.",
 * 		@SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 * 		@SWG\Response(response=200, description="Success"),
 *      @SWG\Response(response=400, description="Invalid id supplied" ),
 *      @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 * @SWG\Patch(path="/feed/{id}", summary="feed.restore", description="Restore feed entry.", tags={"feed"},
 *     @SWG\Parameter(name="id", in="path", required=true, type="string", description="Unique key entry"),
 *
 *     @SWG\Response(response="200", description="feed has been restored"),
 *     @SWG\Response(response=400, description="Invalid id supplied" ),
 *     @SWG\Response(response=403, description="Forbidden"),
 *
 *     security={{"header_user": {}}},
 * ),
 *
 */
