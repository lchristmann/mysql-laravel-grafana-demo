<?php

test('qes/total-unlocked-users', function () {
    $response = $this->getJson('/api/metrics/qes/total-unlocked-users');

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(10);
});

test('qes/active-users', function () {
    $from = now()->subYear()->format('Y-m-d');
    $to = now()->format('Y-m-d');

    $response = $this->getJson('/api/metrics/qes/active-users?from=' . $from . '&to=' . $to);

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(0);
});

test('qes/total-signed-protocols', function () {
    $response = $this->getJson('/api/metrics/qes/total-signed-protocols');

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(100);
});

test('qes/signed-protocols-over-time', function () {
    $from = now()->subMonths(6)->format('Y-m-d');
    $to = now()->format('Y-m-d');
    $groupBy = 'week';

    $response = $this->getJson("/api/metrics/qes/signed-protocols-over-time?from={$from}&to={$to}&group_by={$groupBy}");

    $response->assertOk();

    $data = $response->json();

    expect($data)->toBeArray()->not()->toBeEmpty();

    foreach ($data as $item) {
        expect($item)->toHaveKeys(['period', 'count'])
            ->and($item['count'])->toBeInt()->toBeGreaterThanOrEqual(0);
    }
});
