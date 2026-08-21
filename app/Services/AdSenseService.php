<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\AdSlot;

/**
 * AdSense Service
 * Centralized service for rendering Google AdSense ads.
 * All configuration is read from the settings table — nothing hardcoded.
 */
class AdSenseService
{
    private static ?self $instance = null;
    private Setting $settings;
    private AdSlot $adSlot;
    private array $cache = [];

    public function __construct()
    {
        $this->settings = new Setting();
        $this->adSlot = new AdSlot();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get a cached setting value
     */
    private function getSetting(string $key, mixed $default = null): mixed
    {
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $this->settings->get($key, $default);
        }
        return $this->cache[$key];
    }

    /**
     * Check if AdSense is globally enabled
     */
    public function isEnabled(): bool
    {
        return (int) $this->getSetting('adsense_enabled', '0') === 1;
    }

    /**
     * Get the Publisher ID
     */
    public function getPublisherId(): string
    {
        return trim((string) $this->getSetting('adsense_publisher_id', ''));
    }

    /**
     * Check if Auto Ads is enabled
     */
    public function isAutoAdsEnabled(): bool
    {
        return (int) $this->getSetting('adsense_auto_ads', '0') === 1;
    }

    /**
     * Check if a specific placement is enabled in settings
     */
    public function isPlacementEnabled(string $slotKey): bool
    {
        $settingKey = 'adsense_' . strtolower($slotKey);
        return (int) $this->getSetting($settingKey, '0') === 1;
    }

    /**
     * Get the AdSense configuration status
     */
    public function getStatus(): string
    {
        $pubId = $this->getPublisherId();
        if (empty($pubId)) {
            return 'not_configured';
        }
        if (!$this->isEnabled()) {
            return 'configured_disabled';
        }
        return 'configured_enabled';
    }

    /**
     * Get the status label for display
     */
    public function getStatusLabel(): string
    {
        return match ($this->getStatus()) {
            'not_configured' => 'Not Configured',
            'configured_disabled' => 'Configured (Disabled)',
            'configured_enabled' => 'Configured (Enabled)',
            default => 'Unknown',
        };
    }

    /**
     * Check if ads should be shown for a specific slot
     * Rules: AdSense enabled AND publisher ID set AND placement enabled AND slot enabled
     */
    public function shouldShowAd(string $slotKey): bool
    {
        // Master check
        if (!$this->isEnabled()) {
            return false;
        }

        // Publisher ID must be set
        if (empty($this->getPublisherId())) {
            return false;
        }

        // Check placement setting
        if (!$this->isPlacementEnabled($slotKey)) {
            return false;
        }

        // Check individual slot in ad_slots table
        if (!$this->adSlot->isEnabled($slotKey)) {
            return false;
        }

        // Respect Package Tier (Pro / Enterprise users don't see dashboard ads)
        if ($slotKey === 'DASHBOARD' && \App\Core\Auth::check()) {
            $user = \App\Core\Auth::user();
            if ($user && !empty($user->plan) && strcasecmp($user->plan, 'Gratis') !== 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate the AdSense head script tag
     * Should be placed in <head> once per page
     */
    public function getHeadScript(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $publisherId = $this->getPublisherId();
        if (empty($publisherId)) {
            return '';
        }

        $publisherId = htmlspecialchars($publisherId, ENT_QUOTES, 'UTF-8');
        $script = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . $publisherId . '" crossorigin="anonymous"></script>';

        // Auto Ads
        if ($this->isAutoAdsEnabled()) {
            $script .= "\n" . '<meta name="google-adsense-account" content="' . $publisherId . '">';
        }

        return $script;
    }

    /**
     * Render an ad for a specific slot
     * If real ads are configured, renders the ad code.
     * Otherwise renders a clean placeholder slot so the layout is preserved.
     */
    public function renderAd(string $slotKey): string
    {
        try {
            $slot = $this->adSlot->getByKey($slotKey);
            $slotName = $slot ? ($slot->name ?? $slotKey) : $slotKey;

            // If real ads are configured and active
            if ($this->shouldShowAd($slotKey)) {
                if ($slot && !empty($slot->ad_code)) {
                    return $this->wrapAdContainer($slot->ad_code, $slotKey);
                }

                $publisherId = htmlspecialchars($this->getPublisherId(), ENT_QUOTES, 'UTF-8');
                $adHtml = '<ins class="adsbygoogle"'
                    . ' style="display:block"'
                    . ' data-ad-client="' . $publisherId . '"'
                    . ' data-ad-slot="auto"'
                    . ' data-ad-format="auto"'
                    . ' data-full-width-responsive="true"></ins>'
                    . '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';

                return $this->wrapAdContainer($adHtml, $slotKey);
            }

            // Always render clean, elegant placeholder slot so user can preview ad placement
            return $this->renderPlaceholder($slotKey, $slotName);
        } catch (\Throwable $e) {
            // Graceful fallback — never break the page
            return '';
        }
    }

    /**
     * Render a clean ad placeholder banner
     */
    private function renderPlaceholder(string $slotKey, string $slotName): string
    {
        $slotKeyAttr = htmlspecialchars(strtolower($slotKey), ENT_QUOTES, 'UTF-8');
        $slotTitle = htmlspecialchars($slotName, ENT_QUOTES, 'UTF-8');

        return '<div class="ad-wrapper">'
            . '<div class="container">'
            . '<div class="ad-container ad-placeholder" data-ad-slot="' . $slotKeyAttr . '">'
            . '<div class="ad-label">Advertisement Space</div>'
            . '<div class="ad-placeholder-content">'
            . '<div class="ad-placeholder-badge">📢 Slot Iklan: ' . $slotTitle . '</div>'
            . '<div class="ad-placeholder-text">Ruang Banner Sponsor / Google AdSense Responsif</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
    }

    /**
     * Wrap ad HTML in a responsive container with label
     */
    private function wrapAdContainer(string $adHtml, string $slotKey): string
    {
        $slotKeyAttr = htmlspecialchars(strtolower($slotKey), ENT_QUOTES, 'UTF-8');
        
        return '<div class="ad-wrapper">'
            . '<div class="container">'
            . '<div class="ad-container" data-ad-slot="' . $slotKeyAttr . '">'
            . '<div class="ad-label">Advertisement</div>'
            . '<div class="ad-content">' . $adHtml . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
    }
}
