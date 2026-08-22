<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

/**
 * Landing Page Controller
 */
class HomeController
{
    public function index(): void
    {
        $db = Database::getInstance();

        // Get live stats for landing page counter
        $totalForms     = (int) $db->fetchColumn("SELECT COUNT(*) FROM forms") ?: 0;
        $totalResponses = (int) $db->fetchColumn("SELECT COUNT(*) FROM form_responses") ?: 0;
        $totalDocuments = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents") ?: 0;

        $settingModel   = new \App\Models\Setting();
        $siteName       = $settingModel->get('site_name', 'ASR FORM');
        $siteDesc       = $settingModel->get('site_description', 'ASR FORM adalah platform untuk membuat formulir digital dan mengubah data menjadi dokumen secara otomatis.');

        View::render('home.index', [
            'title'           => $siteName . ' — ' . $settingModel->get('site_tagline', 'Platform Form Builder & Document Generator'),
            'isLoggedIn'      => Auth::check(),
            'user'            => Auth::user(),
            'totalForms'      => $totalForms,
            'totalResponses'  => $totalResponses,
            'totalDocuments'  => $totalDocuments,
            'siteName'        => $siteName,
            'siteTagline'     => $settingModel->get('site_tagline', 'Platform Form Builder & Document Generator'),
            'siteDesc'        => $siteDesc,
            'contactEmail'    => $settingModel->get('site_contact_email', ''),
            'featuresEnabled' => (int) $settingModel->get('page_features_enabled', '1') === 1,
            'aboutEnabled'    => (int) $settingModel->get('page_about_enabled', '1') === 1,
            'contactEnabled'  => (int) $settingModel->get('page_contact_enabled', '1') === 1,
            'pricingEnabled'  => (int) $settingModel->get('page_pricing_enabled', '1') === 1,
            'privacyEnabled'  => (int) $settingModel->get('page_privacy_enabled', '1') === 1,
            'termsEnabled'    => (int) $settingModel->get('page_terms_enabled', '1') === 1,
            'footerText'      => $settingModel->get('site_footer_text', '© 2026 ASR FORM. All rights reserved.'),
        ], 'public');
    }

    public function updateDatabase(): void
    {
        require_once BASE_PATH . '/public/update_db.php';
        exit;
    }
}
