<?php

test('valuation/total-unlocked-users', function () {
    $response = $this->getJson('/api/metrics/valuation/total-unlocked-users');

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(10);
});

test('valuation/active-users', function () {
    $from = now()->subYear()->format('Y-m-d');
    $to = now()->format('Y-m-d');

    $response = $this->getJson("/api/metrics/valuation/active-users?from={$from}&to={$to}");

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBeGreaterThanOrEqual(0);
});

test('valuation/total-valuations', function () {
    $response = $this->getJson('/api/metrics/valuation/total-valuations');

    $response->assertOk()->assertJsonStructure(['count']);

    $count = $response->json('count');

    expect($count)->toBeInt()->toBe(400);
});

test('valuation/valuations-over-time', function () {
    $from = now()->subMonths(6)->format('Y-m-d');
    $to = now()->format('Y-m-d');
    $groupBy = 'month';

    $response = $this->getJson("/api/metrics/valuation/valuations-over-time?from={$from}&to={$to}&group_by={$groupBy}");

    $response->assertOk();

    $data = $response->json();

    expect($data)->toBeArray()->not()->toBeEmpty();

    foreach ($data as $item) {
        expect($item)->toHaveKeys(['period', 'count'])
            ->and($item['count'])->toBeInt()->toBeGreaterThanOrEqual(0);
    }
});
