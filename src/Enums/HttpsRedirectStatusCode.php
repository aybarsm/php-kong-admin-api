<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Status code Kong answers with when a Route requires HTTPS.
 *
 * Spec: `components.schemas.RouteJson.https_redirect_status_code`, `components.schemas.RouteExpression.https_redirect_status_code`.
 */
enum HttpsRedirectStatusCode: int
{
    case MovedPermanently = 301;
    case Found = 302;
    case TemporaryRedirect = 307;
    case PermanentRedirect = 308;
    case UpgradeRequired = 426;
}
