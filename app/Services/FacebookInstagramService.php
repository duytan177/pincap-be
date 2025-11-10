<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class FacebookInstagramService
{
    protected string $shortLivedToken;
    protected string $longLivedToken;
    protected Carbon $longLivedTokenExpiresAt;

    public function __construct(string $shortLivedToken)
    {
        $this->shortLivedToken = $shortLivedToken;
        $this->exchangeLongLivedToken();
    }

    /**
     * Đổi short-lived token sang long-lived token
     */
    protected function exchangeLongLivedToken(): void
    {
        $response = Http::get(config('services.facebook.exchange_url'), [
            'grant_type' => config('services.facebook.grant_type'),
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $this->shortLivedToken,
        ])->json();

        $this->longLivedToken = $response['access_token'] ?? $this->shortLivedToken;
        $this->longLivedTokenExpiresAt = Carbon::now()->addSeconds($response['expires_in'] ?? 60 * 24 * 60 * 60);
    }

    /**
     * Lấy danh sách page của user
     */
    public function getUserPages(): array
    {
        return Http::get("https://graph.facebook.com/v23.0/me/accounts", [
            'access_token' => $this->longLivedToken,
        ])->json('data') ?? [];
    }

    /**
     * Lấy Instagram Business Account từ Page ID
     */
    public function getInstagramBusinessId(string $pageId): ?string
    {
        $response = Http::get("https://graph.facebook.com/v23.0/{$pageId}", [
            'fields' => 'instagram_business_account,access_token',
            'access_token' => $this->longLivedToken,
        ])->json();

        return data_get($response, 'instagram_business_account.id');
    }

    /**
     * Lấy thông tin chi tiết Instagram Business Account
     */
    public function getInstagramDetails(string $igBizId): array
    {
        return Http::get("https://graph.facebook.com/v23.0/{$igBizId}", [
            'fields' => 'id,username,name,profile_picture_url,biography',
            'access_token' => $this->longLivedToken,
        ])->json();
    }

    /**
     * Lấy token long-lived và thời hạn
     */
    public function getLongLivedToken(): array
    {
        return [
            'token' => $this->longLivedToken,
            'expired_at' => $this->longLivedTokenExpiresAt,
        ];
    }
    /**
     * Lấy media với cursor-based paging
     *
     * Nếu $after truyền vào null, dùng cursor lưu trong service
     */
    public function getInstagramMediaWithCursor(string $igBizId, int $limit = 20): ?array
    {

        $response = Http::get("https://graph.facebook.com/v21.0/{$igBizId}", [
            'fields' => "media.limit($limit){id,caption,media_type,media_url,permalink,children{media_type,media_url}}",
            'access_token' => $this->longLivedToken,
        ])->json();

        return $this->formatMedia($response["media"]);
    }

    public function getInstagramMediaWithCursorAfter($after)
    {
        $response = Http::get($after)->json();
        return $response;
    }

    /**
     * Format response media + paging, tự động mã hoá 'next' nếu có
     *
     * @param array $response Raw response từ API Instagram
     * @return array
     */
    public static function formatMedia(array $response): array
    {
        $media = data_get($response, 'data', []);
        $pagingCursors = data_get($response, 'paging.cursors', []);
        $nextUrl = data_get($response, 'paging.next', null);

        return [
            'data' => $media,
            'paging' => [
                'cursors' => $pagingCursors,
                'next' => $nextUrl ? Crypt::encryptString($nextUrl) : null,
            ],
        ];
    }
}
