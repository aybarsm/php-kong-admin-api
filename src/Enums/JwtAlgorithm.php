<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * JWT signing algorithms.
 *
 * Spec: `components.schemas.JWT.algorithm`, `components.schemas.JWTWithoutParents.algorithm`.
 */
enum JwtAlgorithm: string
{
    case Es256 = 'ES256';
    case Es256k = 'ES256K';
    case Es384 = 'ES384';
    case Es512 = 'ES512';
    case Esb256 = 'ESB256';
    case Esb320 = 'ESB320';
    case Esb384 = 'ESB384';
    case Esb512 = 'ESB512';
    case Esp256 = 'ESP256';
    case Esp384 = 'ESP384';
    case Esp512 = 'ESP512';
    case Ed25519 = 'Ed25519';
    case Ed448 = 'Ed448';
    case Eddsa = 'EdDSA';
    case Hs256 = 'HS256';
    case Hs384 = 'HS384';
    case Hs512 = 'HS512';
    case Ps256 = 'PS256';
    case Ps384 = 'PS384';
    case Ps512 = 'PS512';
    case Rs256 = 'RS256';
    case Rs384 = 'RS384';
    case Rs512 = 'RS512';
}
