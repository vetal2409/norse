<?php

define('API_HOST', gethostname());


################################################################################
#                              API Information                                 #
################################################################################

/**
 * @SWG\SecurityScheme(securityDefinition="header_user", type="apiKey", in="header", name="X-Access-Token"),
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host="norse.loc",
 *     basePath="/en/v1",
 *     @SWG\Contact(email="vlad.tuznichenko@gmail.com"),
 *     @SWG\Info(
 *     version="0.0.1",
 *     title="Swagger Norse Digital REST API",
 *     description="The first version of the Norse Digital is an exciting step forward towards making it easier for users to have open access to their data. Build something great!
                    <br><br> <b>Structure URL</b>
                    <br> protocol://example.com/{language}/{version}/ ...
                    <br> - {language} - enum{ru|en|ua},
                    <br> - {version} - enum{v1},
                    <br><br> <b>Response status</b> <br>
                    <br>             500: Internal Server Error;
                    <br>             400: Bad Request;
                    <br>             401: Not authorized;
                    <br>             403: No access;
                    <br>             404: Record not found;
                    <br>             422: Validation Error;
                    <br><br> <b>Structure Response</b> <br>
                    <br> Every response is contained by an envelope. That is, each response has a
                    <br> predictable set of keys with which you can expect to interact:
                    <br>&nbsp;                {
                    <br>&nbsp;&nbsp;            success: bool,     - success or fail requested operation,
                    <br>&nbsp;&nbsp;            data: {},          - Response object,
                    <br>&nbsp;&nbsp;            error: null|string - Global error message,
                    <br>&nbsp;&nbsp;            token: null|string - Auth token current user
                    <br>&nbsp;                }",
 *     termsOfService="http://norse.loc/terms/"))
 */
