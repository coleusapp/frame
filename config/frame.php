<?php

use Coleus\Frame\Sources\ArtInstituteOfChicagoSource;
use Coleus\Frame\Sources\ClevelandMuseumOfArtSource;
use Coleus\Frame\Sources\MetropolitanMuseumOfArtSource;
use Coleus\Frame\Sources\NationalGalleryOfDenmarkSource;

return [

    /*
     * Prefix for the Frame tables (frame_frames, frame_artworks).
     *
     * Default: frame_
     */
    'table_prefix' => 'frame_',

    /*
     * The URL prefix of the kiosk and management pages.
     *
     * Default: frame
     */
    'route_prefix' => 'frame',

    /*
     * Per-IP throttle on the kiosk pages, as "requests,minutes".
     *
     * Default: 120,1
     */
    'kiosk_rate_limit' => '120,1',

    /*
     * Where new artworks come from: classes implementing
     * Coleus\Frame\Contracts\ArtworkSource. Every fetch asks each of them.
     *
     * Default: the Art Institute of Chicago, The Met, the Cleveland Museum of Art
     * and SMK (National Gallery of Denmark).
     */
    'sources' => [
        ArtInstituteOfChicagoSource::class,
        MetropolitanMuseumOfArtSource::class,
        ClevelandMuseumOfArtSource::class,
        NationalGalleryOfDenmarkSource::class,
    ],

    /*
     * Art Institute of Chicago API and IIIF image server.
     */
    'aic' => [
        'api_url' => 'https://api.artic.edu/api/v1',
        'iiif_url' => 'https://www.artic.edu/iiif/2',
        'user_agent' => 'coleus-frame (personal project)',
    ],

    /*
     * The Metropolitan Museum of Art Collection API.
     */
    'met' => [
        'api_url' => 'https://collectionapi.metmuseum.org/public/collection',
    ],

    /*
     * Cleveland Museum of Art Open Access API.
     */
    'cleveland' => [
        'api_url' => 'https://openaccess-api.clevelandart.org/api',
    ],

    /*
     * SMK (National Gallery of Denmark) Open API.
     */
    'smk' => [
        'api_url' => 'https://api.smk.dk/api/v1',
    ],

    /*
     * Default for a new frame's display size: the largest size its stored
     * artworks are fitted into (never cropped or upscaled). Each frame can
     * change its own.
     */
    'display' => [
        'width' => 1920,
        'height' => 1080,
    ],

    /*
     * Private disk the resized artworks are stored on.
     *
     * Default: local
     */
    'disk' => 'local',

    /*
     * Artworks kept per frame; the oldest are pruned after each fetch.
     *
     * Default: 200
     */
    'pool_size' => 200,
];
