# API Documentation <!-- omit in toc -->

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://app.getpostman.com/run-collection/28599065-6d8cc4dc-d71b-4d7c-bf60-d91052cc3a89?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D28599065-6d8cc4dc-d71b-4d7c-bf60-d91052cc3a89%26entityType%3Dcollection%26workspaceId%3D16642c6e-6a2d-4f9a-a351-8d01b09b6246)

You can also visit the public Postman collection via [this link](https://www.postman.com/lchristmann/workspace/lchristmann/collection/28599065-6d8cc4dc-d71b-4d7c-bf60-d91052cc3a89?action=share&creator=28599065).

## Table of Contents <!-- omit in toc -->

- [General Usage](#general-usage)
- [Endpoints](#endpoints)
  - [QES (Qualified Electronic Signature) Metrics](#qes-qualified-electronic-signature-metrics)
    - [1. GET /api/metrics/qes/total-unlocked-users](#1-get-apimetricsqestotal-unlocked-users)
    - [2. GET /api/metrics/qes/active-users](#2-get-apimetricsqesactive-users)
    - [3. GET /api/metrics/qes/total-signed-protocols](#3-get-apimetricsqestotal-signed-protocols)
    - [4. GET /api/metrics/qes/signed-protocols-over-time](#4-get-apimetricsqessigned-protocols-over-time)
  - [Valuation Metrics](#valuation-metrics)
    - [5. GET /api/metrics/valuation/total-unlocked-users](#5-get-apimetricsvaluationtotal-unlocked-users)
    - [6. GET /api/metrics/valuation/active-users](#6-get-apimetricsvaluationactive-users)
    - [7. GET /api/metrics/valuation/total-valuations](#7-get-apimetricsvaluationtotal-valuations)
    - [8. GET /api/metrics/valuation/valuations-over-time](#8-get-apimetricsvaluationvaluations-over-time)
  - [Multi-User-License Metrics](#multi-user-license-metrics)
    - [9. GET /api/metrics/multi-user-license/total-sub-users](#9-get-apimetricsmulti-user-licensetotal-sub-users)
    - [10. GET /api/metrics/multi-user-license/active-sub-users](#10-get-apimetricsmulti-user-licenseactive-sub-users)
    - [11. GET /api/metrics/multi-user-license/protocols-signed-by-sub-users](#11-get-apimetricsmulti-user-licenseprotocols-signed-by-sub-users)

## General Usage

Pass the following headers

- `Accept: application/json`


## Endpoints

### QES (Qualified Electronic Signature) Metrics

#### 1. GET /api/metrics/qes/total-unlocked-users

Returns the number of users that are currently unlocked for QES signing.

**Response:**

```json
{
    "count": 53
}
```

#### 2. GET /api/metrics/qes/active-users

Returns the number of active QES users:

- `unlocked_for_qes = true`
- `last_login` within the last 30 days
- Signed at least one protocol in the specified date range

**Query Parameters:**

| Name | Type   | Description           | Required |
|------|--------|-----------------------|----------|
| from | string | Start date (ISO 8601) | Yes      |
| to   | string | End date (ISO 8601)   | Yes      |

**Example Request:**

```
GET /api/metrics/qes/active-users?from=2025-04-01&to=2025-07-01
```

**Response:**

```json
{
    "count": 31
}
```

---

#### 3. GET /api/metrics/qes/total-signed-protocols

Returns total number of protocols signed with QES.

**Response:**

```json
{
    "count": 190
}
```

---

#### 4. GET /api/metrics/qes/signed-protocols-over-time

Returns how many protocols were signed with QES during a given time range, grouped by day, week, or month.

**Query Parameters:**

| Name     | Type   | Description                      | Required | Default |
|----------|--------|----------------------------------|----------|---------|
| from     | string | Start date (inclusive, ISO 8601) | Yes      |         |
| to       | string | End date (inclusive, ISO 8601)   | Yes      |         |
| group_by | string | `day`, `week`, or `month`        | No       | `day`   |

**Example Request:**

```
GET /api/metrics/qes/signed-protocols-over-time?from=2025-06-01&to=2025-07-01&group_by=week
```

**Example Response:**

```json
[
    {
        "period": "2025-W23",
        "count": 4
    },
    {
        "period": "2025-W24",
        "count": 4
    },
    {
        "period": "2025-W25",
        "count": 5
    },
    {
        "period": "2025-W26",
        "count": 5
    }
]
```

---

### Valuation Metrics

#### 5. GET /api/metrics/valuation/total-unlocked-users

Returns number of users unlocked for doing valuations.

**Response:**

```json
{
    "count": 58
}
```

---

#### 6. GET /api/metrics/valuation/active-users

Returns number of active users:

- `unlocked_for_valuation = true`
- `last_login` within the last 30 days
- Signed at least one valuation in the specified date range

**Query Parameters:**

| Name | Type   | Description           | Required |
|------|--------|-----------------------|----------|
| from | string | Start date (ISO 8601) | Yes      |
| to   | string | End date (ISO 8601)   | Yes      |

```
GET /api/metrics/valuation/active-users?from=2025-05-01&to=2025-07-01
```

**Response:**

```json
{
    "count": 27
}
```

---

#### 7. GET /api/metrics/valuation/total-valuations

Returns the total number of valuations ever created.

**Response:**

```json
{
    "count": 400
}
```

---

#### 8. GET /api/metrics/valuation/valuations-over-time

Returns number of valuations created over a given time period.

**Query Parameters:**

| Name     | Type   | Description                      | Required | Default |
|----------|--------|----------------------------------|----------|---------|
| from     | string | Start date (inclusive, ISO 8601) | Yes      |         |
| to       | string | End date (inclusive, ISO 8601)   | Yes      |         |
| group_by | string | `day`, `week`, or `month`        | No       | `day`   |

**Example Request:**

```
/api/metrics/valuation/valuations-over-time?from=2025-06-01&to=2025-08-01&group_by=month
```

**Example Response:**

```json
[
    {
        "period": "2025-06",
        "count": 47
    },
    {
        "period": "2025-07",
        "count": 20
    }
]
```

---

### Multi-User-License Metrics

#### 9. GET /api/metrics/multi-user-license/total-sub-users

Returns total number of sub-users (users with a `parent_user_id` set).

**Response:**

```json
{
    "count": 28
}
```

---

#### 10. GET /api/metrics/multi-user-license/active-sub-users

Returns number of sub-users who logged in during the given date range.

- `parent_user_id NOT NULL`
- active means `last_login` during the given time period

**Query Parameters:**

| Name | Type   | Description           | Required |
|------|--------|-----------------------|----------|
| from | string | Start date (ISO 8601) | Yes      |
| to   | string | End date (ISO 8601)   | Yes      |

**Example Request:**

```
GET /api/metrics/multi-user-license/active-sub-users?from=2024-04-01&to=2025-07-01
```

**Response:**

```json
{
    "count": 4
}
```

---

#### 11. GET /api/metrics/multi-user-license/protocols-signed-by-sub-users

Returns number of protocols signed with QES by any sub-users in a time range.

**Query Parameters:**

| Name | Type   | Description           | Required |
|------|--------|-----------------------|----------|
| from | string | Start date (ISO 8601) | Yes      |
| to   | string | End date (ISO 8601)   | Yes      |

**Example Request:**

```
GET /api/metrics/multi-user-license/protocols-signed-by-sub-users?from=2024-11-01&to=2025-04-01
```

**Response:**

```json
{
    "count": 23
}
```
