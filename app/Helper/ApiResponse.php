<?php

namespace App\Helper;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * تنسيق موحّد لاستجابات الـ API (JSON).
 *
 * الشكل:
 * - is_success: نجاح العملية من عدمه
 * - data: الحمولة (مصفوفة، كائن، قيمة واحدة، أو null)
 * - meta: معلومات الترقيم وغيرها (قيم null عند عدم التطبيق)
 * - message: رسالة عامة للمستخدم أو التطبيق
 * - errors: أخطاء الحقول (null عند النجاح)، مثال: ['name' => 'Name is required']
 */
final class ApiResponse
{
    /**
     * هيكل meta الافتراضي عندما لا يوجد ترقيم أو بيانات وصفية إضافية.
     *
     * @return array{current_page: int|null, last_page: int|null, per_page: int|null, total: int|null, count: int|null}
     */
    public static function defaultMeta(): array
    {
        return [
            'current_page' => null,
            'last_page' => null,
            'per_page' => null,
            'total' => null,
            'count' => null,
        ];
    }

    /**
     * بناء meta من نتيجة Laravel paginator (LengthAwarePaginator).
     *
     * - total: إجمالي السجلات عبر كل الصفحات
     * - count: عدد العناصر في الصفحة الحالية فقط
     */
    public static function metaFromPaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'count' => $paginator->count(),
        ];
    }

    /**
     * دمج أي مفاتيح meta إضافية فوق الافتراضي (بدون فقدان المفاتيح الموحّدة).
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function mergeMeta(array $overrides): array
    {
        return array_merge(self::defaultMeta(), $overrides);
    }

    /**
     * استجابة JSON بالشكل الموحّد.
     *
     * @param  mixed  $data
     * @param  array<string, mixed>|null  $meta  يُدمَج مع defaultMeta؛ null = استخدام الافتراضي فقط
     * @param  string|null  $message
     * @param  array<string, mixed>|null  $errors  أخطاء الحقول؛ مثال ['name' => 'الحقل مطلوب']
     */
    public static function make(
        bool $isSuccess,
        mixed $data = null,
        ?array $meta = null,
        ?string $message = null,
        ?array $errors = null,
        int $status = 200,
    ): JsonResponse {
        $metaPayload = $meta === null
            ? self::defaultMeta()
            : self::mergeMeta($meta);

        return response()->json([
            'is_success' => $isSuccess,
            'data' => $data,
            'meta' => $metaPayload,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * استجابة نجاح.
     *
     * @param  mixed  $data
     * @param  array<string, mixed>|null  $meta
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        ?array $meta = null,
        int $status = 200,
    ): JsonResponse {
        return self::make(
            isSuccess: true,
            data: $data,
            meta: $meta,
            message: $message,
            errors: null,
            status: $status,
        );
    }

    /**
     * استجابة فشل (تحقق، منطق أعمال، موارد غير موجودة، …).
     *
     * @param  array<string, mixed>|null  $errors
     * @param  mixed  $data
     */
    public static function error(
        ?string $message = null,
        ?array $errors = null,
        int $status = 422,
        mixed $data = null,
    ): JsonResponse {
        return self::make(
            isSuccess: false,
            data: $data,
            meta: null,
            message: $message,
            errors: $errors,
            status: $status,
        );
    }

    /**
     * نجاح مع بيانات مُرقّمة (صفحات).
     *
     * @param  mixed  $items  عادةً مصفوفة من الموديلات/المصفوفات؛ يُمرَّر كـ data
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        mixed $items = null,
        ?string $message = null,
        int $status = 200,
    ): JsonResponse {
        $payload = $items ?? $paginator->items();

        return self::success(
            data: $payload,
            message: $message,
            meta: self::metaFromPaginator($paginator),
            status: $status,
        );
    }

    /**
     * تحويل أخطاء Laravel Validator (رسائل متعددة لكل حقل) إلى أول رسالة لكل حقل
     * لتقترب من الشكل: { "name": "Name is required" }.
     *
     * @param  array<string, array<int, string>|string>  $messages
     * @return array<string, string>
     */
    public static function flattenValidationMessages(array $messages): array
    {
        $out = [];

        foreach ($messages as $field => $msg) {
            if (is_array($msg)) {
                $out[$field] = $msg[0] ?? '';
            } else {
                $out[$field] = $msg;
            }
        }

        return $out;
    }
}
