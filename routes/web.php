<?php

/**
 * ASR FORM — Multi-SaaS Route Definitions
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
// Protected Routes (Auth Required — All admin types)
// ──────────────────────────────────────────

// Dashboard (all roles)
$router->get('dashboard', 'DashboardController', 'index', ['AuthMiddleware']);

// Forms & Visual Builder (Admin + Super Admin)
$router->get('forms', 'FormController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('forms/create', 'FormController', 'create', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/store', 'FormController', 'store', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('forms/{id}/builder', 'FormController', 'builder', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/save', 'FormController', 'saveFields', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('api/forms/{id}/save', 'FormController', 'saveFields', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/upload-bg', 'FormController', 'uploadBackground', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/delete-bg', 'FormController', 'deleteBackground', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('forms/{id}/responses', 'FormController', 'responses', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('forms/{id}/responses/export', 'FormController', 'exportResponses', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/responses/clear', 'FormController', 'clearResponses', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/responses/{responseId}/delete', 'FormController', 'deleteResponse', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('forms/{id}/delete', 'FormController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);

// Document Templates (Admin + Super Admin)
$router->get('templates', 'TemplateController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/create', 'TemplateController', 'create', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/editor', 'TemplateController', 'editor', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/store-editor', 'TemplateController', 'storeEditor', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/{id}/edit', 'TemplateController', 'edit', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/{id}/update-editor', 'TemplateController', 'updateEditor', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/upload-image', 'TemplateController', 'uploadImage', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/store', 'TemplateController', 'store', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/{id}/mapping', 'TemplateController', 'mapping', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/{id}/mapping', 'TemplateController', 'saveMapping', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/{id}/versions', 'TemplateController', 'versions', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/{id}/versions', 'TemplateController', 'uploadVersion', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/{id}/duplicate', 'TemplateController', 'duplicate', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('templates/{id}/download', 'TemplateController', 'download', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('templates/{id}/delete', 'TemplateController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);

// Generator Surat & Dokumen (Admin + Super Admin)
$router->get('documents', 'DocumentController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('documents/create', 'DocumentController', 'create', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('documents/store', 'DocumentController', 'store', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('documents/{id}/download-docx', 'DocumentController', 'downloadDocx', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('documents/{id}/download-pdf', 'DocumentController', 'downloadPdf', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('documents/{id}/delete', 'DocumentController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);

// Responses (Admin + Super Admin)
$router->get('responses', 'ResponseController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('responses/export', 'ResponseController', 'export', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('responses/clear', 'ResponseController', 'clear', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('responses/{id}/delete', 'ResponseController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('responses/{id}/send-wa', 'ResponseController', 'sendWhatsApp', ['AuthMiddleware', 'RoleMiddleware']);

// ──────────────────────────────────────────
// Admin Routes — Manage Users (Admin + Super Admin)
// ──────────────────────────────────────────

$router->get('users', 'UserController', 'index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('users/create', 'UserController', 'create', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/store', 'UserController', 'store', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('users/{id}/edit', 'UserController', 'edit', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/{id}/update', 'UserController', 'update', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('users/{id}/delete', 'UserController', 'destroy', ['AuthMiddleware', 'RoleMiddleware']);

// Tenant WhatsApp Gateway Settings (Admin + Super Admin)
$router->get('settings/wa', 'AdminController', 'waSettings', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/wa/update', 'AdminController', 'updateWaSettings', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('settings/wa/test', 'AdminController', 'testWa', ['AuthMiddleware', 'RoleMiddleware']);

// ──────────────────────────────────────────
// Super Admin Only Routes
// ──────────────────────────────────────────

// Admin Management (Super Admin manages tenant admins)
$router->get('admins', 'AdminController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('admins/create', 'AdminController', 'create', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/store', 'AdminController', 'store', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('admins/{id}/edit', 'AdminController', 'edit', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/{id}/update', 'AdminController', 'update', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/{id}/delete', 'AdminController', 'destroy', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/{id}/approve', 'AdminController', 'approve', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/{id}/reject', 'AdminController', 'reject', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/{id}/impersonate', 'AdminController', 'impersonate', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('admins/leave-impersonate', 'AdminController', 'leaveImpersonate', ['AuthMiddleware']);
$router->get('admins/leave-impersonate', 'AdminController', 'leaveImpersonate', ['AuthMiddleware']);

// Global Settings (Super Admin only)
$router->get('settings', 'SettingController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/update', 'SettingController', 'update', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/site', 'SettingController', 'site', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/site/update', 'SettingController', 'updateSite', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/pages', 'SettingController', 'pages', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/pages/update', 'SettingController', 'updatePages', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/ads', 'AdsController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/ads/update', 'AdsController', 'update', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/ads/slots/{id}/toggle', 'AdsController', 'toggleSlot', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/payment', 'SettingController', 'payment', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/payment/update', 'SettingController', 'updatePayment', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/gateway', 'GatewayController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/gateway/update', 'GatewayController', 'update', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/gateway/test-wa', 'GatewayController', 'testWhatsApp', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/gateway/test-mail', 'GatewayController', 'testMail', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->get('settings/github', 'WebhookController', 'settings', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/github/update', 'WebhookController', 'updateSettings', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/github/pull', 'WebhookController', 'manualPull', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('settings/github/clear-logs', 'WebhookController', 'clearLogs', ['AuthMiddleware', 'SuperAdminMiddleware']);

// Payments Management (Super Admin only)
$router->get('payments', 'PaymentController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('payments/{id}/verify', 'PaymentController', 'verify', ['AuthMiddleware', 'SuperAdminMiddleware']);
$router->post('payments/{id}/reject', 'PaymentController', 'reject', ['AuthMiddleware', 'SuperAdminMiddleware']);

// Audit Log (Super Admin only)
$router->get('audit-log', 'AuditLogController', 'index', ['AuthMiddleware', 'SuperAdminMiddleware']);

// ──────────────────────────────────────────
// Public Payment & Confirmation (No Auth Required)
// ──────────────────────────────────────────
$router->get('payment/{adminId}', 'PaymentController', 'showCheckout');
$router->post('payment/submit', 'PaymentController', 'submitProof');
$router->get('payment/{adminId}/success', 'PaymentController', 'showSuccess');

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
