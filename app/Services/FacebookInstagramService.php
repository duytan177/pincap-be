<?php

namespace App\Services;

use App\Enums\User\SocialType;
use App\Models\UserSocialAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookInstagramService
{
    protected string $shortLivedToken;
    protected string $longLivedToken;
    protected Carbon $longLivedTokenExpiresAt;
    protected string $baseUrl;


    public function __construct(string $shortLivedToken)
    {
        $this->shortLivedToken = $shortLivedToken;
        $this->baseUrl = config('services.facebook.base_url');
    }

    /**
     * Đổi short-lived token sang long-lived token
     */
    public function exchangeLongLivedToken(string $userId, string $socialId): void
    {
        $url = $this->baseUrl . config('services.facebook.exchange_url');
        $response = Http::get($url, [
            'grant_type' => config('services.facebook.grant_type'),
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $this->shortLivedToken,
        ])->json();

        $this->longLivedToken = $response['access_token'] ?? $this->shortLivedToken;
        $this->longLivedTokenExpiresAt = Carbon::now()->addSeconds($response['expires_in'] ?? 60 * 24 * 60 * 60);

        UserSocialAccount::UpdateOrCreate(
            [
                'user_id' => $userId,
                'social_id' => $socialId,
                'social_type' => SocialType::INSTAGRAM,
            ],
            [
                'access_token' => $response['access_token'],
                'access_token_expired' => Carbon::now()->addHour(),
                'refresh_token' => $this->longLivedToken,
                'refresh_token_expired' => $this->longLivedTokenExpiresAt,
            ]
        );
    }

    /**
     * Lấy danh sách page của user
     */
    public function getUserPages(): array
    {
        $url = $this->baseUrl . config('services.facebook.me_accounts');

        return Http::get($url, [
            'access_token' => $this->shortLivedToken,
        ])->json('data') ?? [];
    }

    /**
     * Lấy Instagram Business Account từ Page ID
     */
    public function getInstagramBusinessId(string $pageId): ?string
    {
        $url = "{$this->baseUrl}/{$pageId}";

        $response = Http::get($url, [
            'fields' => 'instagram_business_account,access_token',
            'access_token' => $this->shortLivedToken,
        ])->json();

        return data_get($response, 'instagram_business_account.id');
    }

    /**
     * Lấy thông tin chi tiết Instagram Business Account
     */
    public function getInstagramDetails(string $igBizId): array
    {
        $url = "{$this->baseUrl}/{$igBizId}";

        return Http::get($url, [
            'fields' => 'id,username,name,profile_picture_url,biography',
            'access_token' => $this->shortLivedToken,
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
        $url = "{$this->baseUrl}/{$igBizId}";
        $response = Http::get($url, [
            'fields' => "media.limit($limit){id,caption,media_type,media_url,permalink,children{media_type,media_url}}",
            'access_token' => $this->shortLivedToken,
        ])->json();

        return $response["media"] ?? [];
    }

    public function getInstagramMediaWithCursorAfter($after)
    {
        $response = Http::get($after)->json();
        return $response;
    }

    /**
     * Format response media + paging
     *
     * @param array $response Raw response từ API Instagram
     * @return array
     */
    public static function formatMedia(array $response): array
    {
        $media = data_get($response, 'data', []);
        $pagingCursors = data_get($response, 'paging.cursors', []);
        $previous = data_get($response, 'paging.previous', null);
        $next = data_get($response, 'paging.next', null);
        return [
            'data' => $media,
            'paging' => [
                'cursors' => $pagingCursors,
                "previous" => $previous ? Crypt::encryptString($previous) : null,
                'next' => $next ? Crypt::encryptString($next) : null,
            ],
        ];
    }

    /**
     * Lấy chi tiết media theo một ID
     *
     * @param string $mediaId
     * @return array|null
     */
    public function getMediaDetail(string $mediaId): ?array
    {
        try {
            $url = "{$this->baseUrl}/{$mediaId}";
            $response = Http::get($url, [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,username,children{id,media_type,media_url,permalink}',
                'access_token' => $this->shortLivedToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to fetch media {$mediaId}: {$response->status()} | {$response->body()}");
            return null;

        } catch (\Exception $e) {
            Log::error("Exception fetching media {$mediaId}: " . $e->getMessage());
            return null;
        }
    }
}
