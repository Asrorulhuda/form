<?php

/**
 * ASR FORM - Route Definitions
 * 
 * @var \App\Core\Router $router
 */

// ──────────────────────────────────────────
// Public Routes (No Auth Required)
// ──────────────────────────────────────────

// Root - Public Interactive Landing Page
$router->get('', 'HomeController', 'index');

// Auth
$router->get('login', 'AuthController', 'showLogin');
$router->post('login', 'AuthController', 'login');
$router->get('register', 'AuthController', 'showRegister');
$router->post('register', 'AuthController', 'register');
$router->get('logout', 'AuthController', 'logout');

// Public Form (No Auth Required - Google Forms Style)
$router->get('form/{slug}', 'PublicFormController', 'show');
$router->post('form/{slug}/submit', 'PublicFormController', 'submit');
$router->get('form/{slug}/success', 'PublicFormController', 'success');
$router->get('document/{token}', 'PublicFormController', 'viewDocument');
$router->get('document/{token}/download-docx', 'PublicFormController', 'downloadDocx');
$router->get('document/{token}/download-pdf', 'PublicFormController', 'downloadPdf');
$router->get('verify/{token}', 'PublicFormController', 'verify');

// GitHub Webhook Endpoint (No Auth / CSRF Required - Uses HMAC SHA-256)
$router->post('webhook/github', 'WebhookController', 'github');
$router->post('api/webhook/github', 'WebhookController', 'github');

// Web Database Updater (Direct Browser Access for Hosting / Local)
$router->get('update-database', 'HomeController', 'updateDatabase');
$router->get('database/update', 'HomeController', 'updateDatabase');

// ──────────────────────────────────────────
// Protected Routes (Auth Required)
// ──────────────────────────────────────────

// Dashboard
$router->get('dashboard', 'DashboardController', 'index', ['AuthMiddleware']);

// Forms & Visual Builder
$router->get('forms', 'FormController', 'index', ['AuthMiddleware']);
$router->get('forms/create', 'FormController', 'create', ['AuthMiddleware']);
$router->post('forms/store', 'FormController', 'store', ['AuthMiddleware']);
$router->get('forms/{id}/builder', 'FormController', 'builder', ['AuthMiddleware']);
$router->post('forms/{id}/save', 'FormController', 'saveFields', ['AuthMiddleware']);
$router->post('api/forms/{id}/save', 'FormController', 'saveFields', ['AuthMiddleware']);
$router->post('forms/{id}/upload-bg', 'FormController', 'uploadBackground', ['AuthMiddleware']);
$router->post('forms/{id}/delete-bg', 'FormController', 'deleteBackground', ['AuthMiddleware']);
$router->get('forms/{id}/responses', 'FormController', 'responses', ['AuthMiddleware']);
$router->get('forms/{id}/responses/export', 'FormController', 'exportResponses', ['AuthMiddleware']);
$router->post('forms/{id}/responses/clear', 'FormController', 'clearResponses', ['AuthMiddleware']);
$router->post('forms/{id}/responses/{responseId}/delete', 'FormController', 'deleteResponse', ['AuthMiddleware']);
$router->post('forms/{id}/delete', 'FormController', 'destroy', ['AuthMiddleware']);

// Document Templates (Word .DOCX Engine & Professional Letter Editor)
$router->get('templates', 'TemplateController', 'index', ['AuthMiddleware']);
$router->get('templates/create', 'TemplateController', 'create', ['AuthMiddleware']);
$router->get('templates/editor', 'TemplateController', 'editor', ['AuthMiddleware']);
$router->post('templates/store-editor', 'TemplateController', 'storeEditor', ['AuthMiddleware']);
$router->get('templates/{id}/edit', 'TemplateController', 'edit', ['AuthMiddleware']);
$router->post('templates/{id}/update-editor', 'TemplateController', 'updateEditor', ['AuthMiddleware']);
$router->post('templates/upload-image', 'TemplateController', 'uploadImage', ['AuthMiddleware']);
$router->post('templates/store', 'TemplateController', 'store', ['AuthMiddleware']);
$router->get('templates/{id}/mapping', 'TemplateController', 'mapping', ['AuthMiddleware']);
$router->post('templates/{id}/mapping', 'TemplateController', 'saveMapping', ['AuthMiddleware']);
$router->get('templates/{id}/versions', 'TemplateController', 'versions', ['AuthMiddleware']);
$router->post('templates/{id}/versions', 'TemplateController', 'uploadVersion', ['AuthMiddleware']);
$router->post('templates/{id}/duplicate', 'TemplateController', 'duplicate', ['AuthMiddleware']);
$router->get('templates/{id}/download', 'TemplateController', 'download', ['AuthMiddleware']);
$router->post('templates/{id}/delete', 'TemplateController', 'destroy', ['AuthMiddleware']);

