<?php

declare(strict_types=1);

use Geocoder\Collection;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
final class YoApi extends AbstractHttpProvider implements Provider
{

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        // TODO: Implement geocodeQuery() method.
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        // TODO: Implement reverseQuery() method.
    }

    public function getName(): string
    {
        return 'yoApi';
    }
}