<?php

test('multi-user-license/total-sub-users', function () {
    $response = $this->getJson('/api/metrics/multi-user-license/total-sub-users');

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()
        ->toBeGreaterThanOrEqual(20)
        ->toBeLessThanOrEqual(40);
});

test('multi-user-license/active-sub-users', function () {
    $from = now()->subMonths(2)->format('Y-m-d');
    $to = now()->format('Y-m-d');

    $response = $this->getJson('/api/metrics/multi-user-license/active-sub-users?from=' . $from . '&to=' . $to);

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(0);
});

test('multi-user-license/protocols-signed-by-sub-users', function () {
    $from = now()->subYear()->format('Y-m-d');
    $to = now()->format('Y-m-d');

    $response = $this->getJson('/api/metrics/multi-user-license/protocols-signed-by-sub-users?from=' . $from . '&to=' . $to);

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(0);
});