// Generator Surat & Dokumen
$router->get('documents', 'DocumentController', 'index', ['AuthMiddleware']);
$router->get('documents/create', 'DocumentController', 'create', ['AuthMiddleware']);
$router->post('documents/store', 'DocumentController', 'store', ['AuthMiddleware']);
$router->get('documents/{id}/download-docx', 'DocumentController', 'downloadDocx', ['AuthMiddleware']);
$router->get('documents/{id}/download-pdf', 'DocumentController', 'downloadPdf', ['AuthMiddleware']);
$router->post('documents/{id}/delete', 'DocumentController', 'destroy', ['AuthMiddleware']);

// Responses
$router->get('responses', 'ResponseController', 'index', ['AuthMiddleware']);
$router->get('responses/export', 'ResponseController', 'export', ['AuthMiddleware']);
$router->post('responses/clear', 'ResponseController', 'clear', ['AuthMiddleware']);
$router->post('responses/{id}/delete', 'ResponseController', 'destroy', ['AuthMiddleware']);
$router->post('responses/{id}/send-wa', 'ResponseController', 'sendWhatsApp', ['AuthMiddleware']);

// ──────────────────────────────────────────
// Admin Routes (Auth + Role Required)
// ──────────────────────────────────────────

// Applicants (Approval Pendaftar Baru)
$router->get('applicants', 'ApplicantController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('applicants/{id}/approve', 'ApplicantController', 'approve', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('applicants/{id}/reject', 'ApplicantController', 'reject', ['AuthMiddleware', 'RoleMiddleware']);

// Users
$router->get('users', 'UserController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('users/create', 'UserController', 'create', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/store', 'UserController', 'store', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('users/{id}/edit', 'UserController', 'edit', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/{id}/update', 'UserController', 'update', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/{id}/delete', 'UserController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);

// Settings
$router->get('settings', 'SettingController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/update', 'SettingController', 'update', ['AuthMiddleware', 'RoleMiddleware']);

// Audit Log
$router->get('audit-log', 'AuditLogController', 'index', ['AuthMiddleware', 'RoleMiddleware']);

// Settings Sub-pages (Site, Pages, Ads, Payment, Gateway)
$router->get('settings/site', 'SettingController', 'site', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/site/update', 'SettingController', 'updateSite', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('settings/pages', 'SettingController', 'pages', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/pages/update', 'SettingController', 'updatePages', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('settings/ads', 'AdsController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/ads/update', 'AdsController', 'update', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/ads/slots/{id}/toggle', 'AdsController', 'toggleSlot', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('settings/payment', 'SettingController', 'payment', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/payment/update', 'SettingController', 'updatePayment', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('settings/gateway', 'GatewayController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/gateway/update', 'GatewayController', 'update', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/gateway/test-wa', 'GatewayController', 'testWhatsApp', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/gateway/test-mail', 'GatewayController', 'testMail', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('settings/github', 'WebhookController', 'settings', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/github/update', 'WebhookController', 'updateSettings', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/github/pull', 'WebhookController', 'manualPull', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/github/clear-logs', 'WebhookController', 'clearLogs', ['AuthMiddleware', 'RoleMiddleware']);

// Admin: Payments Management
$router->get('payments', 'PaymentController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('payments/{id}/verify', 'PaymentController', 'verify', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('payments/{id}/reject', 'PaymentController', 'reject', ['AuthMiddleware', 'RoleMiddleware']);

// ──────────────────────────────────────────
// Public Payment & Confirmation (No Auth Required)
// ──────────────────────────────────────────
$router->get('payment/{userId}', 'PaymentController', 'showCheckout');
$router->post('payment/submit', 'PaymentController', 'submitProof');
$router->get('payment/{userId}/success', 'PaymentController', 'showSuccess');

// ──────────────────────────────────────────
// Public Pages (No Auth Required)
// Must be BEFORE catch-all {slug} route
// ──────────────────────────────────────────
$router->get('features', 'PageController', 'features');
$router->get('about', 'PageController', 'about');
$router->get('contact', 'PageController', 'contact');
$router->post('contact', 'PageController', 'sendContact');
$router->get('pricing', 'PageController', 'pricing');
$router->get('privacy-policy', 'PageController', 'privacyPolicy');
$router->get('terms', 'PageController', 'terms');
$router->get('sitemap.xml', 'PageController', 'sitemap');

// ──────────────────────────────────────────
// Clean Direct Public Form Slug Fallback (e.g. /pendaftaran-guru)
// ──────────────────────────────────────────
$router->get('f/{slug}', 'PublicFormController', 'show');
$router->get('{slug}', 'PublicFormController', 'showDirect');
$router->post('{slug}/submit', 'PublicFormController', 'submit');
$router->get('{slug}/success', 'PublicFormController', 'success');

