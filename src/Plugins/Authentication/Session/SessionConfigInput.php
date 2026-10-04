<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Typed `config` request body of a `session` plugin (Session; doc `Authentication/session.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config')]
final readonly class SessionConfigInput implements Input
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['secret'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'session';

    /**
     * @param int|float|null             $absoluteTimeout         The session cookie absolute timeout, in seconds. Default: `86400`.
     * @param string|null                $audience                The session audience, which is the intended target application. Default: `default`.
     * @param list<Bind>|null            $bind                    Bind the session to data acquired from the HTTP request or connection.
     * @param string|null                $cookieDomain            The domain with which the cookie is intended to be exchanged.
     * @param bool|null                  $cookieHttpOnly          Applies the `HttpOnly` tag so that the cookie is sent only to a server. Default: `true`.
     * @param string|null                $cookieName              The name of the cookie. Default: `session`.
     * @param string|null                $cookiePath              The resource in the host where the cookie is available. Default: `/`.
     * @param CookieSameSite|null        $cookieSameSite          Determines whether and how a cookie may be sent with cross-site requests. Default: `Strict`.
     * @param bool|null                  $cookieSecure            Applies the Secure directive so that the cookie may be sent to the server only with an encrypted request over… Default: `true`.
     * @param bool|null                  $hashSubject             Whether to hash or not the subject when store_metadata is enabled. Default: `false`.
     * @param int|float|null             $idlingTimeout           The session cookie idle time, in seconds. Default: `900`.
     * @param list<LogoutMethods>|null   $logoutMethods           A set of HTTP methods that the plugin will respond to. Default: `["DELETE", "POST"]`.
     * @param string|null                $logoutPostArg           The POST argument passed to logout requests. Default: `session_logout`.
     * @param string|null                $logoutQueryArg          The query argument passed to logout requests. Default: `session_logout`.
     * @param bool|null                  $readBodyForLogout       Default: `false`.
     * @param bool|null                  $remember                Enables or disables persistent sessions. Default: `false`.
     * @param int|float|null             $rememberAbsoluteTimeout The persistent session absolute timeout limit, in seconds. Default: `2592000`.
     * @param string|null                $rememberCookieName      Persistent session cookie name. Default: `remember`.
     * @param int|float|null             $rememberRollingTimeout  The persistent session rolling timeout window, in seconds. Default: `604800`.
     * @param list<RequestHeaders>|null  $requestHeaders          List of information to include, as headers, in the response to the downstream.
     * @param list<ResponseHeaders>|null $responseHeaders         List of information to include, as headers, in the response to the downstream.
     * @param int|float|null             $rollingTimeout          The session cookie rolling timeout, in seconds. Default: `3600`.
     * @param string|null                $secret                  The secret that is used in keyed HMAC generation.
     * @param int|float|null             $staleTtl                The duration, in seconds, after which an old cookie is discarded, starting from the moment when the session b… Default: `10`.
     * @param Storage|null               $storage                 Determines where the session data is stored. Default: `cookie`.
     * @param bool|null                  $storeMetadata           Whether to also store metadata of sessions, such as collecting data of sessions for a specific audience belon… Default: `false`.
     */
    public function __construct(
        public int|float|null $absoluteTimeout = null,
        public ?string $audience = null,
        public ?array $bind = null,
        public ?string $cookieDomain = null,
        public ?bool $cookieHttpOnly = null,
        public ?string $cookieName = null,
        public ?string $cookiePath = null,
        public ?CookieSameSite $cookieSameSite = null,
        public ?bool $cookieSecure = null,
        public ?bool $hashSubject = null,
        public int|float|null $idlingTimeout = null,
        public ?array $logoutMethods = null,
        public ?string $logoutPostArg = null,
        public ?string $logoutQueryArg = null,
        public ?bool $readBodyForLogout = null,
        public ?bool $remember = null,
        public int|float|null $rememberAbsoluteTimeout = null,
        public ?string $rememberCookieName = null,
        public int|float|null $rememberRollingTimeout = null,
        public ?array $requestHeaders = null,
        public ?array $responseHeaders = null,
        public int|float|null $rollingTimeout = null,
        #[SensitiveParameter]
        public ?string $secret = null,
        public int|float|null $staleTtl = null,
        public ?Storage $storage = null,
        public ?bool $storeMetadata = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'absolute_timeout' => $this->absoluteTimeout,
            'audience' => $this->audience,
            'bind' => Data::enumValues($this->bind),
            'cookie_domain' => $this->cookieDomain,
            'cookie_http_only' => $this->cookieHttpOnly,
            'cookie_name' => $this->cookieName,
            'cookie_path' => $this->cookiePath,
            'cookie_same_site' => $this->cookieSameSite?->value,
            'cookie_secure' => $this->cookieSecure,
            'hash_subject' => $this->hashSubject,
            'idling_timeout' => $this->idlingTimeout,
            'logout_methods' => Data::enumValues($this->logoutMethods),
            'logout_post_arg' => $this->logoutPostArg,
            'logout_query_arg' => $this->logoutQueryArg,
            'read_body_for_logout' => $this->readBodyForLogout,
            'remember' => $this->remember,
            'remember_absolute_timeout' => $this->rememberAbsoluteTimeout,
            'remember_cookie_name' => $this->rememberCookieName,
            'remember_rolling_timeout' => $this->rememberRollingTimeout,
            'request_headers' => Data::enumValues($this->requestHeaders),
            'response_headers' => Data::enumValues($this->responseHeaders),
            'rolling_timeout' => $this->rollingTimeout,
            'secret' => $this->secret,
            'stale_ttl' => $this->staleTtl,
            'storage' => $this->storage?->value,
            'store_metadata' => $this->storeMetadata,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
